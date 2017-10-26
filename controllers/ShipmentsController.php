<?php
/**
 * Created by PhpStorm.
 * User: RotelandO
 * Date: 7/25/15
 * Time: 1:06 PM
 */

namespace app\controllers;


use Adapter\AdminAdapter;
use Adapter\BankAdapter;
use Adapter\BillingPlanAdapter;
use Adapter\BranchAdapter;
use Adapter\CodTellerAdapter;
use Adapter\CompanyAdapter;
use Adapter\Globals\HttpStatusCodes;
use Adapter\Globals\ServiceConstant;
use Adapter\ParcelAdapter;
use Adapter\RtdTellerAdapter;
use Adapter\UserAdapter;
use Adapter\RefAdapter;
use Adapter\RegionAdapter;
use Adapter\RequestHelper;
use Adapter\ResponseHandler;
use Adapter\Util\Calypso;
use Adapter\Util\Util;
use app\models\BulkShipmentModel;
use app\services\HubService;
use Adapter\TellerAdapter;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Response;
use Adapter\RouteAdapter;
use yii\web\UploadedFile;

/**
 * Class ShipmentsController
 * @package app\controllers
 */
class ShipmentsController extends BaseController
{
    public $userData = null;
    public $branch_to_view = null;

    public function beforeAction($action)
    {
        $this->userData = (Calypso::getInstance()->session('user_session'));
        $this->branch_to_view = ($this->userData['role_id'] == ServiceConstant::USER_TYPE_SUPER_ADMIN) ? null :
            ($this->userData['role_id'] == ServiceConstant::USER_TYPE_ADMIN) ? null : Calypso::getValue($this->userData, 'branch_id'); //displays all when null
        //print_r($this->userData);
        if (empty($this->userData)) {
            return false;
        }
        return parent::beforeAction($action);
    }


    public function actionAll($page = 1, $search = false, $page_width = null)
    {
        $from_date = date('Y-m-d');
        $to_date = date('Y-m-d');
        $search_action = $search;
        $cash_on_delivery = Yii::$app->request->get('cash_on_delivery', ServiceConstant::FALSE);

        if ($page_width != null) {
            $this->page_width = $page_width;
            Calypso::getInstance()->cookie('page_width', $page_width);
        }

        if(Yii::$app->request->isPost){
            $records = \Yii::$app->request->post();
            if ($records['task'] == 'submit_teller') {
                if (!isset($records['bank_id'], $records['account_no'], $records['amount_paid'], $records['teller_no'], $records['waybill_numbers'])) {
                    $this->flashError("Invalid parameter(s) sent!");
                } else {
                    $teller = new CodTellerAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
                    $teller = $teller->addTeller($records);
                    $response = new ResponseHandler($teller);

                    if ($response->getStatus() == ResponseHandler::STATUS_OK) {
                        $this->flashSuccess('Teller successfully added');
                    } else {
                        $messages = '';
                        $errors = $response->getError();
                        if(is_array($errors)){
                            foreach ($errors as $key => $message) {
                                $messages .= "$key: $message<br/>";
                            }
                        }else $messages = $errors;
                        $this->flashError($messages);
                    }
                }
            }

            if ($records['task'] == 'submit_rtd_teller') {
                if (!isset($records['bank_id'], $records['account_no'], $records['amount_paid'],
                    $records['teller_no'], $records['waybill_numbers'])) {
                    $this->flashError("Invalid parameter(s) sent!");
                } else {
                    $teller = new RtdTellerAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
                    $teller = $teller->addTeller($records);
                    $response = new ResponseHandler($teller);

                    if ($response->getStatus() == ResponseHandler::STATUS_OK) {
                        $this->flashSuccess('Teller successfully added');
                    } else {
                        $messages = '';
                        $errors = $response->getError();
                        if(is_array($errors)){
                            foreach ($errors as $key => $message) {
                                $messages .= "$key: $message<br/>";
                            }
                        }else $messages = $errors;
                        $this->flashError($messages);
                    }
                }
            }

            if($records['task'] == 'update_pod'){

            }
        }

        $page_width = is_null($page_width) ? $this->page_width : $page_width;
        $offset = ($page - 1) * $page_width;
        $parcel = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());


        if (isset(Calypso::getInstance()->get()->from, Calypso::getInstance()->get()->to)) {
            $from_date = Calypso::getInstance()->get()->from;
            $to_date = Calypso::getInstance()->get()->to;
            $filter = null;
            //$filter = Calypso::getInstance()->get()->date_filter;
            $filter = isset(Calypso::getInstance()->get()->date_filter) && Calypso::getInstance()->get()->date_filter != '-1'
                ? Calypso::getInstance()->get()->date_filter : null;
            $response = $parcel->getFilterParcelsByDateAndStatus($from_date . ' 00:00:00', $to_date . ' 23:59:59', $filter, $offset, $this->page_width, 1, null, null, true, $cash_on_delivery);
            $search_action = true;

        } elseif (!empty(Calypso::getInstance()->get()->search)) { //check if not empty criteria
            $search = Calypso::getInstance()->get()->search;
            $response = $parcel->getSearchParcels(null, $search, $offset, $this->page_width, 1, $this->branch_to_view, 1, null);
            $search_action = true;
            $filter = null;

        } else {
            $response = $parcel->getParcels($from_date . ' 00:00:00', $to_date . ' 23:59:59', null, $this->branch_to_view, $offset, $this->page_width, 1, 1, 1, true, $cash_on_delivery);
            //$response = $parcel->getParcels(null,null,$offset,$this->page_width);
            //$response = $parcel->getNewParcelsByDate(date('Y-m-d', strtotime('now')).' 00:00:00',$offset,$this->page_width, 1,$this->userData['branch_id']);
            $search_action = false;
            $filter = null;
        }
        $response = new ResponseHandler($response);
        $data = [];
        $total_count = 0;
        if ($response->getStatus() == ResponseHandler::STATUS_OK) {
            $data = $response->getData();
            $total_count = 0;// $data['total_count'];
            if (isset($data['total_count'])) {
                $total_count = $data['total_count'];
            }
            if (isset($data['parcels'])) {
                $data = $data['parcels'];
                $total_count = $total_count <= 0 ? count($data) : $total_count;
            }
        }

        $refData = new RefAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $banks = $refData->getBanks(); // get all the banks

        return $this->render('all', array('filter' => $filter, 'parcels' => $data, 'from_date' => $from_date, 'to_date' => $to_date, 'offset' => $offset, 'page_width' => $this->page_width, 'search' => $search_action, 'total_count' => $total_count, 'banks' => $banks, 'cash_on_delivery' => $cash_on_delivery));
    }

    public function actionFordelivery($page = 1, $search = false, $page_width = null)
    {
        $from_date = date('Y/m/d');
        $to_date = date('Y/m/d');
        $search_action = $search;
        $parcel = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $page_width = is_null($page_width) ? $this->page_width : $page_width;
        $offset = ($page - 1) * $page_width;
        $route_id = !empty(Calypso::getInstance()->get()->route) ? Calypso::getInstance()->get()->route : null;

        if (\Yii::$app->request->isPost) {
            $rawData = \Yii::$app->request->post('waybills');
            $data = json_decode($rawData, true);
            $waybills = [];
            foreach ($data['waybills'] as $wb) {
                $waybills[] = $wb;
            }
            $record = array(
                'waybill_numbers' => implode(",", $waybills),
                'held_by_id' => Calypso::getValue($data, 'held_by_id', null),
                'enforce_action' => Calypso::getValue($data, 'enforce_action', 0)
            );

            if (!isset($record['waybill_numbers'], $record['held_by_id'])) {
                $this->flashError("Invalid parameter(s) sent!");
            } else {
                $parcelData = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
                $response = $parcelData->moveToBeingDelivered($record);
                $responseHandler = new ResponseHandler($response);
                $data = $responseHandler->getData();

                if ($responseHandler->getStatus() === ResponseHandler::STATUS_OK) {
                    if (empty($data['bad_parcels']))
                        return $this->redirect('/manifest/view?id=' . Calypso::getValue($response, 'data.manifest.id', ''));
                    else {
                        $bad_parcels = $data['bad_parcels'];
                        foreach ($bad_parcels as $key => $bad_parcel) {
                            $this->flashError($key . ' - ' . $bad_parcel);
                        }
                    }
                } else {
                    $this->flashError($responseHandler->getError());
                }
            }
        }
        if (isset(Calypso::getInstance()->get()->from, Calypso::getInstance()->get()->to)) {
            $from_date = Calypso::getInstance()->get()->from . ' 00:00:00';
            $to_date = Calypso::getInstance()->get()->to . ' 23:59:59';
            $filter = isset(Calypso::getInstance()->get()->date_filter) ? Calypso::getInstance()->get()->date_filter : '-1';
            $response = $parcel->getFilterParcelsByDateAndStatus($from_date, $to_date, $filter, $offset, $page_width, 1, $this->branch_to_view);
            $search_action = true;
        } elseif (!empty(Calypso::getInstance()->get()->search)) {
            $search = Calypso::getInstance()->get()->search;
            $response = $parcel->getSearchParcels(null, $search, $offset, $page_width, 1, $this->branch_to_view, 1);
            $search_action = true;
        } else {
            $response = $parcel->getParcelsForDelivery(null, null, ServiceConstant::FOR_DELIVERY, $this->branch_to_view, $offset, $page_width, true, 1, null, $route_id, true);
            $search_action = false;
        }
        $response = new ResponseHandler($response);
        $data = [];
        $total_count = 0;
        if ($response->getStatus() == ResponseHandler::STATUS_OK) {
            $data = $response->getData();
            $total_count = empty($data['total_count']) ? 0 : $data['total_count'];
            $data = empty($data['parcels']) ? 0 : $data['parcels'];
        }

        $routeAdp = new RouteAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $routes = $routeAdp->getRoutes($this->branch_to_view);
        $routes = new ResponseHandler($routes);
        $route_list = $routes->getStatus() == ResponseHandler::STATUS_OK ? $routes->getData() : [];
        $reasons_list = $parcel->getParcelReturnReasons();
        return $this->render('fordelivery', array('parcels' => $data, 'from_date' => $from_date, 'to_date' => $to_date, 'offset' => $offset, 'page_width' => $page_width, 'search' => $search_action, 'total_count' => $total_count, 'routes' => $route_list, 'reasons_list' => $reasons_list, 'route_id' => $route_id));
    }

    public function actionStaffcheck()
    {
        $this->enableCsrfValidation = false;

        $data = (Yii::$app->request->get());
        if ($data) {
            $admin = new AdminAdapter();
            $response = $admin->login($data['staff_id'], $data['password']);
            $response = new ResponseHandler($response);
            if ($response->getStatus() == ResponseHandler::STATUS_OK) {
                $data = $response->getData();
                if ($data['role_id'] == ServiceConstant::USER_TYPE_DISPATCHER) {
                    return $this->sendSuccessResponse($data['role_id']);
                } else {
                    return $this->sendErrorResponse('Access denied');
                }
            } else {
                return $this->sendErrorResponse('Invalid details', null);
            }
        }
    }

    public function actionForsweep($page = 1, $search = false, $page_width = null)
    {

        //Move to In Transit (waybill_numbers, to_branch_id.
        //and staff_id (not the code)
        if (\Yii::$app->request->isPost) {
            $rawData = \Yii::$app->request->post('payload');
            $data = json_decode($rawData, true);
            $service = new HubService();
            $payloadData = $service->buildPostData($data);
            if (!isset($payloadData['waybill_numbers'], $payloadData['to_branch_id'], $payloadData['held_by_id'])) {
                $this->flashError("Invalid parameter(s) sent!");
            } else {
                $parcelData = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
                $response = $parcelData->generateManifest($payloadData);
                if ($response['status'] === ResponseHandler::STATUS_OK) {
                    //Forward to manifest page
                    return $this->redirect('/manifest/view?id=' . Calypso::getValue($response, 'data.manifest.id', ''));
                } else {
                    //Flash error message
                    $this->flashError($response['message']);
                }
            }
        }

        $search_action = $search;
        $page_width = is_null($page_width) ? $this->page_width : $page_width;
        $offset = ($page - 1) * $page_width;
        $parcel = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());

        $branchData = new BranchAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $branch = $branchData->getOneHub($this->userData['branch_id']);

        if (!empty(Calypso::getInstance()->get()->search)) {  //check if not empty criteria
            $search = Calypso::getInstance()->get()->search;
            $response = $parcel->getSearchParcels(null, $search, $offset, $page_width, 1, $this->branch_to_view, 1);
            $search_action = $search;
        } else {
            $response = $parcel->getParcels(null, null, ServiceConstant::FOR_SWEEPER, $this->branch_to_view, $offset, $page_width, 1, 1, null, true);
            $search_action = false;
        }
        $response = new ResponseHandler($response);
        $data = [];
        $total_count = 0;
        if ($response->getStatus() == ResponseHandler::STATUS_OK) {
            $data = $response->getData();
            $total_count = empty($data['total_count']) ? 0 : $data['total_count'];
            $data = empty($data['parcels']) ? 0 : $data['parcels'];
        }
        return $this->render('forsweep', array('branch' => $branch['data'], 'parcels' => $data, 'offset' => $offset, 'page_width' => $page_width, 'search' => $search_action, 'total_count' => $total_count));
    }

    /**
     * This is a method to render the view for generating manifest
     * @param $data
     * @return string
     */
    public function viewManifest($data)
    {

        $user_session = Calypso::getInstance()->session("user_session");
        $parcelsAdapter = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $in_transit_parcels = $parcelsAdapter->getParcelsForNextDestination(ServiceConstant::IN_TRANSIT, $user_session['branch_id'], $data['to_branch_id'], $data['held_by_id']);
        if ($in_transit_parcels['status'] === ResponseHandler::STATUS_OK) {
            $viewData['parcel_delivery'] = $in_transit_parcels['data'];

            $adminData = new AdminAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
            $staff = $adminData->getStaff($data['staff_code']);
            if ($staff['status'] === ResponseHandler::STATUS_OK) {
                $viewData['staff'] = $staff['data'];
            } else {
                $viewData['staff'] = [];
            }

        } else {
            $this->flashError('An error occured while trying to fetch parcels. Please try again.');
            $viewData['parcel_delivery'] = [];
        }

        return $this->render('/hubs/manifest', $viewData);
    }

    public function actionProcessed($page = 1, $search = false, $page_width = null)
    {
        $from_date = date('Y-m-d');
        $to_date = date('Y-m-d');
        $search_action = $search;
        if ($page_width != null) {
            $this->page_width = $page_width;
            Calypso::getInstance()->cookie('page_width', $page_width);
        }
        $offset = ($page - 1) * $page_width;

        $parcel = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        if (\Yii::$app->request->isPost) {
            $records = \Yii::$app->request->post();
            if ($records['task'] == 'cancel_shipment') {
                $response = $parcel->cancel($records);
                $response = new ResponseHandler($response);

                if ($response->getStatus() == ResponseHandler::STATUS_OK) {
                    $this->flashSuccess('Shipment successfully marked as CANCELLED');
                } else {
                    $this->flashError('An error occurred while trying to cancel shipment. #' . $response->getError());
                }
            } elseif ($records['task'] == 'submit_teller') {
                if (!isset($records['bank_id'], $records['account_no'], $records['amount_paid'], $records['teller_no'], $records['waybill_numbers'])) {
                    $this->flashError("Invalid parameter(s) sent!");
                } else {
                    $teller = new TellerAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
                    $teller = $teller->addTeller($records);
                    $response = new ResponseHandler($teller);

                    if ($response->getStatus() == ResponseHandler::STATUS_OK) {
                        $this->flashSuccess('Teller successfully added');
                    } else {
                        $messages = '';
                        $errors = $response->getError();
                        if(is_array($errors)){
                            foreach ($errors as $key => $message) {
                                $messages .= "$key: $message<br/>";
                            }
                        }else $messages = $errors;
                        $this->flashError($messages);
                    }
                }
            }
        }

        if (isset(Calypso::getInstance()->get()->from, Calypso::getInstance()->get()->to)) {
            $from_date = Calypso::getInstance()->get()->from;
            $to_date = Calypso::getInstance()->get()->to;
            $filter = null;
            $response = $parcel->getFilterParcelsByDateAndStatus($from_date . ' 00:00:00', $to_date . ' 23:59:59', $filter, $offset, $this->page_width, 1, $this->branch_to_view, null, true);
            $search_action = true;

        } elseif (!empty(Calypso::getInstance()->get()->search)) {  //check if not empty criteria
            $search = Calypso::getInstance()->get()->search;
            $response = $parcel->getSearchParcels(null, $search, $offset, $this->page_width, 1, $this->branch_to_view, 1, null);
            $search_action = true;
            $filter = null;

        } else {
            //$response = $parcel->getNewParcelsByDate(date('Y-m-d'),$offset,$this->page_width, 1, $this->branch_to_view, 1);
            $response = $parcel->getNewParcelsByDate(date('Y-m-d 00:00:00', strtotime('now')), $offset, $this->page_width, 1, $this->branch_to_view, 1);
            $search_action = false;
            $filter = null;
        }
        $response = new ResponseHandler($response);
        $data = [];
        $total_count = 0;
        if ($response->getStatus() == ResponseHandler::STATUS_OK) {
            $data = $response->getData();
            $total_count = empty($data['total_count']) ? 0 : $data['total_count'];
            $data = empty($data['parcels']) ? 0 : $data['parcels'];
        }
        $refData = new RefAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $banks = $refData->getBanks(); // get all the banks
        $reasons_list = $parcel->getParcelReturnReasons(); // get all reason


        return $this->render('processed', array('reasons_list' => $reasons_list, 'filter' => $filter, 'parcels' => $data, 'from_date' => $from_date, 'to_date' => $to_date, 'offset' => $offset, 'page_width' => $this->page_width, 'search' => $search_action, 'total_count' => $total_count, 'banks' => $banks));
    }

    public function actionRequestreturn()
    {
        $parcel = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        if (\Yii::$app->request->isPost) {
            $records = \Yii::$app->request->post();
            if ($records['task'] == 'request_return') {
                if (!isset($records['waybill_numbers']) && !isset($records['comment'])) {
                    $this->flashError("Invalid parameter(s) sent!");
                } else {
                    $result = $parcel->sendReturnRequest($records['waybill_numbers'], $records['comment'], $records['attempted_delivery'], $records['extra_note']);
                    $response = new ResponseHandler($result);

                    if ($response->getStatus() == ResponseHandler::STATUS_OK) {
                        $data = $response->getData();
                        if (empty($data['bad_parcels']))
                            $this->flashSuccess('Negative status added');
                        else {
                            $bad_parcels = $data['bad_parcels'];
                            foreach ($bad_parcels as $key => $bad_parcel) {
                                $this->flashError($key . ' - ' . $bad_parcel);
                            }
                        }
                    } else {
                        $this->flashError('An error occurred while trying to send request. #' . $response->getError());
                    }
                }
            }

        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionRemovenegativestatus(){
        $parcel = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $waybill_number = Yii::$app->request->get('waybill_number');
        if(empty($waybill_number)){
            $this->flashError('Waybill number is required');
        }else{
            $result = $parcel->removeNegativeStatus($waybill_number);
            $response = new ResponseHandler($result);

            if ($response->getStatus() == ResponseHandler::STATUS_OK) {
                $data = $response->getData();
                if (empty($data['bad_parcels']))
                    $this->flashSuccess('Negative status removed');
                else {
                    $bad_parcels = $data['bad_parcels'];
                    foreach ($bad_parcels as $key => $bad_parcel) {
                        $this->flashError($key . ' - ' . $bad_parcel);
                    }
                }
            }
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionCancel()
    {

        $rawBody = \Yii::$app->request->getRawBody();
        $payload = json_decode($rawBody, true);
        $parcel = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $response = $parcel->cancel($payload);
        $response = new ResponseHandler($response);

        if ($response->getStatus() == ResponseHandler::STATUS_OK) {
            return $this->sendSuccessResponse('Shipment successfully marked as CANCELLED');

        } else {
            $errorMessage = 'An error occurred while trying to cancel shipment. #' . $response->getError();
            return $this->sendErrorResponse($errorMessage, HttpStatusCodes::HTTP_200);
        }
    }

    public function actionCustomerhistory()
    {
        return $this->render('customer_history');
    }

    public function actionCustomerhistorydetails($page = 1, $search = false)
    {
        $page_width = $this->page_width;
        $offset = ($page - 1) * $page_width;
        $from_date = date('Y-m-d', 0) . '%2000:00:00';
        $to_date = date('Y-m-d') . '%2023:59:59';
        if (!$search) { //default, empty
            // display empty message
            $this->redirect('customerhistory');
        }
        $user = [];
        $data = [];
        $parcels = [];
        $total_count = 0;

        $userAdapter = new UserAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $userResponse = new ResponseHandler($userAdapter->getUserDetails($search));

        if ($userResponse->getStatus() == ResponseHandler::STATUS_OK) {
            $user = $userResponse->getData();
            $user_id = $user['id'];
            $parcelAdapter = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
            $parcelResponse = new ResponseHandler($parcelAdapter->getParcelsByUser($user_id, $from_date, $to_date, $offset, $page_width, 1));
            if ($parcelResponse->getStatus() == ResponseHandler::STATUS_OK) {
                $data = $parcelResponse->getData();
                $parcels = $data['parcels'];
                $total_count = $data['total_count'];
            }
        }
        return $this->render('customer_history_details', array('user' => $user, 'parcels' => $parcels, 'total_count' => $total_count, 'search' => $search, 'offset' => $offset, 'page_width' => $page_width));
    }

    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @author Olajide Oye <jide@cottacush.com>
     * @author Olawale Lawal <wale@cottacush.com>
     * @return string
     */
    public function actionView()
    {
        $data = [];
        $sender_location = [];
        $receiver_location = [];
        $sender_merchant = [];
        $histories = [];

        if (isset(Calypso::getInstance()->get()->waybill_number)) {
            $waybill_number = trim(Calypso::getInstance()->get()->waybill_number);
            if (ParcelAdapter::isBag($waybill_number)) {
                return $this->redirect(Url::toRoute('/shipments/viewbag?waybill_number=' . $waybill_number));
            }
            $refData = new RefAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());

            $serviceType = $refData->getShipmentType();
            $parcelType = $refData->getparcelType();
            $deliveryType = $refData->getdeliveryType();


            $parcel = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
            $response = $parcel->getParcelByWayBillNumber($waybill_number);
            $response = new ResponseHandler($response);
            if ($response->getStatus() == ResponseHandler::STATUS_OK) {
                $data = $response->getData();
                if (isset($data['sender_address']) && isset($data['sender_address']['city_id'])) {
                    $city_id = $data['sender_address']['city_id'];
                    $regionAdp = new RegionAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
                    $sender_location = $regionAdp->getCity($city_id);
                }
                if (isset($data['receiver_address']) && isset($data['receiver_address']['city_id'])) {
                    $city_id = $data['receiver_address']['city_id'];
                    $regionAdp = new RegionAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
                    $receiver_location = $regionAdp->getCity($city_id);
                }
                $bankAdapter = new BankAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
                $bankInfo = $bankAdapter->getSenderBankAccout($data['sender']['id']);
                if ($bankInfo['status'] === ResponseHandler::STATUS_OK) {
                    if (!empty($bankInfo['data'])) {
                        $sender_merchant = $bankInfo['data']['0'];
                    }
                }

                //get histories
                $response = $parcel->getParcelHistories($waybill_number);
                if($response['status'] == ResponseHandler::STATUS_OK){
                    $histories = $response['data']['history'];
                }
            }
        }
        $user_session = Calypso::getInstance()->session("user_session");

        return $this->render('view', array(
            'parcelData' => $data,
            'sessionData' => $user_session,
            'serviceType' => $serviceType,
            'parcelType' => $parcelType,
            'deliveryType' => $deliveryType,
            'senderLocation' => $sender_location,
            'receiverLocation' => $receiver_location,
            'senderMerchant' => $sender_merchant,
            'histories' => $histories
        ));
    }

    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @return string
     */
    public function actionViewbag()
    {
        $waybill_number = Calypso::getInstance()->get()->waybill_number;
        $print = Yii::$app->request->getQueryParam('print');

        if (!isset($waybill_number)) {
            Yii::$app->session->setFlash('danger', 'Could not fetch bag details');
            return $this->render('view_bag', ['waybill_number' => '']);
        }

        if (!ParcelAdapter::isBag($waybill_number)) {
            Yii::$app->session->setFlash('danger', 'Invalid bag number. Please try again');
            return $this->render('view_bag', ['waybill_number' => $waybill_number]);
        }


        $parcelAdapter = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $bag = $parcelAdapter->getBag($waybill_number);
        if (is_string($bag)) {
            Yii::$app->session->setFlash('danger', $bag);
        }
        $view = (is_null($print)) ? 'view_bag' : 'print_bag';
        return $this->render($view, ['waybill_number' => $waybill_number, 'bag' => $bag]);
    }

    public function actionDispatched($page = 1, $page_width = null)
    {
        $todays_date = Util::getToday();
        $page_width = is_null($page_width) ? $this->page_width : $page_width;
        $offset = ($page - 1) * $page_width;
        $search = null;



        if (isset(\Yii::$app->request->post()['password']) || !empty(\Yii::$app->request->post()['search'])) {

            $records = \Yii::$app->request->post();


            if (!empty($records['search'])) {
                $search = $records['search'];
                $offset = 0;
            } else {
                $password = $records['password'];
                $fullName = $records['fullname'];
                $email = $records['email'];
                $date = $records['date']; // date('Y-m-d H:i:s');
                $time = $records['time']; // date('H:i:s');
                $dateAndTimeTimeStamp = Util::getDateTimeFormatFromDateTimeFields($date, $time);
                $phoneNumber = $records['phone'];
                $rawData = $records['waybills'];
                $enforce_action = Calypso::getValue($records, 'enforce_action', 0);
                $task = $records['task'];


                if (Util::mempty($rawData, $password, $task)) {
                    $this->flashError("Invalid parameter(s) sent!");
                } else {
                    $admin = new AdminAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
                    $temp = $admin->revalidate(null, $password);
                    $temp = new ResponseHandler($temp);
                    if ($temp->getStatus() == ResponseHandler::STATUS_OK) {
                        $data = json_decode($rawData, true);
                        $waybills = [];
                        foreach ($data as $wb) {
                            $waybills[] = $wb;
                        }
                        $record = [];
                        $record['waybill_numbers'] = implode(",", $waybills);
                        $record['enforce_action'] = $enforce_action;

                        $parcelData = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
                        $success_msg = '';

                        if ($task == 'receive') {
                            $response = $parcelData->receiveFromBeingDelivered($record);
                            $success_msg = 'Shipments successfully received';
                        } elseif ($task == 'deliver') {
                            $record['receiver_name'] = $fullName;
                            $record['receiver_phone_number'] = $phoneNumber;
                            $record['receiver_email'] = $email;
                            $record['date_and_time_of_delivery'] = $dateAndTimeTimeStamp;
                            $enforce_action = Calypso::getValue($records, 'enforce_action', 0);
                            $record['enforce_action'] = $enforce_action;
                            $response = $parcelData->moveToDelivered($record);
                            $success_msg = 'Shipments successfully delivered';
                        } elseif ($task == 'return') {
                            $record['receiver_name'] = $fullName;
                            $record['receiver_phone_number'] = $phoneNumber;
                            $record['receiver_email'] = $email;
                            $enforce_action = Calypso::getValue($records, 'enforce_action', 0);
                            $record['enforce_action'] = $enforce_action;
                            $record['date_and_time_of_delivery'] = $dateAndTimeTimeStamp;
                            $response = $parcelData->markAsReturned($record);
                            $success_msg = 'Shipments successfully returned';
                        }
                        $responseHandler = new ResponseHandler($response);
                        $data = $responseHandler->getData();
                        if ($responseHandler->getStatus() === ResponseHandler::STATUS_OK) {
                            if (empty($data['bad_parcels']))
                                $this->flashSuccess($success_msg);
                            else {
                                $bad_parcels = $data['bad_parcels'];
                                foreach ($bad_parcels as $key => $bad_parcel) {
                                    $this->flashError($key . ' - ' . $bad_parcel);
                                }
                            }
                        } else {
                            $this->flashError($responseHandler->getError());
                        }
                    } else {
                        $this->flashError($temp->getError());
                    }
                }


                return $this->redirect('/shipments/dispatched?page=' . $page);
            }
        }


        /** @author Ademu antohny filtering */
        $filter_params = ['for_return', 'shipping_type', 'start_created_date', 'end_created_date', 'dispatcher'];
        $extra_details = ['with_holder'];


        $filters = [];
        foreach ($filter_params as $param) {
            $filters[$param] = Yii::$app->request->get($param);
        }

        foreach ($extra_details as $extra) {
            $filters[$extra] = true;
        }

        $start_created_date = Yii::$app->request->get('start_created_date', Util::getToday('/'));
        $end_created_date = Yii::$app->request->get('end_created_date', Util::getToday('/'));

        $filters['start_created_date'] = $start_created_date . ' 00:00:00';
        $filters['end_created_date'] = $end_created_date . ' 23:59:59';

        $shipping_types = ServiceConstant::getShippingTypes();

        $filters = array_merge($filters, (Calypso::userIsInRole(ServiceConstant::USER_TYPE_ADMIN) ||
            Calypso::userIsInRole(ServiceConstant::USER_TYPE_OFFICER)) && !empty($search)?
            array(
                'to_branch_id' => null,
                'with_total_count' => 1,
                'status' => null,
                'waybill_number' => $search,
                'with_receiver' => 1,
                //'with_holder' => 1,
                'with_to_branch' => 1,
                'with_created_branch' => 1,
                'with_parcel_comment' => 1,
                'offset' => $offset,
                'count' => $page_width
            ) :
            array(
                'to_branch_id' => $this->branch_to_view,
                'with_total_count' => 1,
                'status' => ServiceConstant::BEING_DELIVERED,
                'waybill_number' => $search,
                'with_receiver' => 1,
                'with_holder' => 1,
                'with_to_branch' => 1,
                'with_created_branch' => 1,
                'with_parcel_comment' => 1,
                'offset' => $offset,
                'count' => $page_width
            ));

        $user_session = Calypso::getInstance()->session("user_session");
        $parcelsAdapter = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        if($search)
        $dispatch_parcels = $parcelsAdapter->getECDispatchedParcels($this->branch_to_view, $offset, $page_width, $search);
        else $dispatch_parcels = $parcelsAdapter->getParcelsByFilters($filters);
        $reasons_list = $parcelsAdapter->getParcelReturnReasons();
        $parcels = new ResponseHandler($dispatch_parcels);
        $total_count = 0;
        if ($parcels->getStatus() == ResponseHandler::STATUS_OK) {
            $data = $parcels->getData();
            $parcels = $data['parcels'];
            $total_count = $data['total_count'];
        }


        return $this->render('dispatched', array('reasons_list' => $reasons_list, 'todays_date' => $todays_date, 'parcels' => $parcels, 'total_count' => $total_count, 'offset' => $offset, 'page_width' => $page_width, 'filters' => $filters, 'shipping_types' => $shipping_types));
    }

    public function actionDelivered($page = 1, $page_width = null)
    {
        $page_width = is_null($page_width) ? $this->page_width : $page_width;
        $offset = ($page - 1) * $page_width;

        $from_date = date('Y-m-d');
        $to_date = date('Y-m-d');

        if (isset(Calypso::getInstance()->get()->from, Calypso::getInstance()->get()->to)) {
            $from_date = Calypso::getInstance()->get()->from;
            $to_date = Calypso::getInstance()->get()->to;
        }

        $parcelsAdapter = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $delivered_parcels = $parcelsAdapter->getDeliveredParcels($this->branch_to_view, $offset, $page_width, $from_date . ' 00:00:00', $to_date . ' 23:59:59');
        $parcelsHandler = new ResponseHandler($delivered_parcels);
        $total_count = 0;
        $parcels = [];
        if ($parcelsHandler->getStatus() == ResponseHandler::STATUS_OK) {
            $data = $parcelsHandler->getData();
            $parcels = $data['parcels'];
            $total_count = $data['total_count'];
        }
        return $this->render('delivered', array('parcels' => $parcels, 'total_count' => $total_count, 'offset' => $offset, 'page_width' => $page_width, 'from_date' => $from_date, 'to_date' => $to_date));
    }

    /**
     * @author Olawale Lawal <wale@cottacush.com>
     * @param int $page
     * @param null $page_width
     * @return string
     */
    public function actionReturned($page = 1, $page_width = null)
    {
        $page_width = is_null($page_width) ? $this->page_width : $page_width;
        $offset = ($page - 1) * $page_width;

        $from_date = Yii::$app->request->get('from', date('Y/m/d'));
        $to_date = Yii::$app->request->get('to', date('Y/m/d'));

        $filter_params = ['for_return' => 1, 'status' => ServiceConstant::RETURNED, 'start_modified_date' => $from_date . ' 00:00:00', 'end_modified_date' => $to_date . ' 23:59:59', 'to_branch_id' => $this->branch_to_view, 'offset' => $offset, 'count' => $page_width, 'with_total_count' => true, 'with_sender' => true, 'with_receiver' => true];

        if (isset(Calypso::getInstance()->get()->search)) {
            $filter_params['waybill_number'] = Calypso::getInstance()->get()->search;
            $filter_params['start_modified_date'] = null;
            $filter_params['end_modified_date'] = null;
        }

        $filter_params = array_filter($filter_params, 'strlen');

        $parcelsAdapter = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $delivered_parcels = $parcelsAdapter->getParcelsByFilters($filter_params);
        $parcelsHandler = new ResponseHandler($delivered_parcels);
        $total_count = 0;
        $parcels = [];
        if ($parcelsHandler->getStatus() == ResponseHandler::STATUS_OK) {
            $data = $parcelsHandler->getData();
            $parcels = $data['parcels'];
            $total_count = $data['total_count'];
        }
        return $this->render('returned', array('parcels' => $parcels, 'total_count' => $total_count, 'offset' => $offset, 'page_width' => $page_width, 'from_date' => $from_date, 'to_date' => $to_date));
    }

    /**
     * This action opens a parcel of type bag.
     * @author Akintewe Rotimi <akintewe.rotimi@gmail.com>
     */
    public function actionOpenbag()
    {

        $waybill_number = Calypso::getInstance()->get()->waybill_number;
        if (!$waybill_number) {
            $this->flashError('Please ensure that the correct bag item is selected');
            return $this->redirect('/shipments/view_bag?waybill_number=' . $waybill_number);
        }

        $unbag_referrer = Calypso::getInstance()->getUnbagReferrer();

        $parcel = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $response = $parcel->openBag(['waybill_number' => $waybill_number]);
        $response = new ResponseHandler($response);

        if ($response->getStatus() == ResponseHandler::STATUS_OK) {
            $this->flashSuccess("Bag with waybill number [ $waybill_number ] has been successfully opened.");
            return $this->redirect($unbag_referrer);
        } else {
            $errorMessage = 'An error occurred while trying to open bag. #' . $response->getError();
            $this->flashError($errorMessage);
            return $this->redirect('/shipments/view_bag?waybill_number=' . $waybill_number);
        }
    }

    /**
     * This action remove one or more items (parcels) from a bag.
     * @author Akintewe Rotimi <akintewe.rotimi@gmail.com>
     */
    public function actionRemovefrombag()
    {

        $rawBody = \Yii::$app->request->getRawBody();
        $payload = json_decode($rawBody, true);

        if (!isset($payload['id'], $payload['linked_waybills'])) {
            return $this->sendErrorResponse('There is a problem with the data sent. Please try again.', HttpStatusCodes::HTTP_200);
        }
        $postData['bag_waybill_number'] = $payload['id'];
        $postData['parcel_waybill_number_list'] = implode(',', $payload['linked_waybills']);

        $parcel = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $response = $parcel->removeFromBag($postData);
        $response = new ResponseHandler($response);

        if ($response->getStatus() == ResponseHandler::STATUS_OK) {

            $message = "Shipment(s) ({$postData['parcel_waybill_number_list']}) has been successfully removed from bag #{$postData['bag_waybill_number']}";
            $this->flashSuccess($message);
            return $this->sendSuccessResponse('');
        } else {

            $errorMessage = 'An error occurred while trying to remove shipment from bag. #' . $response->getError();
            return $this->sendErrorResponse($errorMessage, HttpStatusCodes::HTTP_200);
        }
    }

    public function actionReceivefromdispatcher()
    {
        if (isset(Calypso::getInstance()->post()->waybill_numbers)) {
            $parcel = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
            $response = $parcel->receiveFromBeingDelivered([
                'held_by_id' => Calypso::getInstance()->post()->held_by_id,
                'waybill_numbers' => (Calypso::getInstance()->post()->waybill_numbers)
            ]);
            $response = new ResponseHandler($response);
            if ($response->getStatus() == ResponseHandler::STATUS_OK) {
                return $this->sendSuccessResponse($response->getData());
            } else {
                return $this->sendErrorResponse($response->getError(), null);
            }
        } else {
            return $this->sendErrorResponse("Invalid data", null);
        }
    }

    public function actionGetparcels()
    {
        $staff_no = \Yii::$app->request->get('staff_no');
        $session_data = Calypso::getInstance()->session('user_session');
        $branch_id = $session_data['branch']['id'];
        $status = \Yii::$app->request->get('status', ServiceConstant::IN_TRANSIT);

        if (!isset($staff_no)) {
            return $this->sendErrorResponse("Invalid parameter(s) sent!", null);
        }
        $parcel = new  ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $response = $parcel->getParcel($staff_no, $status, $branch_id, true);

        if ($response['status'] === ResponseHandler::STATUS_OK) {
            return $this->sendSuccessResponse($response['data']);
        } else {
            return $this->sendErrorResponse($response['message'], null);
        }
    }

    /**
     * Prepares the report based based on filters
     * @param int $page
     * @param null $page_width
     * @return string
     */
    public function actionReport($page = 1, $page_width = null)
    {

        $page_width = is_null($page_width) ? $this->page_width : $page_width;
        $offset = ($page - 1) * $page_width;

        $filter_params = ['company_id', 'start_pickup_date', 'end_pickup_date', 'start_modified_date', 'end_modified_date', 'for_return', 'parcel_type',
            'status', 'min_weight', 'max_weight', 'min_amount_due', 'max_amount_due', 'cash_on_delivery', 'delivery_type',
            'payment_type', 'shipping_type', 'start_created_date', 'end_created_date', 'created_branch_id', 'route_id', 'request_type',
            'from_branch_id', 'branch_type', 'return_reason_comment', 'business_manager_staff_id',
            'delivery_branch_id', 'with_sales_teller', 'with_cod_teller', 'no_cod_teller'];
        $extra_details = ['with_receiver', 'with_receiver_address', 'with_route'];


        $filters = [];
        foreach ($filter_params as $param) {
            $filters[$param] = trim(Yii::$app->request->get($param));
        }

        foreach ($extra_details as $extra) {
            $filters[$extra] = true;
        }

        $start_modified_date = Yii::$app->request->get('start_modified_date', null);
        $end_modified_date = Yii::$app->request->get('end_modified_date', null);
        $filters['start_modified_date'] = (Util::checkEmpty($start_modified_date)) ? null : $start_modified_date . ' 00:00:00';
        $filters['end_modified_date'] = (Util::checkEmpty($end_modified_date)) ? null : $end_modified_date . ' 23:59:59';

        $start_pickup_date = Yii::$app->request->get('start_pickup_date', null);
        $end_pickup_date = Yii::$app->request->get('end_pickup_date', null);
        $filters['start_pickup_date'] = (Util::checkEmpty($start_pickup_date)) ? null : $start_pickup_date . ' 00:00:00';
        $filters['end_pickup_date'] = (Util::checkEmpty($end_pickup_date)) ? null : $end_pickup_date . ' 23:59:59';


        $start_created_date = Yii::$app->request->get('start_created_date', Util::getToday('/'));
        $end_created_date = Yii::$app->request->get('end_created_date', Util::getToday('/'));

        $filters['start_created_date'] = $start_created_date . ' 00:00:00';
        $filters['end_created_date'] = $end_created_date . ' 23:59:59';

        $filters['offset'] = $offset;
        $filters['count'] = $page_width;
        $filters['with_total_count'] = true;
        $filters['report'] = 1;
        $filters['show_both_parent_and_splits'] = 1;

        $status = ServiceConstant::getStatusRef();
        $payment_methods = ServiceConstant::getPaymentMethods();
        $request_types = ServiceConstant::getRequestTypes();
        $shipping_types = ServiceConstant::getShippingTypes();
        $delivery_types = ServiceConstant::getDeliveryTypes();

        $branch_adapter = new BranchAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $branches = Calypso::getValue($branch_adapter->getAll(), 'data', []);
        if (!$branches) {
            $branches = [];
        }
        $ecs = $branch_adapter->getAllEcs();
        $hubs = Calypso::getValue($branch_adapter->getAllHubs(), 'data', []);

        $route_adapter = new RouteAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $routes = new ResponseHandler($route_adapter->getRoutes(null, null, null, null, null));

        $parcelAdapter = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $filtered_parcels = $parcelAdapter->getParcelsByFilters(array_filter($filters, 'strlen'));

        $response = new ResponseHandler($filtered_parcels);
        $return_reasons = $parcelAdapter->getParcelReturnReasons();


        $parcels = [];
        $total_count = 0;
        if ($response->getStatus() == ResponseHandler::STATUS_OK) {
            $data = $response->getData();
            $parcels = $data['parcels'];
            $total_count = $data['total_count'];
        } else {
            $this->flashError('Could not load reports');
        }

        //dd($routes);

        $companies = (new CompanyAdapter())->getAllCompanies([]);


        return $this->render('report', array(
            'parcels' => $parcels,
            'branches' => $branches,
            'ecs' => $ecs,
            'hubs' => $hubs,
            'routes' => $routes->getData(),
            'statuses' => $status,
            'payment_methods' => $payment_methods,
            'request_types' => $request_types,
            'shipping_types' => $shipping_types,
            'delivery_types' => $delivery_types,
            'filters' => $filters,
            'start_modified_date' => $start_modified_date,
            'end_modified_date' => $end_modified_date,
            'start_pickup_date' => $start_pickup_date,
            'end_pickup_date' => $end_pickup_date,
            'start_created_date' => $start_created_date,
            'end_created_date' => $end_created_date,
            'offset' => $offset,
            'page_width' => $page_width,
            'total_count' => $total_count,
            'companies' => $companies,
            'selected_company' => $filters['company_id'],
            'return_reasons' => $return_reasons,
            'selected_return_reason' => $filters['return_reason_comment'],
            'business_manager_staff_id' => isset($filters['business_manager_staff_id'])?$filters['business_manager_staff_id']:''
        ));
    }

    /**
     * Download parcels report
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     */
    public function actionDownloadreport()
    {
        set_time_limit(0);
        $extra_details = ['with_to_branch', 'with_created_by', 'with_from_branch', 'with_sender',
            'with_sender_address', 'with_receiver', 'with_receiver_address', 'with_bank_account',
            'with_created_branch', 'with_route', 'with_created_by', 'with_company'];

        $filters = Yii::$app->request->get();

        $filters['with_sales_teller'] = ServiceConstant::TRUE;
        $filters['with_cod_teller'] = ServiceConstant::TRUE;
        $filters['with_rtd_teller'] = ServiceConstant::TRUE;
        $filters['with_invoice_parcel'] = ServiceConstant::TRUE;

        foreach ($extra_details as $extra) {
            $filters[$extra] = true;
        }

        $from_date = Yii::$app->request->get('start_created_date', date('Y/m/d'));
        $end_date = Yii::$app->request->get('end_created_date', date('Y/m/d'));
        $filters['start_created_date'] = $from_date . ' 00:00:00';
        $filters['end_created_date'] = $end_date . ' 23:59:59';


        $start_modified_date = Yii::$app->request->get('start_modified_date', null);
        $end_modified_date = Yii::$app->request->get('end_modified_date', null);

        $filters['start_modified_date'] = (Util::checkEmpty($start_modified_date) ? null : $start_modified_date . ' 00:00:00');
        $filters['end_modified_date'] = (Util::checkEmpty($end_modified_date) ? null : $end_modified_date . ' 23:59:59');

        $parcel = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());


        $filters['report'] = 1;
        $filters['with_total_count'] = true;
        $filters['show_both_parent_and_splits'] = 1;

        $filters['no_cod_teller'] = Yii::$app->request->get('no_cod_teller');

        $offset = 0;
        $count = 500;

        $filters['count'] = $count;
        $filters['offset'] = $offset;
        $filtered_parcels = $parcel->getParcelsByFilters(array_filter($filters, 'strlen'));
        $response = new ResponseHandler($filtered_parcels);
        //dd($response);

        $name = 'report_' . date(ServiceConstant::DATE_TIME_FORMAT) . '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename=' . $name);
        header('Pragma: no-cache');
        header("Expires: 0");
        $stream = fopen("php://output", "w");


        $headers = array('SN', 'Waybill Number', 'Sender', 'Sender Email', 'Sender Phone', 'Sender Address',
            'Sender City', 'Sender State', 'Receiver', 'Receiver Email', 'Receiver Phone', 'Receiver Address',
            'Receiver City', 'Receiver State', 'Weight/Piece', 'Payment Method', 'Amount Due', 'Discounted Amount Due', 'Cash Amount',
            'POS Amount', 'POS Transaction ID', 'Parcel Type', 'Cash on Delivery', 'Delivery Type', 'Package Value',
            '# of Package', 'Shipping Type', 'Created Date', 'Pickup Date', 'Last Modified Date', 'Status',
            'Reference Number', 'Originating Branch', 'Route', 'Request Type', 'For Return', 'Other Info',
            'Company Reg No', 'Region', 'Business Manager', 'Territory', 'Billing Plan Name', 'Created By', 'Amount due to Merchant', 'Insurance Charge',
            'Storage/Demurage Charge', 'Handling Charge', 'Duty Charge', 'Cost of Crating', 'Other Charges',
            'POD Name', 'POD Date', 'Sales Banks',
            'Sales Account No.', 'Sales Teller No.', 'Sales Teller Amount', 'Sales Teller Date',
            'COD Banks', 'COD Account No.', 'COD Teller No.', 'COD Teller Amount.', 'COD Teller Date',
            'Rtd Teller Banks', 'Rtd Teller Account No.', 'Rtd Teller No.', 'Rtd Teller Amount.', 'Rtd Teller Date',
            'Invoice Number'
            );

        /*if(array_key_exists('with_sales_teller', $filters) && $filters['with_sales_teller'] == '1'){
            $headers[] = 'Sales Banks';
            $headers[] = 'Sales Account No.';
            $headers[] = 'Sales Teller No.';
        }
        if(array_key_exists('with_cod_teller', $filters) && $filters['with_cod_teller'] == '1'){
            $headers[] = 'COD Banks';
            $headers[] = 'COD Account No.';
            $headers[] = 'COD Teller No.';
        }*/
        fputcsv($stream, $headers);



        $filters['count'] = $count;
        $total_count = 0;
        $serial_number = 1;
        while (true) {
            $filters['offset'] = $offset;
            $filtered_parcels = $parcel->getParcelsByFilters(array_filter($filters, 'strlen'));
            $response = new ResponseHandler($filtered_parcels);
            if ($response->isSuccess()) {
                $data = $response->getData();
                $parcels = $data['parcels'];

                $exportData = [];
                foreach ($parcels as $key => $result) {
                    $exportData[] = [
                        $serial_number++,
                        $result['parcel_waybill_number'],
                        $result['sender_firstname'] . ' ' . $result['sender_lastname'],
                        $result['sender_email'],
                        $result['sender_phone'],
                        $result['sender_address_street_address1'] . ' ' . $result['sender_address_street_address2'],
                        $result['sender_address_city_name'],
                        $result['sender_address_state_name'],
                        $result['receiver_firstname'] . ' ' . $result['receiver_lastname'],
                        $result['receiver_email'],
                        $result['receiver_phone'],
                        $result['receiver_address_street_address1'] . ' ' . $result['receiver_address_street_address2'],
                        $result['receiver_address_city_name'],
                        $result['receiver_address_state_name'],
                        $result['parcel_weight'],
                        ServiceConstant::getPaymentMethod($result['parcel_payment_type']),
                        $result['parcel_amount_due'],
                        $result['parcel_discounted_amount_due'],
                        $result['parcel_cash_amount'],
                        $result['parcel_pos_amount'],
                        $result['parcel_pos_trans_id'],
                        ServiceConstant::getParcelType($result['parcel_parcel_type']),
                        $result['parcel_cash_on_delivery'] ? 'Yes' : 'No',
                        ServiceConstant::getDeliveryType($result['parcel_delivery_type']),
                        $result['parcel_package_value'],
                        $result['parcel_no_of_package'],
                        ServiceConstant::getShippingType($result['parcel_shipping_type']),
                        Util::convertToTrackingDateFormat($result['parcel_created_date']),
                        Util::convertToTrackingDateFormat($result['parcel_pickup_date']),
                        Util::formatDate(ServiceConstant::DATE_TIME_FORMAT, $result['parcel_modified_date']),
                        strip_tags(ServiceConstant::getStatus($result['parcel_status'])),
                        $result['parcel_reference_number'],
                        $result['created_branch_name'],
                        Calypso::getDisplayValue($result, 'route_name', ''),
                        ServiceConstant::getRequestType(Calypso::getDisplayValue($result, 'parcel_request_type', '')),
                        $result['parcel_for_return'] ? 'Yes' : 'No',
                        $result['parcel_other_info'],
                        $result['company_reg_no'],

                        $result['company_region'],
                        $result['company_business_manager'],
                        $result['company_territory'],

                        $result['billing_plan_name'],
                        $result['created_by_fullname'],
                        $result['parcel_cash_on_delivery_amount'],
                        $result['parcel_insurance'],
                        $result['parcel_storage_demurrage'],
                        $result['parcel_handling_charge'],
                        $result['parcel_duty_charge'],
                        $result['parcel_cost_of_crating'],
                        $result['parcel_others'],

                        (array_key_exists('delivery_receipt_name', $result)?$result['delivery_receipt_name']:''),
                        (array_key_exists('delivery_receipt_delivered_at', $result)?$result['delivery_receipt_delivered_at']:''),

                        $result['teller_bank_name'],
                        $result['teller_account_no'],
                        $result['teller_teller_no'],
                        $result['teller_amount_paid'],
                        $result['teller_created_date'],

                        $result['cod_teller_bank_name'],
                        $result['cod_teller_account_no'],
                        $result['cod_teller_teller_no'],
                        $result['cod_teller_amount_paid'],
                        $result['cod_teller_created_date'],

                        $result['rtd_teller_bank_name'],
                        $result['rtd_teller_account_no'],
                        $result['rtd_teller_teller_no'],
                        $result['rtd_teller_amount_paid'],
                        $result['rtd_teller_created_date'],
                        $result['invoice_parcel_invoice_number'],
                    ];

                    /*if(array_key_exists('with_sales_teller', $filters) && $filters['with_sales_teller'] == '1'){
                        $exportData[] = Calypso::getValue($result, 'teller_bank_name', '');
                        $exportData[] = Calypso::getValue($result, 'teller_account_no', '');
                        $exportData[] = Calypso::getValue($result, 'teller_teller_no', '');
                    }
                    if(array_key_exists('with_cod_teller', $filters) && $filters['with_cod_teller'] == '1'){
                        $exportData[] = Calypso::getValue($result, 'cod_teller_bank_name', '');
                        $exportData[] = Calypso::getValue($result, 'cod_teller_account_no', '');
                        $exportData[] = Calypso::getValue($result, 'cod_teller_teller_no', '');
                    }*/
                }


                foreach ($exportData as $row) {
                    fputcsv($stream, $row);
                }

                $total_count += count($parcels);
                if ($total_count >= $data['total_count'] || count($parcels) == 0) {
                    break;
                }
                $offset += $count;
            } else {
                $this->flashError('An error occurred while trying to download report: Reason: ' . $response->getError());
                return $this->redirect(Yii::$app->getRequest()->getReferrer());
            }
        }

        fclose($stream);
        exit;
    }

    /**
     * Bulk Shipment
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     */
    public function actionBulkshipment1()
    {
        $companyAdapter = new CompanyAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $companies = $companyAdapter->getAllCompanies([]);
        $billingPlanAdapter = new BillingPlanAdapter();
        /*$billingPlans = $billingPlanAdapter->getBillingPlans(['no_paginate' => '1', 'type' => BillingPlanAdapter::TYPE_WEIGHT_AND_ON_FORWARDING]);*/

        $billingPlans = $billingPlanAdapter->getCompanyBillingPlans();

        $billingPlans = ArrayHelper::map($billingPlans, 'id', 'name', 'company_id', 'p');
        $refData = new RefAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $paymentMethods = Calypso::getValue($refData->getPaymentMethods(), 'data', []);

        return $this->renderAjax('partial_bulk_shipment', ['companies' => $companies, 'billing_plans' => $billingPlans, 'payment_methods' => $paymentMethods]);
    }
	
    public function actionBulkshipment()
    {
        $companyAdapter = new CompanyAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $companies = $companyAdapter->getAllCompanies([]);
        $billingPlanAdapter = new BillingPlanAdapter();
        /*$billingPlans = $billingPlanAdapter->getBillingPlans(['no_paginate' => '1', 'type' => BillingPlanAdapter::TYPE_WEIGHT_AND_ON_FORWARDING]);*/

        $refData = new RefAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $paymentMethods = Calypso::getValue($refData->getPaymentMethods(), 'data', []);
        $billingPlans = $billingPlanAdapter->getCompanyBillingPlans();

        // if this is a corporate user, show only his company and payment method should be deferred
        if(Calypso::userIsInRole(ServiceConstant::USER_TYPE_COMPANY_OFFICER) ||
            Calypso::userIsInRole(ServiceConstant::USER_TYPE_COMPANY_ADMIN)){
            $this_company = [];
            $this_company_id = Calypso::getInstance()->session('user_session')['company']['id'];
            foreach ($companies as $comp) {
                if($comp['id'] == $this_company_id){
                    $this_company = $comp;
                }
            }
            $companies = [$this_company];
            $paymentMethods = [['name' => 'DEFERRED'], ['id' => 4]];
        }
        $billingPlans = ArrayHelper::map($billingPlans, 'id', 'name', 'company_id', 'p');

        return $this->renderAjax('partial_bulk_shipment', ['companies' => $companies, 'billing_plans' => $billingPlans, 'payment_methods' => $paymentMethods]);
    }

    /**
     * Create bulk shipment
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     */
    public function actionCreatebulkshipment()
    {

        ///*

        if (!Yii::$app->getRequest()->isAjax) {
            return $this->redirect(Yii::$app->getRequest()->getReferrer());
        }
        //*/



        $model = new BulkShipmentModel();
        $postData = Yii::$app->getRequest()->post();

        /*
        $postData['company_id'] = '55';
        $postData['billing_plan_id'] = '3';
        $postData['payment_type'] = '1';
        */

        //return $this->sendErrorResponse($postData, 200);


        $model->load($postData, '');
        $model->dataFile = UploadedFile::getInstanceByName('dataFile');


        $response = $model->process();


        //return $this->sendErrorResponse($response, 200);

        if (!($response instanceof ResponseHandler)) {
            if ($model->hasErrors()) {
                return $this->sendErrorResponse($model->getErrorMessage(), 200);
            } else {
                return $this->sendErrorResponse('Something went wrong while creating bulk shipment. Please try again', 200);
            }
        }



        if (!$response->isSuccess()) {
            return $this->sendErrorResponse($response->getError(), 200);
        }

        $this->flashSuccess('Shipments have been queued for creation. <a href="/shipments/bulk">View Progress</a>');
        return $this->redirect(Yii::$app->getRequest()->getReferrer());
    }

    /**
     * View Bulk Shipment Tasks
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @return string
     */
    public function actionBulk($page = 1)
    {
        $parcelAdapter = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $taskId = Yii::$app->getRequest()->get('task_id', false);
        
        if ($taskId) {
            $task = $parcelAdapter->getBulkShipmentTask($taskId);
            return $this->render('bulk_shipment_task_details', ['task_id' => $taskId, 'task' => $task]);
        }
        
        $offset = ($page - 1) * $this->page_width;
        $tasks = $parcelAdapter->getBulkShipmentTasks($offset, $this->page_width);
        $environment = 'local';
        //$environment = getenv("APPLICATION_ENV") ? getenv("APPLICATION_ENV") : 'local';
        $s3BaseUrl = '//s3-us-west-2.amazonaws.com/bulk-waybills/' . $environment . '/';
        //$s3BaseUrl = '//s3.amazonaws.com/tnt-bulk-waybills/' . $environment . '/';
        return $this->render('bulk_shipment_tasks',
            [
                'tasks' => $tasks['tasks'],
                'total_count' => $tasks['total_count'],
                's3_base_url' => $s3BaseUrl,
                'page_width' => $this->page_width,
                'offset' => $offset
            ]
        );
    }

    /**
     * Print Bulk Shipment
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param null $task_id
     * @return Response
     */
    public function actionPrintbulkshipment($task_id = null)
    {
        if (is_null($task_id)) {
            return $this->redirect(Yii::$app->getRequest()->getReferrer());
        }

        $parcelAdapter = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $response = $parcelAdapter->createBulkWaybillPrintingTask($task_id);
        if ($response->isSuccess()) {
            $userEmail = ArrayHelper::getValue(Yii::$app->getSession()->get('user_session'), 'email');
            $this->flashSuccess("Waybills generation for bulk Shipment #$task_id has been queued. A link to a printable document will be sent to your email (<strong>" . $userEmail . "</strong>) when done.");
        } else {
            $this->flashError($parcelAdapter->getLastErrorMessage());
        }

        return $this->redirect(Yii::$app->getRequest()->getReferrer());
    }

    /**
     * @author Babatunde Otaru <tunde@cottacush.com>
     * @param $waybill_number
     * @return Response
     */
    public function actionCancelshipment($waybill_number)
    {
        $parcelAdapter = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $response = $parcelAdapter->cancel(['waybill_numbers' => $waybill_number]);
        $response = new ResponseHandler($response);
        if ($response->getStatus() == ResponseHandler::STATUS_OK) {
            $this->flashSuccess('Shipment successfully marked as CANCELLED');
        } else {
            $this->flashError('An error occurred while trying to cancel shipment. #' . $response->getError());
        }

        return $this->redirect(Yii::$app->request->referrer);

    }

    public function actionExceptions(){
        $viewData = [];

        $regionAdapter = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $exceptions = $regionAdapter->getShipmentExceptions(\Yii::$app->request->get());

        $viewData['exceptions'] = $exceptions;

        $branchAdapter = new BranchAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $allHubs = $branchAdapter->getAllHubs(false);


        $viewData['branches'] = [];
        if ($allHubs['status'] === ResponseHandler::STATUS_OK) {
            $viewData['branches'] = $allHubs['data'];
        }

        return $this->render('exceptions', $viewData);
    }

    public function actionDelayedshipments(){
        $viewData = [];

        $regionAdapter = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $shipments = $regionAdapter->getDelayedShipments(\Yii::$app->request->get());

        $viewData['shipments'] = $shipments;

        $branchAdapter = new BranchAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $allHubs = $branchAdapter->getAllHubs(false);

        $viewData['branches'] = [];
        if ($allHubs['status'] === ResponseHandler::STATUS_OK) {
            $viewData['branches'] = $allHubs['data'];
        }

        return $this->render('delayedShipments', $viewData);
    }

    public function actionValidateparcels(){

        if(Yii::$app->request->isPost){
            $adapter = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
            $waybill_numbers = Yii::$app->request->post()['numbers'];
            $by = Yii::$app->request->post()['by'];

            $response = new ResponseHandler($adapter->validateNumbers($waybill_numbers, $by));
            if(!$response->isSuccess()){
                $this->flashError($response->getError());
                return $this->render('validateparcels', ['numbers' => $waybill_numbers, 'by' => $by]);
            }
            $results = $response->getData();



            $name = 'report_' . date(ServiceConstant::DATE_TIME_FORMAT) . '.csv';
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename=' . $name);
            header('Pragma: no-cache');
            header("Expires: 0");
            $stream = fopen("php://output", "w");


            $headers = array('SN', 'Number', 'Status');

            fputcsv($stream, $headers);


            $total_count = 0;
            $serial_number = 1;

            $exportData = [];
            foreach ($results as $key => $result) {
                $exportData[] = [
                    $serial_number++,
                    $result['number'],
                    $result['status'],
                ];

            }


            foreach ($exportData as $row) {
                fputcsv($stream, $row);
            }

            fclose($stream);
            exit;

        }

        return $this->render('validateparcels', ['numbers' => '', 'by' => 'reference number']);
    }
}
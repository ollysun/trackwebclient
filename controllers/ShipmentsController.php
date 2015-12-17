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
use Adapter\BranchAdapter;
use Adapter\Globals\HttpStatusCodes;
use Adapter\Globals\ServiceConstant;
use Adapter\ParcelAdapter;
use Adapter\UserAdapter;
use Adapter\RefAdapter;
use Adapter\RegionAdapter;
use Adapter\RequestHelper;
use Adapter\ResponseHandler;
use Adapter\Util\Calypso;
use Adapter\Util\Util;
use app\services\HubService;
use Adapter\TellerAdapter;
use yii\data\Pagination;
use Yii;
use yii\helpers\Url;
use yii\web\Response;
use Adapter\RouteAdapter;

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
        if ($page_width != null) {
            $this->page_width = $page_width;
            Calypso::getInstance()->cookie('page_width', $page_width);
        }

        $page_width = is_null($page_width) ? $this->page_width : $page_width;
        $offset = ($page - 1) * $page_width;
        $parcel = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());


        if (isset(Calypso::getInstance()->get()->from, Calypso::getInstance()->get()->to)) {
            $from_date = Calypso::getInstance()->get()->from;
            $to_date = Calypso::getInstance()->get()->to;
            $filter = null;
            //  $filter = isset(Calypso::getInstance()->get()->date_filter) ? Calypso::getInstance()->get()->date_filter : '-1';
            $response = $parcel->getFilterParcelsByDateAndStatus($from_date . ' 00:00:00', $to_date . ' 23:59:59', $filter, $offset, $this->page_width, 1, null, null, true);
            $search_action = true;

        } elseif (!empty(Calypso::getInstance()->get()->search)) { //check if not empty criteria
            $search = Calypso::getInstance()->get()->search;
            $response = $parcel->getSearchParcels(null, $search, $offset, $this->page_width, 1, $this->branch_to_view, null, null);
            $search_action = true;
            $filter = null;

        } else {
            $response = $parcel->getParcels($from_date . '%2000:00:00', $to_date . '%2023:59:59', null, $this->branch_to_view, $offset, $this->page_width, 1, 1, 1, true);
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
        return $this->render('all', array('filter' => $filter, 'parcels' => $data, 'from_date' => $from_date, 'to_date' => $to_date, 'offset' => $offset, 'page_width' => $this->page_width, 'search' => $search_action, 'total_count' => $total_count));
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
            $record = array('waybill_numbers' => implode(",", $waybills), 'held_by_id' => Calypso::getValue($data, 'held_by_id', null));

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
            $response = $parcel->getSearchParcels(null, $search, $offset, $page_width, 1, $this->branch_to_view);
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
            $response = $parcel->getSearchParcels(null, $search, $offset, $page_width, 1, $this->branch_to_view);
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
                        $this->flashError($response->getError());
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
            $response = $parcel->getSearchParcels(null, $search, $offset, $this->page_width, 1, $this->branch_to_view, null, null);
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
                    $result = $parcel->sendReturnRequest($records['waybill_numbers'], $records['comment']);
                    $response = new ResponseHandler($result);

                    if ($response->getStatus() == ResponseHandler::STATUS_OK) {
                        $data = $response->getData();
                        if (empty($data['bad_parcels']))
                            $this->flashSuccess('Return request sent');
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
                $date = $records['date'];
                $time = $records['time'];
                $date_and_time_timestamp = Util::getDateTimeFormatFromDateTimeFields($date, $time);
                $phoneNumber = $records['phone'];
                $rawData = $records['waybills'];
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

                        $parcelData = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
                        $success_msg = '';
                        if ($task == 'receive') {
                            $response = $parcelData->receiveFromBeingDelivered($record);
                            $success_msg = 'Shipments successfully received';
                        } elseif ($task == 'deliver') {
                            $record['receiver_name'] = $fullName;
                            $record['receiver_phone_number'] = $phoneNumber;
                            $record['receiver_email'] = $email;
                            $record['date_and_time_of_delivery'] = $date_and_time_timestamp;
                            $response = $parcelData->moveToDelivered($record);
                            $success_msg = 'Shipments successfully delivered';
                        } elseif ($task == 'return') {
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
        $user_session = Calypso::getInstance()->session("user_session");
        $parcelsAdapter = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $dispatch_parcels = $parcelsAdapter->getECDispatchedParcels($this->branch_to_view, $offset, $page_width,$search);
        $parcels = new ResponseHandler($dispatch_parcels);
        $total_count = 0;
        if ($parcels->getStatus() == ResponseHandler::STATUS_OK) {
            $data = $parcels->getData();
            $parcels = $data['parcels'];
            $total_count = $data['total_count'];
        }

        return $this->render('dispatched', array('todays_date' => $todays_date, 'parcels' => $parcels, 'total_count' => $total_count, 'offset' => $offset, 'page_width' => $page_width));
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

        $filter_params = ['start_modified_date', 'end_modified_date', 'for_return', 'parcel_type', 'status', 'min_weight', 'max_weight', 'min_amount_due', 'max_amount_due', 'cash_on_delivery', 'delivery_type', 'payment_type', 'shipping_type', 'start_created_date', 'end_created_date', 'created_branch_id', 'route_id', 'request_type'];
        $extra_details = ['with_to_branch', 'with_from_branch', 'with_sender', 'with_sender_address', 'with_receiver', 'with_receiver_address', 'with_bank_account', 'with_created_branch', 'with_route', 'with_created_by'];


        $filters = [];
        foreach ($filter_params as $param) {
            $$param = Yii::$app->request->get($param);
            $filters[$param] = $$param;
        }

        foreach ($extra_details as $extra) {
            $filters[$extra] = true;
        }

        $from_date = Yii::$app->request->get('start_created_date', date('Y/m/d'));
        $end_date = Yii::$app->request->get('end_created_date', date('Y/m/d'));
        $filters['start_created_date'] = $from_date . ' 00:00:00';
        $filters['end_created_date'] = $end_date . ' 23:59:59';

        $start_modified_date = Yii::$app->request->get('start_modified_date', date('Y/m/d'));
        $end_modified_date = Yii::$app->request->get('end_modified_date', date('Y/m/d'));

        $filters['start_modified_date'] = $start_modified_date . ' 00:00:00';
        $filters['end_modified_date'] = $end_modified_date . ' 23:59:59';


        if (!empty(Yii::$app->request->get('download'))) {

            $parcel = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());

            $filters['send_all'] = true;
            $filtered_parcels = $parcel->getParcelsByFilters(array_filter($filters, 'strlen'));
            $response = new ResponseHandler($filtered_parcels);

            $name = 'report_' . date(ServiceConstant::DATE_TIME_FORMAT) . '.csv';
            $data = array();

            $headers = array('SN', 'Waybill Number', 'Sender', 'Sender Email', 'Sender Phone', 'Sender Address', 'Receiver', 'Receiver Email', 'Receiver Phone', 'Receiver Address', 'Weight', 'Payment Method', 'Amount Due', 'Cash Amount', 'POS Amount', 'POS Transaction ID', 'Parcel Type', 'Cash on Delivery', 'Delivery Type', 'Package Value', '# of Package', 'Shipping Type', 'Created Date', 'Last Modified Date', 'Status', 'Reference Number', 'Originating Branch', 'Route', 'Request Type', 'For Return', 'Other Info');
            foreach ($response->getData() as $key => $result) {
                $data[] = [
                    $key + 1,
                    $result['waybill_number'],
                    $result['sender']['firstname'] . ' ' . $result['sender']['lastname'],
                    $result['sender']['email'],
                    $result['sender']['phone'],
                    $result['sender_address']['street_address1'] . ' ' . $result['sender_address']['street_address2'] . ', ' . $result['sender_address']['city']['name'] . ', ' . $result['sender_address']['state']['name'],
                    $result['receiver']['firstname'] . ' ' . $result['receiver']['lastname'],
                    $result['receiver']['email'],
                    $result['receiver']['phone'],
                    $result['receiver_address']['street_address1'] . ' ' . $result['receiver_address']['street_address2'] . ', ' . $result['receiver_address']['city']['name'] . ', ' . $result['receiver_address']['state']['name'],
                    $result['weight'],
                    ServiceConstant::getPaymentMethod($result['payment_type']),
                    $result['amount_due'],
                    $result['cash_amount'],
                    $result['pos_amount'],
                    $result['pos_trans_id'],
                    ServiceConstant::getParcelType($result['parcel_type']),
                    $result['cash_on_delivery'] ? 'Yes' : 'No',
                    ServiceConstant::getDeliveryType($result['delivery_type']),
                    $result['package_value'],
                    $result['no_of_package'],
                    ServiceConstant::getShippingType($result['shipping_type']),
                    Util::convertToTrackingDateFormat($result['created_date']),
                    Util::formatDate(ServiceConstant::DATE_TIME_FORMAT, $result['modified_date']),
                    strip_tags(ServiceConstant::getStatus($result['status'])),
                    $result['reference_number'],
                    isset($result['created_branch']) ? $result['created_branch']['name'] : '',
                    isset($result['route']) ? $result['route']['name'] : '',
                    ServiceConstant::getRequestType($result['request_type']),
                    $result['for_return'] ? 'Yes' : 'No',
                    $result['other_info'],
                ];
            }
            Util::exportToCSV($name, $headers, $data);
            exit;
        }

        $filters['offset'] = $offset;
        $filters['count'] = $page_width;
        $filters['with_total_count'] = true;

        $status = ServiceConstant::getStatusRef();
        $payment_methods = ServiceConstant::getPaymentMethods();
        $request_types = ServiceConstant::getRequestTypes();
        $shipping_types = ServiceConstant::getShippingTypes();
        $delivery_types = ServiceConstant::getDeliveryTypes();

        $branch_adapter = new BranchAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $branches = $branch_adapter->getAll();

        $route_adapter = new RouteAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $routes = $route_adapter->getRoutes(null, null, null, null, null);

        $parcel = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $filtered_parcels = $parcel->getParcelsByFilters(array_filter($filters, 'strlen'));
        $response = new ResponseHandler($filtered_parcels);

        $parcels = [];
        $total_count = 0;
        if ($response->getStatus() == ResponseHandler::STATUS_OK) {
            $data = $response->getData();
            $parcels = $data['parcels'];
            $total_count = $data['total_count'];
        }

        return $this->render('report', array(
            'parcels' => $parcels,
            'branches' => $branches['data'],
            'routes' => $routes['data'],
            'statuses' => $status,
            'payment_methods' => $payment_methods,
            'request_types' => $request_types,
            'shipping_types' => $shipping_types,
            'delivery_types' => $delivery_types,
            'filters' => $filters,
            'from_date' => $from_date,
            'end_date' => $end_date,
            'start_modified_date'=>$start_modified_date,
            'end_modified_date' => $end_modified_date,
            'offset' => $offset,
            'page_width' => $page_width,
            'total_count' => $total_count
        ));
    }
}
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
            ($this->userData['role_id'] == ServiceConstant::USER_TYPE_ADMIN) ? null : $this->userData['branch_id']; //displays all when null
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
            $filter = isset(Calypso::getInstance()->get()->date_filter) ? Calypso::getInstance()->get()->date_filter : '-1';
            $response = $parcel->getFilterParcelsByDateAndStatus($from_date . '%2000:00:00', $to_date . '%2023:59:59', $filter, $offset, $this->page_width, 1, $this->branch_to_view, 1, true);
            $search_action = true;
        } elseif (!empty(Calypso::getInstance()->get()->search)) { //check if not empty criteria
            $search = Calypso::getInstance()->get()->search;
            $response = $parcel->getSearchParcels('-1', $search, $offset, $this->page_width, 1, $this->branch_to_view, 1, true);
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
        $route_id = !empty(Calypso::getInstance()->get()->route) ? Calypso::getInstance()->get()->route:null;

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
                        return $this->redirect('/manifest/view?id='.Calypso::getValue($response, 'data.manifest.id', ''));
                        //$this->flashSuccess('Shipments dispatched');
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
            $response = $parcel->getSearchParcels('-1', $search, $offset, $page_width, 1, $this->branch_to_view);
            $search_action = true;
        } else {
            $response = $parcel->getParcelsForDelivery(null, null, ServiceConstant::FOR_DELIVERY, $this->branch_to_view, $offset, $page_width, null, 1, null,$route_id, true);
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

        return $this->render('fordelivery', array('parcels' => $data, 'from_date' => $from_date, 'to_date' => $to_date, 'offset' => $offset, 'page_width' => $page_width, 'search' => $search_action, 'total_count' => $total_count, 'routes' => $route_list, 'route_id'=>$route_id));
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
                    return $this->redirect('/manifest/view?id='.Calypso::getValue($response, 'data.manifest.id', ''));
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
            $response = $parcel->getSearchParcels('-1', $search, $offset, $page_width, 1, $this->branch_to_view);
            $search_action = $search;
        } else {
            $response = $parcel->getParcels(null, null, ServiceConstant::FOR_SWEEPER, $this->branch_to_view, $offset, $page_width, null, 1);
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
        $from_date = date('Y/m/d');
        $to_date = date('Y/m/d');
        $search_action = $search;
        if ($page_width != null) {
            $this->page_width = $page_width;
            Calypso::getInstance()->cookie('page_width', $page_width);
        }
        $offset = ($page - 1) * $page_width;

        if (\Yii::$app->request->isPost) {
            $records = \Yii::$app->request->post();
            if ($records['task'] == 'cancel_shipment') {
                $parcel = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
                $response = $parcel->cancel($records);
                $response = new ResponseHandler($response);

                if ($response->getStatus() == ResponseHandler::STATUS_OK) {
                    $this->flashSuccess('Shipment successfully marked as CANCELLED');
                } else {
                    $this->flashError('An error occurred while trying to cancel shipment. #' . $response->getError());
                }

            } elseif ($records['submit_teller']) {
                if (!isset($records['bank_id'], $records['account_no'], $records['amount_paid'], $records['teller_no'], $records['waybill_numbers'])) {
                    $this->flashError("Invalid parameter(s) sent!");
                } else {
                    $teller = new TellerAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
                    $teller = $teller->addTeller($records);
                    $response = new ResponseHandler($teller);

                    if ($response->getStatus() == ResponseHandler::STATUS_OK) {
                        $this->flashSuccess('Teller successfully added');
                    } else {
                        $this->flashError('An error occurred while trying to add teller. #' . $response->getError());
                    }
                }
            }
        }

        $parcel = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        if (isset(Calypso::getInstance()->get()->from, Calypso::getInstance()->get()->to)) {
            $from_date = Calypso::getInstance()->get()->from;
            $to_date = Calypso::getInstance()->get()->to;
            $filter = isset(Calypso::getInstance()->get()->date_filter) ? Calypso::getInstance()->get()->date_filter : '-1';

            $response = $parcel->getFilterParcelsByDateAndStatus($from_date . '%2000:00:00', $to_date . '%2023:59:59', $filter, $offset, $this->page_width, 1, $this->branch_to_view, 1);
            $search_action = true;
        } elseif (!empty(Calypso::getInstance()->get()->search)) {  //check if not empty criteria
            $search = Calypso::getInstance()->get()->search;
            $response = $parcel->getSearchParcels('-1', $search, $offset, $this->page_width, 1, $this->branch_to_view, 1);
            $search_action = true;
            $filter = null;
        } else {
            //$response = $parcel->getNewParcelsByDate(date('Y-m-d'),$offset,$this->page_width, 1, $this->branch_to_view, 1);
            $response = $parcel->getNewParcelsByDate(date('Y-m-d%2000:00:00', strtotime('now')), $offset, $this->page_width, 1, $this->branch_to_view, 1);
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

        return $this->render('processed', array('filter' => $filter, 'parcels' => $data, 'from_date' => $from_date, 'to_date' => $to_date, 'offset' => $offset, 'page_width' => $this->page_width, 'search' => $search_action, 'total_count' => $total_count, 'banks' => $banks));
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
                return $this->redirect(Url::to('viewbag?waybill_number=' . $waybill_number));
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

        return $this->render('view', array(
            'parcelData' => $data,
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
        $page_width = is_null($page_width) ? $this->page_width : $page_width;
        $offset = ($page - 1) * $page_width;

        if (\Yii::$app->request->isPost) {
            $records = \Yii::$app->request->post();
            $password = $records['password'];
            $rawData = $records['waybills'];
            $task = $records['task'];

            if (empty($rawData) || empty($password) || empty($task)) {
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
                        $response = $parcelData->moveToDelivered($record);
                        $success_msg = 'Shipments successfully delivered';
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
        }
        $user_session = Calypso::getInstance()->session("user_session");
        $parcelsAdapter = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $dispatch_parcels = $parcelsAdapter->getECDispatchedParcels($user_session['branch_id'], $offset, $page_width);
        $parcels = new ResponseHandler($dispatch_parcels);
        $total_count = 0;
        if ($parcels->getStatus() == ResponseHandler::STATUS_OK) {
            $data = $parcels->getData();
            $parcels = $data['parcels'];
            $total_count = $data['total_count'];
        }
        return $this->render('dispatched', array('parcels' => $parcels, 'total_count' => $total_count, 'offset' => $offset, 'page_width' => $page_width));
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

        $user_session = Calypso::getInstance()->session("user_session");
        $parcelsAdapter = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $delivered_parcels = $parcelsAdapter->getDeliveredParcels($this->branch_to_view, $offset, $page_width, $from_date . '%2000:00:00', $to_date . '%2023:59:59');
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
}
<?php
/**
 * Created by PhpStorm.
 * User: RotelandO
 * Date: 7/25/15
 * Time: 8:31 AM
 */

namespace app\controllers;

use Yii;
use Adapter\AdminAdapter;
use Adapter\BranchAdapter;
use Adapter\Globals\ServiceConstant;
use Adapter\ParcelAdapter;
use Adapter\RefAdapter;
use Adapter\RequestHelper;
use Adapter\ResponseHandler;
use Adapter\Util\Calypso;
use app\services\HubService;
use app\services\ParcelService;

class HubsController extends BaseController
{
    public $userData = null;
    public $branch_to_view = null;

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        $this->userData = (Calypso::getInstance()->session('user_session'));
        $this->branch_to_view = ($this->userData['role_id'] == ServiceConstant::USER_TYPE_SUPER_ADMIN) ? null :
            ($this->userData['role_id'] == ServiceConstant::USER_TYPE_ADMIN) ? null : $this->userData['branch_id']; //displays all when null
        return parent::beforeAction($action);
    }

    /**
     * This action allows setting next destination for shipments
     * @return string
     */
    public function actionDestination($page = 1, $page_width = null)
    {
        $viewData['page_width'] = is_null($page_width) ? $this->page_width : $page_width;
        $viewData['offset'] = ($page - 1) * $viewData['page_width'];
        $isGroundman = $this->userData['role_id'] == ServiceConstant::USER_TYPE_GROUNDSMAN;

        $parcelsAdapter = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());

        if (\Yii::$app->request->isPost) {
            $branch = \Yii::$app->request->post('branch');
            $branch_type = \Yii::$app->request->post('branch_type');
            $waybill_numbers = \Yii::$app->request->post('waybills');
            if (!isset($branch) || empty($waybill_numbers)) {
                $this->flashError('Please ensure you set destinations at least a (one) for the parcels');
            }

            $postParams['waybill_numbers'] = implode(',', $waybill_numbers);

            if ($branch == $this->userData['branch_id']) {
                $response = $parcelsAdapter->assignToGroundsMan($postParams);
            } else {
                if($branch_type == 'Route'){
                    $postParams['route_id'] = $branch;
                    $response = $parcelsAdapter->moveForDelivery($postParams);
                }
                else{
                    $postParams['to_branch_id'] = $branch;
                    $response = $parcelsAdapter->moveToForSweeper($postParams);
                }
            }

            if ($response['status'] === ResponseHandler::STATUS_OK) {
                $this->flashSuccess('Parcels have been successfully moved to the next destination. <a href="delivery">Generate Manifest</a>');
            } else {
                $this->flashError('An error occurred while trying to move parcels to next destination. Please try again.');
            }
        }
        $parcelsAdapter = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $arrival_parcels = $parcelsAdapter->getParcelsForNextDestination($isGroundman ? ServiceConstant::ASSIGNED_TO_GROUNDSMAN : ServiceConstant::FOR_ARRIVAL, null, $isGroundman ? $this->userData['branch_id'] : $this->branch_to_view, null, $viewData['offset'], 50, 1);


        if ($arrival_parcels['status'] === ResponseHandler::STATUS_OK) {
            $viewData['parcel_next'] = $arrival_parcels['data']['parcels'];
            $viewData['total_count'] = $arrival_parcels['data']['total_count'];
        } else {
            $this->flashError('An error occurred while trying to fetch parcels. Please try again.');
            $viewData['parcel_next'] = [];
        }
        $viewData['isGroundsman'] = $isGroundman;
        return $this->render('destination', $viewData);
    }


    public function actionHubarrival()
    {
        $parcelsAdapter = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());

        if (\Yii::$app->request->isPost) {
            $branch = \Yii::$app->request->post('branch');
            $waybill_numbers = \Yii::$app->request->post('waybills');
            if (!isset($branch) || empty($waybill_numbers)) {
                $this->flashError('Please ensure you set destinations at least a (one) for the parcels');
            }

            $postParams['waybill_numbers'] = implode(',', $waybill_numbers);
            $postParams['to_branch_id'] = $branch;
            $response = $parcelsAdapter->moveToForSweeper($postParams);
            if ($response['status'] === ResponseHandler::STATUS_OK) {
                $this->flashSuccess('Parcels have been successfully moved to the next destination. <a href="hubmovetodelivery">Generate Manifest</a>');
            } else {
                $this->flashError('An error occurred while trying to move parcels to next destination. Please try again.');
            }
        }
        $user_session = Calypso::getInstance()->session("user_session");
        $parcelsAdapter = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $arrival_parcels = $parcelsAdapter->getParcelsForNextDestination(ServiceConstant::FOR_ARRIVAL, null, $user_session['branch_id']);
        if ($arrival_parcels['status'] === ResponseHandler::STATUS_OK) {
            $viewData['parcel_next'] = $arrival_parcels['data'];
        } else {
            $this->flashError('An error occurred while trying to fetch parcels. Please try again.');
            $viewData['parcel_next'] = [];
        }

        return $this->render('hub_arrival', $viewData);
    }

    public function actionHubdispatch()
    {
        $from_date = date('Y/m/d');
        $to_date = date('Y/m/d');
        $user_session = Calypso::getInstance()->session("user_session");
        $from_branch_id = $user_session['branch_id'];
        $to_branch_id = Calypso::getValue(Yii::$app->request->post(), 'to_branch_id', null);

        $hubAdp = new BranchAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $hubs = $hubAdp->getAll();
        $hubs = new ResponseHandler($hubs);
        $hub_list = $hubs->getStatus() == ResponseHandler::STATUS_OK ? $hubs->getData() : [];

        $parcelsAdapter = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        if (isset(Calypso::getInstance()->get()->from, Calypso::getInstance()->get()->to)) {
            $from_date = Calypso::getInstance()->get()->from;
            $to_date = Calypso::getInstance()->get()->to;
            $filter = isset(Calypso::getInstance()->get()->date_filter) ? Calypso::getInstance()->get()->date_filter : '-1';
            $dispatch_parcels = $parcelsAdapter->getDispatchedParcels($user_session['branch_id'], $to_branch_id, $from_date . '%2000:00:00', $to_date . '%2023:59:59', $filter);
        } else
            $dispatch_parcels = $parcelsAdapter->getDispatchedParcels($user_session['branch_id'], $to_branch_id);
        $parcels = new ResponseHandler($dispatch_parcels);
        $parcel_list = $parcels->getStatus() == ResponseHandler::STATUS_OK ? $parcels->getData() : [];

        return $this->render('hub_dispatch', array('sweeper' => [], 'hubs' => $hub_list, 'parcels' => $parcel_list, 'branch_id' => $to_branch_id, 'from_date' => $from_date, 'to_date' => $to_date));
    }

    /**
     * Ajax calls to get all hubs
     */
    public function actionAllhubs()
    {

        $branchAdapter = new BranchAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $allHubs = $branchAdapter->getAllHubs(false);
        if ($allHubs['status'] === ResponseHandler::STATUS_OK) {
            return $this->sendSuccessResponse($allHubs['data']);
        } else {
            return $this->sendErrorResponse($allHubs['message'], null);
        }
    }

    /**
     * Ajax calls to get all routes in the present hub
     */
    public function actionAllroutesforhubs()
    {

        $branchAdapter = new Route(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $user_session = Calypso::getInstance()->session("user_session");
        $allEcsInHubs = $branchAdapter->listECForHub($user_session['branch_id']);
        if ($allEcsInHubs['status'] === ResponseHandler::STATUS_OK) {
            return $this->sendSuccessResponse($allEcsInHubs['data']);
        } else {
            return $this->sendErrorResponse($allEcsInHubs['message'], null);
        }
    }

    /**
     * Ajax calls to get all ec in the present hub
     */
    public function actionAllecforhubs()
    {

        $branchAdapter = new BranchAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $user_session = Calypso::getInstance()->session("user_session");
        $allEcsInHubs = $branchAdapter->listECForHub($user_session['branch_id']);
        if ($allEcsInHubs['status'] === ResponseHandler::STATUS_OK) {
            return $this->sendSuccessResponse($allEcsInHubs['data']);
        } else {
            return $this->sendErrorResponse($allEcsInHubs['message'], null);
        }
    }

    /**
     * Ajax calls to get Branch details
     */
    public function actionBranchdetails()
    {
        $branch_id = \Yii::$app->request->get('id');
        if (!isset($branch_id)) {
            return $this->sendErrorResponse("Invalid parameter(s) sent!", null);
        }
        $refData = new RefAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $branch = $refData->getBranchbyId($branch_id);
        if ($branch['status'] === ResponseHandler::STATUS_OK) {
            return $this->sendSuccessResponse($branch['data']);
        } else {
            return $this->sendErrorResponse($branch['message'], null);
        }
    }

    /**
     * Ajax calls to get Branch details
     */
    public function actionStaffdetails()
    {
        $code = \Yii::$app->request->get('code');
        if (!isset($code)) {
            return $this->sendErrorResponse("Invalid parameter(s) sent!", null);
        }
        $adminData = new AdminAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $staff = $adminData->getStaff($code);
        if ($staff['status'] === ResponseHandler::STATUS_OK) {
            return $this->sendSuccessResponse($staff['data']);
        } else {
            return $this->sendErrorResponse($staff['message'], null);
        }
    }

    /**
     * Ajax calls to get Branch details
     */
    public function actionGeneratemanifest()
    {

    }

    /**
     * This action moves shipment to in transit.
     * Shipments are also assigned to sweepers for delivery
     * @return string
     */
    public function actionDelivery($page = 1, $page_width = null)
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
                    return $this->viewManifest($payloadData);
                    //return \Yii::$app->runAction('hubs/manifest', $payloadData);
                } else {
                    //Flash error message
                    $this->flashError($response['message']);
                }
            }
        }
        $viewData = [];
        $to_branch_id = \Yii::$app->request->get('bid');
        $btype = \Yii::$app->request->get('btype');
        if (!isset($to_branch_id, $btype)) {
            $to_branch_id = null;
        }

        $viewData['to_branch_id'] = $to_branch_id;
        $viewData['btype'] = $btype;
        $viewData['page_width'] = is_null($page_width) ? $this->page_width : $page_width;
        $viewData['offset'] = ($page - 1) * $viewData['page_width'];

        $user_session = Calypso::getInstance()->session("user_session");
        $parcelsAdapter = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $for_delivery_parcels = $parcelsAdapter->getParcelsForNextDestination(ServiceConstant::FOR_SWEEPER, $user_session['branch_id'], $to_branch_id, null, $viewData['offset'], $viewData['page_width'], 1);
        if ($for_delivery_parcels['status'] === ResponseHandler::STATUS_OK) {
            $viewData['parcel_delivery'] = $for_delivery_parcels['data']['parcels'];
            $viewData['total_count'] = $for_delivery_parcels['data']['total_count'];
        } else {
            $this->flashError('An error occurred while trying to fetch parcels. Please try again.');
            $viewData['parcel_delivery'] = [];
            $viewData['total_count'] = 0;
        }
        return $this->render('delivery', $viewData);
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
            $this->flashError('An error occurred while trying to fetch parcels. Please try again.');
            $viewData['parcel_delivery'] = [];
        }

        return $this->render('manifest', $viewData);
    }

    public function actionCreatebag()
    {
        $rawData = \Yii::$app->request->getRawBody();
        $data = json_decode($rawData, true);
        $paramWaybills = [];

        if (!isset($data['waybills'])) {
            return $this->sendErrorResponse("Invalid parameter(s) sent!", null);
        }

        foreach ($data['waybills'] as $wb) {
            $paramWaybills[] = $wb['number'];
        }

        $waybills = implode(",", $paramWaybills);
        $payload = [
            'waybill_numbers' => $waybills,
            'to_branch_id' => Calypso::getValue($data, 'to_branch_id', null),
            'status' => ServiceConstant::FOR_SWEEPER
        ];

        $parcelData = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $response = $parcelData->createBag($payload);
        $responseHandler = new ResponseHandler($response);
        $data = $responseHandler->getData();

        if ($responseHandler->getStatus() === ResponseHandler::STATUS_OK) {
            if (empty($data['bad_parcels']))
                return $this->sendSuccessResponse($data);
            else {
                $bad_parcels = $data['bad_parcels'];
                $error_message = 'The following parcels cannot be bagged: ';
                foreach ($bad_parcels as $key => $bad_parcel) {
                    $error_message .= '[' . $key . '#' . $bad_parcel . ']';
                }
                return $this->sendErrorResponse($error_message, null);
            }
        } else {
            return $this->sendErrorResponse($responseHandler->getError(), null);
        }
    }

    public function actionViewbag()
    {
        return $this->render('view_bag');
    }
}
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

class HubsController extends BaseController {

    public function beforeAction($action) {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    /**
     * This action allows setting next destination for shopments
     * @return string
     */
    public function actionDestination()
    {

        $parcelsAdapter = new ParcelAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());

        if(\Yii::$app->request->isPost) {
            $branch = \Yii::$app->request->post('branch');
            $waybill_numbers = \Yii::$app->request->post('waybills');
            if(!isset($branch) || empty($waybill_numbers)) {
                $this->flashError('Please ensure you set destinations at least a (one) for the parcels');
            }

            $postParams['waybill_numbers'] = implode(',', $waybill_numbers);
            $postParams['to_branch_id'] = $branch;
            $response = $parcelsAdapter->moveToForSweeper($postParams);
            if($response['status'] === ResponseHandler::STATUS_OK) {
                $this->flashSuccess('Parcels have been successfully moved to the next destination. <a href="/hubs/hubmovetodelivery">Generate Manifest</a>');
            } else {
                $this->flashError('An error occured while trying to move parcels to next destination. Please try again.');
            }
        }
        $user_session = Calypso::getInstance()->session("user_session");
        $arrival_parcels = $parcelsAdapter->getParcelsForNextDestination(ServiceConstant::FOR_ARRIVAL, $user_session['branch_id']);
        if($arrival_parcels['status'] === ResponseHandler::STATUS_OK) {
            $viewData['parcel_next'] = $arrival_parcels['data'];
        } else {
            $this->flashError('An error occured while trying to fetch parcels. Please try again.');
            $viewData['parcel_next'] = [];
        }
        return $this->render('destination', $viewData);
    }

    public function actionHubdispatch()
    {
        $from_date = date('Y/m/d');
        $to_date = date('Y/m/d');
        $user_session = Calypso::getInstance()->session("user_session");
        $from_branch_id = $user_session['branch_id'];
        $to_branch_id = Calypso::getValue(Yii::$app->request->post(), 'to_branch_id',null);

        $hubAdp = new BranchAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        $hubs = $hubAdp->getAll();
        $hubs = new ResponseHandler($hubs);
        $hub_list = $hubs->getStatus()==ResponseHandler::STATUS_OK?$hubs->getData(): [];

        $parcelsAdapter = new ParcelAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        if(isset(Calypso::getInstance()->get()->from,Calypso::getInstance()->get()->to)){
            $from_date = Calypso::getInstance()->get()->from.' 00:00:00';
            $to_date = Calypso::getInstance()->get()->to.' 23:59:59';
            $filter = isset(Calypso::getInstance()->get()->date_filter) ? Calypso::getInstance()->get()->date_filter : '-1';
            $dispatch_parcels = $parcelsAdapter->getDispatchedParcels($user_session['branch_id'], $to_branch_id,$from_date,$to_date,$filter);
        }else
            $dispatch_parcels = $parcelsAdapter->getDispatchedParcels($user_session['branch_id'], $to_branch_id);
        $parcels = new ResponseHandler($dispatch_parcels);
        $parcel_list = $parcels->getStatus()==ResponseHandler::STATUS_OK?$parcels->getData(): [];

        return $this->render('hub_dispatch', array('sweeper'=>[], 'hubs'=>$hub_list,'parcels'=>$parcel_list, 'branch_id'=>$from_branch_id, 'from_date'=>$from_date, 'to_date'=>$to_date));
    }

    /**
     * Ajax calls to get all hubs
     */
    public function actionAllhubs() {

        $branchAdapter = new BranchAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        $allHubs = $branchAdapter->getAllHubs();
        if ($allHubs['status'] === ResponseHandler::STATUS_OK) {
            return $this->sendSuccessResponse($allHubs['data']);
        } else {
            return $this->sendErrorResponse($allHubs['message'], null);
        }
    }

    /**
     * Ajax calls to get all ec in the present hub
     */
    public function actionAllecforhubs() {

        $branchAdapter = new BranchAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
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
    public function actionBranchdetails(){
        $branch_id = \Yii::$app->request->get('id');
        if(!isset($branch_id)) {
            return $this->sendErrorResponse("Invalid parameter(s) sent!", null);
        }
        $refData = new RefAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
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
    public function actionStaffdetails(){
        $code = \Yii::$app->request->get('code');
        if(!isset($code)) {
            return $this->sendErrorResponse("Invalid parameter(s) sent!", null);
        }
        $adminData = new AdminAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
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
    public function actionGeneratemanifest(){

    }

    /**
     * This action moves shipment to in transit.
     * Shipments are also assigned to sweepers for delivery
     * @return string
     */
    public function actionDelivery()
    {
        //Move to In Transit (waybill_numbers, to_branch_id.
        //and staff_id (not the code)
        if(\Yii::$app->request->isPost) {
            $rawData = \Yii::$app->request->post('payload');
            $data = json_decode($rawData, true);
            $service = new HubService();
            $payloadData = $service->buildPostData($data);
            if(!isset($payloadData['waybill_numbers'], $payloadData['to_branch_id'], $payloadData['held_by_id'])) {
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
        if(!isset($to_branch_id, $btype)) {
            $to_branch_id = null;
        }

        $viewData['to_branch_id'] = $to_branch_id;
        $viewData['btype'] = $btype;
        $user_session = Calypso::getInstance()->session("user_session");
        $parcelsAdapter = new ParcelAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        $for_delivery_parcels = $parcelsAdapter->getParcelsForNextDestination(ServiceConstant::FOR_SWEEPER, $user_session['branch_id'], $to_branch_id);
        if($for_delivery_parcels['status'] === ResponseHandler::STATUS_OK) {
            $viewData['parcel_delivery'] = $for_delivery_parcels['data'];
        } else {
            $this->flashError('An error occured while trying to fetch parcels. Please try again.');
            $viewData['parcel_delivery'] = [];
        }
        return $this->render('delivery', $viewData);
    }

    /**
     * This is a method to render the view for generating manifest
     * @param $data
     * @return string
     */
    public function viewManifest($data) {

        $user_session = Calypso::getInstance()->session("user_session");
        $parcelsAdapter = new ParcelAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        $in_transit_parcels = $parcelsAdapter->getParcelsForNextDestination(ServiceConstant::IN_TRANSIT, $user_session['branch_id'], $data['to_branch_id'], $data['held_by_id']);
        if($in_transit_parcels['status'] === ResponseHandler::STATUS_OK) {
            $viewData['parcel_delivery'] = $in_transit_parcels['data'];

            $adminData = new AdminAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
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

        return $this->render('manifest', $viewData);
    }
    public function actionViewbag() {
        return $this->render('view_bag');
    }
}
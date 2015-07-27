<?php
/**
 * Created by PhpStorm.
 * User: RotelandO
 * Date: 7/25/15
 * Time: 8:31 AM
 */

namespace app\controllers;


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
}
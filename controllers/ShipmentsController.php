<?php
/**
 * Created by PhpStorm.
 * User: RotelandO
 * Date: 7/25/15
 * Time: 1:06 PM
 */

namespace app\controllers;


use Adapter\AdminAdapter;
use Adapter\Globals\ServiceConstant;
use Adapter\ParcelAdapter;
use Adapter\UserAdapter;
use Adapter\RequestHelper;
use Adapter\ResponseHandler;
use Adapter\Util\Calypso;
use app\services\HubService;
use yii\data\Pagination;
use Yii;
use yii\web\Response;

class ShipmentsController extends BaseController {

    public function actionAll($page=1,$search=false,$page_width=null)
    {
        $from_date = date('Y/m/d');
        $to_date = date('Y/m/d');
        $search_action = $search;

        $page_width = is_null($page_width) ? $this->page_width : $page_width;
        $offset = ($page-1)*$page_width;
        $parcel = new ParcelAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        if(isset(Calypso::getInstance()->get()->from,Calypso::getInstance()->get()->to)){
            $from_date = Calypso::getInstance()->get()->from.' 00:00:00';
            $to_date = Calypso::getInstance()->get()->to.' 23:59:59';
            $filter = isset(Calypso::getInstance()->get()->date_filter) ? Calypso::getInstance()->get()->date_filter : '-1';
            $response = $parcel->getFilterParcelsByDateAndStatus($from_date,$to_date,$filter,$offset,$this->page_width, 1);
            $search_action = true;
        }
        elseif(isset(Calypso::getInstance()->get()->search) ){
            $search = Calypso::getInstance()->get()->search;
            $response = $parcel->getSearchParcels('-1',$search,$offset,$this->page_width, 1);
            $search_action = true;
            $filter = null;
        }else{
            //$response = $parcel->getParcels(null,null,$offset,$this->page_width);
            $response = $parcel->getNewParcelsByDate(date('Y-m-d'),$offset,$this->page_width, 1);
            $search_action = false;
            $filter = null;
        }
        $response = new ResponseHandler($response);
        $data = [];
        $total_count = 0;
        if($response->getStatus() ==  ResponseHandler::STATUS_OK){
            $data = $response->getData();
            $total_count = $data['total_count'];
            $data = $data['parcels'];
        }
        return $this->render('all',array('filter'=>$filter,'parcels'=>$data,'from_date'=>$from_date,'to_date'=>$to_date,'offset'=>$offset,'page_width'=>$this->page_width,'search'=>$search_action,'total_count'=>$total_count));
    }

    public function actionFordelivery($page=1,$search=false,$page_width=null)
    {
        $from_date =  date('Y/m/d');
        $to_date = date('Y/m/d');
        $search_action = $search;
        $parcel = new ParcelAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        $page_width = is_null($page_width) ? $this->page_width : $page_width;
        $offset = ($page-1)*$page_width;
        if(\Yii::$app->request->isPost) {
            $rawData = \Yii::$app->request->post('waybills');
            $data = json_decode($rawData, true);
            $waybills = [];
            foreach ($data['waybills'] as $wb) {
                $waybills[] = $wb;
            }
            $record = [];
            $record['waybill_numbers'] = implode(",", $waybills);
            $record['held_by_id'] = Calypso::getValue($data, 'held_by_id', null);
            if(!isset($record['waybill_numbers'], $record['held_by_id'])) {
                $this->flashError("Invalid parameter(s) sent!");
            } else {
                $parcelData = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
                $response = $parcelData->moveToBeingDelivered($record);
                $data = $response['data'];
                $response = new ResponseHandler($response);
                if ($response->getStatus() === ResponseHandler::STATUS_OK && empty($data['bad_parcels'])) {
                    $this->flashSuccess('Shipments dispatched');
                } else {
                    $bad_parcels = $data['bad_parcels'];
                    foreach($bad_parcels as $key=>$bad_parcel){
                        $this->flashError($key.' - '.$bad_parcel);
                    }
                }
            }
        }
        if(isset(Calypso::getInstance()->get()->from,Calypso::getInstance()->get()->to)){
            $from_date = Calypso::getInstance()->get()->from.' 00:00:00';
            $to_date = Calypso::getInstance()->get()->to.' 23:59:59';
            $filter = isset(Calypso::getInstance()->get()->date_filter) ? Calypso::getInstance()->get()->date_filter : '-1';
            $response = $parcel->getFilterParcelsByDateAndStatus($from_date,$to_date,$filter,$offset,$page_width, 1);
            $search_action = true;
        }
        elseif(isset(Calypso::getInstance()->get()->search) ){
            $search = Calypso::getInstance()->get()->search;
            $response = $parcel->getSearchParcels('-1',$search,$offset,$page_width, 1);
            $search_action = true;
        }else{
            $user_session = Calypso::getInstance()->session("user_session");
            $response = $parcel->getParcels(null, null, ServiceConstant::FOR_DELIVERY,$user_session['branch_id'],$offset,$page_width, null, 1);
            $search_action = false;
        }
        $response = new ResponseHandler($response);
        $data = [];
        $total_count = 0;
        if($response->getStatus() ==  ResponseHandler::STATUS_OK){
            $data = $response->getData();
            $total_count = $data['total_count'];
            $data = $data['parcels'];
        }
        return $this->render('fordelivery',array('parcels'=>$data,'from_date'=>$from_date,'to_date'=>$to_date,'offset'=>$offset,'page_width'=>$page_width,'search'=>$search_action,'total_count'=>$total_count));
    }

    public function actionStaffcheck(){
        $this->enableCsrfValidation = false;

        $data = (Yii::$app->request->get());
        if($data){
            $admin = new AdminAdapter();
            $response = $admin->login($data['staff_id'],$data['password']);
            $response = new ResponseHandler($response);
            if($response->getStatus() == ResponseHandler::STATUS_OK){
                $data = $response->getData();
                if($data['role_id'] == ServiceConstant::USER_TYPE_DISPATCHER){
                    return $this->sendSuccessResponse($data['role_id']);
                }
                else{
                    return $this->sendErrorResponse('Access denied');
                }
            }else {
                return $this->sendErrorResponse('Invalid details', null);
            }
        }
    }

    public function actionForsweep($page=1,$search=false,$page_width=null)
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
                } else {
                    //Flash error message
                    $this->flashError($response['message']);
                }
            }
        }

        $from_date = date('Y/m/d');
        $to_date = date('Y/m/d');
        $search_action = $search;
        $page_width = is_null($page_width) ? $this->page_width : $page_width;
        $offset = ($page-1)*$page_width;
        $parcel = new ParcelAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        if(isset(Calypso::getInstance()->get()->from,Calypso::getInstance()->get()->to)){
            $from_date = Calypso::getInstance()->get()->from.' 00:00:00';
            $to_date = Calypso::getInstance()->get()->to.' 23:59:59';
            $filter = isset(Calypso::getInstance()->get()->date_filter) ? Calypso::getInstance()->get()->date_filter : '-1';
            $response = $parcel->getFilterParcelsByDateAndStatus($from_date,$to_date,$filter,$offset,$page_width, 1);
            $search_action = true;
        }
        elseif(isset(Calypso::getInstance()->get()->search)){
            $search = Calypso::getInstance()->get()->search;
            $response = $parcel->getSearchParcels('-1',$search,$offset,$page_width, 1);
            $search_action = true;
        }else{
            $response = $parcel->getParcels(null, null, ServiceConstant::FOR_SWEEPER,null,$offset,$page_width, null, 1);
            $search_action = false;
        }
        $response = new ResponseHandler($response);
        $data = [];
        $total_count = 0;
        if($response->getStatus() ==  ResponseHandler::STATUS_OK){
            $data = $response->getData();
            $total_count = $data['total_count'];
            $data = $data['parcels'];
        }
        return $this->render('forsweep',array('parcels'=>$data,'from_date'=>$from_date,'to_date'=>$to_date,'offset'=>$offset,'page_width'=>$page_width,'search'=>$search_action, 'total_count'=>$total_count));
    }

    public function actionProcessed($page=1,$search=false,$page_width=null)
    {
        $from_date = date('Y/m/d');
        $to_date = date('Y/m/d');
        $search_action = $search;
        if($page_width != null){
            $this->page_width = $page_width;
            Calypso::getInstance()->cookie('page_width',$page_width);
        }
        $offset = ($page-1)*$page_width;
        $parcel = new ParcelAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        if(isset(Calypso::getInstance()->get()->from,Calypso::getInstance()->get()->to)){
            $from_date = Calypso::getInstance()->get()->from.' 00:00:00';
            $to_date = Calypso::getInstance()->get()->to.' 23:59:59';
            $filter = isset(Calypso::getInstance()->get()->date_filter) ? Calypso::getInstance()->get()->date_filter : '-1';
            $response = $parcel->getFilterParcelsByDateAndStatus($from_date,$to_date,$filter,$offset,$this->page_width, 1);
            $search_action = true;
        }
        elseif(isset(Calypso::getInstance()->get()->search) ){
            $search = Calypso::getInstance()->get()->search;
            $response = $parcel->getSearchParcels('-1',$search,$offset,$this->page_width,1);
            $search_action = true;
            $filter = null;
        }else{
            $response = $parcel->getNewParcelsByDate(date('Y-m-d'),$offset,$this->page_width, 1);
            $search_action = false;
            $filter = null;
        }
        $response = new ResponseHandler($response);
        $data = [];
        $total_count = 0;
        if($response->getStatus() ==  ResponseHandler::STATUS_OK){
            $data = $response->getData();
            $total_count = $data['total_count'];
            $data = $data['parcels'];
        }
        return $this->render('processed',array('filter'=>$filter,'parcels'=>$data,'from_date'=>$from_date,'to_date'=>$to_date,'offset'=>$offset,'page_width'=>$this->page_width,'search'=>$search_action, 'total_count'=>$total_count));
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

        return $this->render('/hubs/manifest', $viewData);
    }


    public function actionCustomerhistory()
    {
        return $this->render('customer_history');
    }

    public function actionCustomerhistorydetails($page=1,$search=false)
    {
        $page_width=20;
        $offset=($page-1)*$page_width;
        $from_date = date('Y/m/d', 0);
        $to_date = date('Y/m/d');
        if (!$search) { //default, empty
            // display empty message
            $this->redirect('customerhistory');
        }
        $user = [];
        $data = [];
        $parcels = [];
        $total_count = 0;

        $userAdapter = new UserAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        $userResponse = new ResponseHandler($userAdapter->getUserDetails($search));

        if($userResponse->getStatus() ==  ResponseHandler::STATUS_OK){
            $user = $userResponse->getData();
            $user_id = $user['id'];
            $parcelAdapter = new ParcelAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
            $parcelResponse = new ResponseHandler($parcelAdapter->getParcelsByUser($user_id,$from_date,$to_date,$offset,$page_width));
            if($parcelResponse->getStatus() ==  ResponseHandler::STATUS_OK){
                $data = $parcelResponse->getData();
                $parcels = $data['parcels'];
                $total_count = $data['total_count'];
            }
        }
        $pagination = new Pagination(['totalCount'=>$total_count,'defaultPageSize'=>$page_width]);
        return $this->render('customer_history_details', array('user'=>$user, 'parcels'=>$parcels, 'total_count'=>$total_count, 'search'=>$search, 'offset'=>$offset, 'page_width'=>$page_width, 'pagination'=>$pagination));
    }

    public function actionView()
    {
        $data = [];
        $id = "-1";
        if(isset(Calypso::getInstance()->get()->id)){
            $id = Calypso::getInstance()->get()->id;
            $parcel = new ParcelAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
            $response = $parcel->getOneParcel($id);
            $response = new ResponseHandler($response);
            if($response->getStatus() == ResponseHandler::STATUS_OK){
                $data = $response->getData();
            }
        }
        return $this->render('view',array('parcelData'=>$data));
    }
    public function actionDispatched ($page=1, $page_width=null)
    {
        $page_width = is_null($page_width) ? $this->page_width : $page_width;
        $offset=($page-1)*$page_width;

        if(\Yii::$app->request->isPost) {
            $records = \Yii::$app->request->post();
            $password = $records['password'];
            $rawData = $records['waybills'];
            $task = $records['task'];

            if(empty($rawData) || empty($password) || empty($task)) {
                $this->flashError("Invalid parameter(s) sent!");
            } else {
                $admin = new AdminAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
                $temp = $admin->revalidate(null, $password);
                $temp = new ResponseHandler($temp);
                if($temp->getStatus() == ResponseHandler::STATUS_OK){
                    $data = json_decode($rawData, true);
                    $waybills = [];
                    foreach ($data as $wb) {
                        $waybills[] = $wb;
                    }
                    $record = [];
                    $record['waybill_numbers'] = implode(",", $waybills);

                    $parcelData = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
                    if($task == 'receive'){ }
                    elseif($task == 'deliver'){
                        $response = $parcelData->moveToDelivered($record);
                    }

                    $bad_parcels = $response['data']['bad_parcels'];
                    if ($response['status'] === ResponseHandler::STATUS_OK && !count($bad_parcels)) {
                        $this->flashSuccess('Shipments successfully delivered');
                    } else {
                        foreach($bad_parcels as $key=>$bad_parcel){
                            $this->flashError($key.' - '.$bad_parcel);
                        }
                    }
                }
                else{
                    $this->flashError($temp->getError());
                }
            }
        }
        $user_session = Calypso::getInstance()->session("user_session");
        $parcelsAdapter = new ParcelAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        $dispatch_parcels = $parcelsAdapter->getECDispatchedParcels($user_session['branch_id'],$offset,$page_width);
        $parcels = new ResponseHandler($dispatch_parcels);
        $total_count = 0;
        if($parcels->getStatus() ==  ResponseHandler::STATUS_OK){
            $data = $parcels->getData();
            $parcels = $data['parcels'];
            $total_count = $data['total_count'];
        }
        return $this->render('dispatched',array('parcels'=>$parcels, 'total_count'=>$total_count, 'offset'=>$offset, 'page_width'=>$page_width));
    }
    public function actionDelivered ($page=1, $page_width=null)
    {
        $page_width = is_null($page_width) ? $this->page_width : $page_width;
        $offset=($page-1)*$page_width;

        $from_date = date('Y/m/d');
        $to_date = date('Y/m/d');

        $user_session = Calypso::getInstance()->session("user_session");
        $parcelsAdapter = new ParcelAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        $delivered_parcels = $parcelsAdapter->getDeliveredParcels($user_session['branch_id'],$offset,$page_width);
        $parcels = new ResponseHandler($delivered_parcels);
        $total_count = 0;
        if($parcels->getStatus() ==  ResponseHandler::STATUS_OK){
            $data = $parcels->getData();
            $parcels = $data['parcels'];
            $total_count = $data['total_count'];
        }
        return $this->render('delivered', array('parcels'=>$parcels,'total_count'=>$total_count, 'offset'=>$offset, 'page_width'=>$page_width,'from_date'=>$from_date, 'to_date'=>$to_date));
    }
}
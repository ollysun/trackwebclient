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
use Adapter\RefAdapter;
use Adapter\RegionAdapter;
use Adapter\RequestHelper;
use Adapter\ResponseHandler;
use Adapter\Util\Calypso;
use app\services\HubService;
use yii\data\Pagination;

class ShipmentsController extends BaseController {

    private $page_width = 10;

    public function actionAll($offset=0,$search=false,$page_width=null)
    {
        $from_date = date('Y/m/d');
        $to_date = date('Y/m/d');
        $search_action = $search;
        if($page_width != null){
            $this->page_width = $page_width;
            Calypso::getInstance()->cookie('page_width',$page_width);
        }
        $parcel = new ParcelAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        if(isset(Calypso::getInstance()->get()->from,Calypso::getInstance()->get()->to)){
            $from_date = Calypso::getInstance()->get()->from.' 00:00:00';
            $to_date = Calypso::getInstance()->get()->to.' 23:59:59';
            $filter = isset(Calypso::getInstance()->get()->date_filter) ? Calypso::getInstance()->get()->date_filter : '-1';
            $response = $parcel->getFilterParcelsByDateAndStatus($from_date,$to_date,$filter,$offset,$this->page_width);
            $search_action = true;
        }
        elseif(isset(Calypso::getInstance()->get()->search) ){
            $search = Calypso::getInstance()->get()->search;
            $response = $parcel->getSearchParcels('-1',$search,$offset,$this->page_width);
            $search_action = true;
            $filter = null;
        }else{
            //$response = $parcel->getParcels(null,null,$offset,$this->page_width);
            $response = $parcel->getNewParcelsByDate(date('Y-m-d'),$offset,$this->page_width);
            $search_action = false;
            $filter = null;
        }
        $response = new ResponseHandler($response);
        $data = [];
        if($response->getStatus() ==  ResponseHandler::STATUS_OK){
            $data = $response->getData();
        }
        return $this->render('all',array('filter'=>$filter,'parcels'=>$data,'from_date'=>$from_date,'to_date'=>$to_date,'offset'=>$offset,'page_width'=>$this->page_width,'search'=>$search_action));
    }

    public function actionFordelivery($offset=0,$search=false)
    {
        $from_date =  date('Y/m/d');
        $to_date = date('Y/m/d');
        $search_action = $search;
        $parcel = new ParcelAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        if(isset(Calypso::getInstance()->get()->from,Calypso::getInstance()->get()->to)){
            $from_date = Calypso::getInstance()->get()->from.' 00:00:00';
            $to_date = Calypso::getInstance()->get()->to.' 23:59:59';
            $filter = isset(Calypso::getInstance()->get()->date_filter) ? Calypso::getInstance()->get()->date_filter : '-1';
            $response = $parcel->getFilterParcelsByDateAndStatus($from_date,$to_date,$filter,$offset,$this->page_width);
            $search_action = true;
        }
        elseif(isset(Calypso::getInstance()->get()->search) ){
            $search = Calypso::getInstance()->get()->search;
            $response = $parcel->getSearchParcels('-1',$search,$offset,$this->page_width);
            $search_action = true;
        }else{
            $response = $parcel->getParcels(null, null, ServiceConstant::FOR_DELIVERY,null,$offset,$this->page_width);
            $search_action = false;
        }
        $response = new ResponseHandler($response);
        $data = [];
        if($response->getStatus() ==  ResponseHandler::STATUS_OK){
            $data = $response->getData();
        }
        return $this->render('fordelivery',array('parcels'=>$data,'from_date'=>$from_date,'to_date'=>$to_date,'offset'=>$offset,'page_width'=>$this->page_width,'search'=>$search_action));
    }

    public function actionForsweep($offset=0,$search=false)
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
        $parcel = new ParcelAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        if(isset(Calypso::getInstance()->get()->from,Calypso::getInstance()->get()->to)){
            $from_date = Calypso::getInstance()->get()->from.' 00:00:00';
            $to_date = Calypso::getInstance()->get()->to.' 23:59:59';
            $filter = isset(Calypso::getInstance()->get()->date_filter) ? Calypso::getInstance()->get()->date_filter : '-1';
            $response = $parcel->getFilterParcelsByDateAndStatus($from_date,$to_date,$filter,$offset,$this->page_width);
            $search_action = true;
        }
        elseif(isset(Calypso::getInstance()->get()->search)){
            $search = Calypso::getInstance()->get()->search;
            $response = $parcel->getSearchParcels('-1',$search,$offset,$this->page_width);
            $search_action = true;
        }else{
            $response = $parcel->getParcels(null, null, ServiceConstant::FOR_SWEEPER,null,$offset,$this->page_width);
            $search_action = false;
        }
        $response = new ResponseHandler($response);
        $data = [];
        if($response->getStatus() ==  ResponseHandler::STATUS_OK){
            $data = $response->getData();
        }
        return $this->render('forsweep',array('parcels'=>$data,'from_date'=>$from_date,'to_date'=>$to_date,'offset'=>$offset,'page_width'=>$this->page_width,'search'=>$search_action));
    }

    public function actionProcessed($offset=0,$search=false,$page_width=null)
    {
        $from_date = date('Y/m/d');
        $to_date = date('Y/m/d');
        $search_action = $search;
        if($page_width != null){
            $this->page_width = $page_width;
            Calypso::getInstance()->cookie('page_width',$page_width);
        }
        $parcel = new ParcelAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        if(isset(Calypso::getInstance()->get()->from,Calypso::getInstance()->get()->to)){
            $from_date = Calypso::getInstance()->get()->from.' 00:00:00';
            $to_date = Calypso::getInstance()->get()->to.' 23:59:59';
            $filter = isset(Calypso::getInstance()->get()->date_filter) ? Calypso::getInstance()->get()->date_filter : '-1';
            $response = $parcel->getFilterParcelsByDateAndStatus($from_date,$to_date,$filter,$offset,$this->page_width);
            $search_action = true;
        }
        elseif(isset(Calypso::getInstance()->get()->search) ){
            $search = Calypso::getInstance()->get()->search;
            $response = $parcel->getSearchParcels('-1',$search,$offset,$this->page_width);
            $search_action = true;
            $filter = null;
        }else{
            $response = $parcel->getNewParcelsByDate(date('Y-m-d'),$offset,$this->page_width);
            $search_action = false;
            $filter = null;
        }
        $response = new ResponseHandler($response);
        $data = [];
        if($response->getStatus() ==  ResponseHandler::STATUS_OK){
            $data = $response->getData();
        }
        return $this->render('processed',array('filter'=>$filter,'parcels'=>$data,'from_date'=>$from_date,'to_date'=>$to_date,'offset'=>$offset,'page_width'=>$this->page_width,'search'=>$search_action));
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
        $sender_location = [];
        $receiver_location = [];
        $id = "-1";

        $refData = new RefAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());

        $serviceType = $refData->getShipmentType();
        $parcelType = $refData->getparcelType();
        $deliveryType = $refData->getdeliveryType();

        if(isset(Calypso::getInstance()->get()->id)){
            $id = Calypso::getInstance()->get()->id;
            $parcel = new ParcelAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
            $response = $parcel->getOneParcel($id);
            $response = new ResponseHandler($response);
            if($response->getStatus() == ResponseHandler::STATUS_OK){
                $data = $response->getData();
                if (isset($data['sender_address']) && isset($data['sender_address']['city_id'])) {
                    $city_id = $data['sender_address']['city_id'];
                    $regionAdp = new RegionAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
                    $sender_location = $regionAdp->getCity($city_id);
                }
                if (isset($data['receiver_address']) && isset($data['receiver_address']['city_id'])) {
                    $city_id = $data['receiver_address']['city_id'];
                    $regionAdp = new RegionAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
                    $receiver_location = $regionAdp->getCity($city_id);
                }
            }
        }

        return $this->render('view',array(
            'parcelData'=>$data,
            'serviceType'=>$serviceType,
            'parcelType'=>$parcelType,
            'deliveryType'=>$deliveryType,
            'senderLocation'=>$sender_location,
            'receiverLocation'=>$receiver_location
        ));
    }
    public function actionDispatched ()
    {
        return $this->render('dispatched',array());
    }
    public function actionDelivered ()
    {
        $from_date = date('Y/m/d');
        $to_date = date('Y/m/d');
        return $this->render('delivered', array('from_date'=>$from_date, 'to_date'=>$to_date));
    }
}
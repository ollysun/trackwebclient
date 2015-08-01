<?php
/**
 * Created by PhpStorm.
 * User: RotelandO
 * Date: 7/25/15
 * Time: 1:06 PM
 */

namespace app\controllers;


use Adapter\Globals\ServiceConstant;
use Adapter\ParcelAdapter;
use Adapter\RequestHelper;
use Adapter\ResponseHandler;
use Adapter\Util\Calypso;

class ShipmentsController extends BaseController {

    private $page_width = 5;

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
            $response = $parcel->getParcels(ServiceConstant::FOR_DELIVERY,null,$offset,$this->page_width);
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
        elseif(isset(Calypso::getInstance()->get()->search) ){
            $search = Calypso::getInstance()->get()->search;
            $response = $parcel->getSearchParcels('-1',$search,$offset,$this->page_width);
            $search_action = true;
        }else{
            $response = $parcel->getParcels(ServiceConstant::FOR_SWEEPER,null,$offset,$this->page_width);
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
}
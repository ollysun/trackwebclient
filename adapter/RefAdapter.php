<?php
namespace Adapter;

use Adapter\BaseAdapter;
use Adapter\Globals\ServiceConstant;

class RefAdapter extends BaseAdapter{


    public function getBanks(){
        return $this->request(ServiceConstant::URL_REF_BANK,array(),self::HTTP_GET);

    }
    public function getShipmentType(){
        return $this->request(ServiceConstant::URL_REF_SHIPMENT,array(),self::HTTP_GET);
    }
    public function getdeliveryType(){
        return $this->request(ServiceConstant::URL_REF_deliveryType,array(),self::HTTP_GET);
    }
    public function getparcelType(){
        return $this->request(ServiceConstant::URL_REF_parcelType,array(),self::HTTP_GET);
    }


}
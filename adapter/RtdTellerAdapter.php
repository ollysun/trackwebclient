<?php
namespace Adapter;

use Adapter\BaseAdapter;
use Adapter\Globals\ServiceConstant;

class RtdTellerAdapter extends BaseAdapter{

    public function addTeller($data){
        return $this->request(ServiceConstant::URL_RTD_TELLER_ADD, $data,self::HTTP_POST);
    }

    public function getTellers($filter){
        return $this->request(ServiceConstant::URL_RTD_TELLER_GET_ALL, $filter, self::HTTP_GET);
    }

    public function approveTeller($id){
        return $this->request(ServiceConstant::URL_RTD_TELLER_APPROVE, ['id' => $id], self::HTTP_POST);
    }

    public function declineTeller($id){
        return $this->request(ServiceConstant::URL_RTD_TELLER_DECLINE, ['id' => $id], self::HTTP_POST);
    }

}
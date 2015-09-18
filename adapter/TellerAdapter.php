<?php
namespace Adapter;

use Adapter\BaseAdapter;
use Adapter\Globals\ServiceConstant;

class TellerAdapter extends BaseAdapter{

    public function addTeller($data){
        return $this->request(ServiceConstant::URL_TELLER_ADD, $data,self::HTTP_POST);
    }

}
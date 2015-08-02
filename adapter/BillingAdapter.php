<?php
/**
 * Created by PhpStorm.
 * User: Icreatechub
 * Date: 8/1/2015
 * Time: 11:46 AM
 */

namespace Adapter;


use Adapter\Globals\ServiceConstant;

class BillingAdapter extends BaseAdapter
{
    public function addOnforwardingCharge($data){
        return $this->request(ServiceConstant::URL_ONFORWARDING_ADD, $data, self::HTTP_POST);
    }
    public function editOnforwardingCharge($data){
        return $this->request(ServiceConstant::URL_ONFORWARDING_EDIT, $data, self::HTTP_POST);
    }
    public function getOnforwardingCharge($id){
        return $this->request(ServiceConstant::URL_ONFORWARDING_FETCH_ID, $id, self::HTTP_GET);}

    public function addException(){}
    public function addPrincing(){}
}
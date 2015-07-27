<?php
namespace Adapter;

use Adapter\BaseAdapter;
use Adapter\Globals\ServiceConstant;

class RefAdapter extends BaseAdapter {


    public function getBanks(){
        return $this->request(ServiceConstant::URL_REF_BANK,array(),self::HTTP_GET);

    }
    public function getRoles(){
        return $this->request(ServiceConstant::URL_REF_ROLE,array(),self::HTTP_GET);

    }
    public function getBranch($state_id,$branch_type=null){
        return $this->request(ServiceConstant::URL_GET_ALL_BRANCH,['state_id'=>$state_id,'branch_type'=>$branch_type],self::HTTP_GET);
    }

    public function getBranchbyId($id){
        return $this->request(ServiceConstant::URL_BRANCH_GET_ONE,['branch_id'=>$id],self::HTTP_GET);
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

    /**
     * This fetches all states from the middleware
     */
    public function getStates($id) {
        return $this->request(ServiceConstant::URL_REF_STATES, [ 'country_id' => $id ], self::HTTP_GET);
    }

    /**
     * This function fetches all countries from the middleware
     */
    public function getCountries() {
        return $this->request(ServiceConstant::URL_REF_COUNTRIES, [], self::HTTP_GET);
    }

    public function getPaymentMethods() {
        return $this->request(ServiceConstant::URL_REF_PAYMENT_METHODS, [], self::HTTP_GET);
    }

    public function getRegions($country_id){
        return $this->request(ServiceConstant::URL_REF_REGIONS, ['country_id'=>$country_id], self::HTTP_GET);
    }
}
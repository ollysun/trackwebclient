<?php
namespace Adapter;

use Adapter\BaseAdapter;
use Adapter\Globals\ServiceConstant;

class RegionAdapter extends BaseAdapter
{
    public function addRegion($data) {
        return $this->request(ServiceConstant::URL_REGION_CREATE, $data, self::HTTP_POST);
    }
    public function editRegion($data, $task) {
        if($task == 'status')
            return $this->request(ServiceConstant::URL_REGION_STATUS, $data, self::HTTP_POST);
        else
            return $this->request(ServiceConstant::URL_REGION_EDIT, $data, self::HTTP_POST);
    }
    public function getRegionStates($region_id) {
        return $this->request(ServiceConstant::URL_REGION_STATE, $region_id, self::HTTP_POST);
    }
    public function mapState($data) {
        return $this->request(ServiceConstant::URL_REGION_STATE, $data, self::HTTP_POST);
    }

    public function addCity($data){
        return $this->request(ServiceConstant::URL_REGION_CITY_ADD, $data, self::HTTP_POST);
    }
    public function editCity($data){
        return $this->request(ServiceConstant::URL_REGION_CITY_EDIT, $data, self::HTTP_POST);
    }
    public function changeCityStatus($data){
        return $this->request(ServiceConstant::URL_REGION_CITY_STATUS, $data, self::HTTP_POST);
    }
    public function getCity($city_id){
        return $this->request(ServiceConstant::URL_REGION_CITY_GET_ONE, $city_id, self::HTTP_POST);
    }
    public function getAllCity($with_state=1,$with_region=1,$state_id=null){
        $filter = ''.($with_state? 'with_state&':'');
        $filter .= ($with_state? 'with_country':'');
        return $this->request(ServiceConstant::URL_REGION_CITY_GET_ALL .'?'.$filter, [], self::HTTP_GET);
    }
}
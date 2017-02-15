<?php
/**
 * Created by PhpStorm.
 * User: USER
 * Date: 2/10/2017
 * Time: 3:46 PM
 */

namespace Adapter;


use Adapter\Globals\ServiceConstant;

class IntlAdapter extends BaseAdapter
{
    public function getZones($filter){
        return $this->request(ServiceConstant::URL_INTL_ZONES_GET_ALL, $filter, self::HTTP_GET);
    }

    public function addZone($code, $description){
        return $this->request(ServiceConstant::URL_INTL_ADD_ZONE, ['code' => $code, 'description' => $description], self::HTTP_POST);
    }

    public function addCountryToZone($country_id, $zone_id){
        return $this->request(ServiceConstant::URL_INTL_ADD_COUNTRY_TO_ZONE,
            ['zone_id' => $zone_id, 'country_id' => $country_id], self::HTTP_POST);
    }

    public function getCountriesByZoneId($zone_id){
        return $this->request(ServiceConstant::URL_INTL_GET_COUNTRIES_BY_ZONE, ['zone_id' => $zone_id], self::HTTP_GET);
    }

    public function getWeightRange(){
        return $this->request(ServiceConstant::URL_INTL_GET_WEIGHT_RANGE, [], self::HTTP_GET);
    }

    public function addWeightRange(array $data){
        return $this->request(ServiceConstant::URL_INTL_ADD_WEIGHT_RANGE, $data, self::HTTP_POST);
    }

    public function editRange(array $data){
        return $this->request(ServiceConstant::URL_INTL_EDIT_WEIGHT_RANGE, $data, self::HTTP_POST);
    }

    public function saveTariff(array $data){
        return $this->request(ServiceConstant::URL_INTL_SAVE_TARIFF, $data, self::HTTP_POST);
    }

}
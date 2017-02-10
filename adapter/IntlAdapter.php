<?php
/**
 * Created by PhpStorm.
 * User: USER
 * Date: 2/10/2017
 * Time: 3:46 PM
 */

namespace adapter;


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

}
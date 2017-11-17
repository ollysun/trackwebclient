<?php
/**
 * Created by PhpStorm.
 * User: ELACHI
 * Date: 12/26/2016
 * Time: 11:47 PM
 */

namespace Adapter;


use Adapter\Globals\ServiceConstant;

class BusinessZoneAdapter extends BaseAdapter
{
    public function addBusinessZone($name, $region_id, $description){
        return $this->request(ServiceConstant::URL_BUSINESS_ZONE_ADD, ['region_id' => $region_id, 'name' => $name, 'description' => $description], self::HTTP_POST);
    }

    public function deleteBusinessZone($id){
        return $this->request(ServiceConstant::URL_BUSINESS_ZONE_DELETE, ['id' => $id], self::HTTP_POST);
    }

    public function getAll(array $filters = null)
    {
        $filter = '';
        if(!is_null($filters)){
            $filter = '?';
            foreach ($filters as $key => $value) {
                $value = urlencode($value);
                $filter .= "$key=$value&";
            }
        }
        //die($filter);
        return $this->request(ServiceConstant::URL_BUSINESS_ZONE_GET_ALL . $filter, [], self::HTTP_GET);
    }
}
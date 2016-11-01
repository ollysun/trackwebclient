<?php
/**
 * Created by PhpStorm.
 * User: ELACHI
 * Date: 10/30/2016
 * Time: 5:33 PM
 */

namespace Adapter;


use Adapter\Globals\ServiceConstant;

class BusinessManagerAdapter extends BaseAdapter
{
    public function addBusinessManager($region_id, $staff_id){
        return $this->request(ServiceConstant::URL_BUSINESS_MANAGER_ADD, ['region_id' => $region_id, 'staff_id' => $staff_id], self::HTTP_POST);
    }

    public function changeRegion($staff_id, $region_id){
        return $this->request(ServiceConstant::URL_BUSINESS_MANAGER_CHANGE_REGION, ['region_id' => $region_id, 'staff_id' => $staff_id], self::HTTP_POST);
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
        return $this->request(ServiceConstant::URL_BUSINESS_MANAGER_GET_ALL . $filter, [], self::HTTP_GET);
    }

}
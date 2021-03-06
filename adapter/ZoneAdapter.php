<?php
/**
 * Created by PhpStorm.
 * User: Icreatechub
 * Date: 7/30/2015
 * Time: 8:50 PM
 */

namespace Adapter;

namespace Adapter;

use Adapter\BaseAdapter;
use Adapter\Globals\ServiceConstant;

class ZoneAdapter extends BaseAdapter
{
    public function createZone($data)
    {
        return $this->request(ServiceConstant::URL_ZONES_ADD, $data, self::HTTP_POST);
    }
    public function editZone($data)
    {
        return $this->request(ServiceConstant::URL_ZONES_EDIT, $data, self::HTTP_POST);
    }
    public function getZones()
    {
        return $this->request(ServiceConstant::URL_ZONES_GET_ALL, [], self::HTTP_GET);
    }
    public function saveMatrix($data){
        return $this->request(ServiceConstant::URL_ZONES_MATRIX_SAVE, $data, self::HTTP_POST);
    }
    public function removeMatrix($data){
        return $this->request(ServiceConstant::URL_ZONES_MATRIX_REMOVE, $data, self::HTTP_POST);
    }
    public function getMatrix($zone_id=null,$branch_id=null,$other_branch_id=null){
        $filter = '';
        $filter .= empty($zone_id)?'':'zone_id='.$zone_id;
        $filter .= empty($branch_id)?'':'&branch_id='.$branch_id;
        $filter .= empty($other_branch_id)?'':'&other_branch_id='.$other_branch_id;
        return $this->request(ServiceConstant::URL_ZONES_MATRIX_SAVE.'?'.$filter, [], self::HTTP_GET);
    }


    //transit time
    public function getTransitTime()
    {
        return $this->request(ServiceConstant::URL_GET_TRANSIT_TIME, [], self::HTTP_GET);
    }

    public function saveTransitTime($data)
    {
        return $this->request(ServiceConstant::URL_SAVE_TRANSIT_TIME, $data, self::HTTP_POST);
    }

    public function removeTransitTime($data){
        return $this->request(ServiceConstant::URL_REMOVE_TRANSIT_TIME, $data, self::HTTP_POST);
    }

    //distance
    public function getDistanceTable()
    {
        return $this->request(ServiceConstant::URL_GET_DISTANCE, [], self::HTTP_GET);
    }

    public function saveDistance($data)
    {
        return $this->request(ServiceConstant::URL_SAVE_DISTANCE, $data, self::HTTP_POST);
    }

    public function removeDistance($data){
        return $this->request(ServiceConstant::URL_REMOVE_DISTANCE, $data, self::HTTP_POST);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: ELACHI
 * Date: 1/23/2017
 * Time: 3:46 PM
 */

namespace Adapter;


use Adapter\Globals\ServiceConstant;

class RemittanceAdapter extends BaseAdapter
{
    public function getAll($filter){
        return $this->request(ServiceConstant::URL_REMITTANCE_GET_ALL, $filter, self::HTTP_GET);
    }

    public function getOne($filter){
        return $this->request(ServiceConstant::URL_REMITTANCE_GET_ONE, $filter, self::HTTP_GET);
    }

    public function save($filter){
        return $this->request(ServiceConstant::URL_REMITTANCE_SAVE, $filter, self::HTTP_POST);
    }

}
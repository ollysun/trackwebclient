<?php
/**
 * Created by PhpStorm.
 * User: Icreatechub
 * Date: 7/30/2015
 * Time: 8:50 PM
 */

namespace Adapter;

use Adapter\BaseAdapter;
use Adapter\Globals\ServiceConstant;

class WeightRangeAdapter extends BaseAdapter
{
    public function createRange($data)
    {
        return $this->request(ServiceConstant::URL_WEIGHT_ADD, $data, self::HTTP_POST);
    }
    public function editRange($data, $task)
    {
        if($task == 'edit')
            return $this->request(ServiceConstant::URL_WEIGHT_EDIT, $data, self::HTTP_POST);
        elseif($task == 'status')
            return $this->request(ServiceConstant::URL_WEIGHT_CHANGE_STATUS, $data, self::HTTP_POST);
    }
    public function getRange()
    {
        return $this->request(ServiceConstant::URL_WEIGHT_FETCH_ALL, [], self::HTTP_GET);
    }
}
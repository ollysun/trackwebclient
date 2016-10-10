<?php
/**
 * Created by Ademu Anthony.
 * User: ELACHI
 * Date: 6/2/2016
 * Time: 12:03 AM
 */

namespace Adapter;

use Adapter\BaseAdapter;
use Adapter\Globals\ServiceConstant;

class AuditAdapter extends BaseAdapter
{
    public function getAudit($log_id)
    {
        return $this->request(ServiceConstant::URL_REGION_CITY_GET_ONE  . '?log_id=' . $log_id, [], self::HTTP_POST);
    }

    public function getAllAudit(array $filters = null)
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
        return $this->request(ServiceConstant::URL_AUDIT_GET_ALL . $filter, [], self::HTTP_GET);
    }
}
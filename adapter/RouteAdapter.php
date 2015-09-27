<?php
/**
 * Created by PhpStorm.
 * @author Olawale Lawal<wale@cottacush.com>
 */

namespace Adapter;

use Adapter\Globals\ServiceConstant;

class RouteAdapter extends BaseAdapter
{
    public function createRoute($data) {
        return $this->request(ServiceConstant::URL_ROUTE_ADD, $data, self::HTTP_POST);
    }

    public function getRoutes($branch_id=null){
        $filter = is_null($branch_id) ? '':'branch_id='.$branch_id;
        return $this->request(ServiceConstant::URL_ROUTE_GET_ALL.'?'.$filter, [], self::HTTP_GET);
    }

    public function editRoute($data) {
        return $this->request(ServiceConstant::URL_ROUTE_EDIT, $data, self::HTTP_POST);
    }
}
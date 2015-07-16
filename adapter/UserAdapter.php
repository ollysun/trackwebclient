<?php

namespace Adapter;

use Adapter\BaseAdapter;
use Adapter\Globals\ServiceConstant;

class UserAdapter extends BaseAdapter {

    public function getUserDetails($term)
    {
        return $this->request(ServiceConstant::URL_USER_BY_PHONE, [ 'phone' => $term ], self::HTTP_GET);
    }
}
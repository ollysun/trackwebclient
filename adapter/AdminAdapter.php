<?php
namespace Adapter;

use Adapter\BaseAdapter;
use Adapter\Globals\ServiceConstant;

class AdminAdapter extends BaseAdapter{


    public function login($identifier,$password){
        return $this->request(ServiceConstant::URL_ADMIN_LOGIN,array(
            'identifier' => $identifier,
            'password' => $password
        ),self::HTTP_POST);

    }

}
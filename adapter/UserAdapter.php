<?php

namespace Adapter;

use Adapter\BaseAdapter;
use Adapter\Globals\ServiceConstant;

class UserAdapter extends BaseAdapter {

    public function getUserDetails($term)
    {
        return $this->request(ServiceConstant::URL_USER_BY_PHONE, [ 'phone' => $term ], self::HTTP_GET);
    }

    public function createNewUser($role_id,$branch_id,$staff_id,$email,$fullname,$phone){

        return $this->request(ServiceConstant::URL_CREATE_USER, [
            'role_id' => $role_id,
            'branch_id' => $branch_id,
            'staff_id' => $staff_id,
            'email' => $email,
            'fullname' => $fullname,
            'phone' => $phone,
        ], self::HTTP_POST);
    }

    public function getStaffMembers(){
        return [];// $this->request(ServiceConstant::URL_USER_BY_PHONE, [ 'phone' => $term ], self::HTTP_GET);
    }
}
<?php

namespace Adapter;

use Adapter\BaseAdapter;
use Adapter\Globals\ServiceConstant;

class UserAdapter extends BaseAdapter {

    public function getUserDetails($term)
    {
        return $this->request(ServiceConstant::URL_USER_BY_PHONE, [ 'phone' => $term ], self::HTTP_GET);
    }

    public function createNewUser($role_id,$branch_id,$staff_id,$email,$fullname){
        /*
         * $role_id = $this->request->getPost('role_id');
        $branch_id = $this->request->getPost('branch_id');
        $staff_id = $this->request->getPost('staff_id');
        $email = $this->request->getPost('email');
        $fullname = $this->request->getPost('fullname');
        $phone = $this->request->getPost('phone');
        $password = '123456'; //auto-generated

         */
        return $this->request(ServiceConstant::URL_CREATE_USER, [
            'role_id' => $role_id,
            'branch_id' => $branch_id,
            'staff_id' => $staff_id,
            'email' => $email,
            'fullname' => $fullname,
        ], self::HTTP_POST);
    }
}
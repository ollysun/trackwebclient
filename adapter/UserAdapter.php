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

    public function getUserDetailsWithParcels($term)
    {
        return $this->request(ServiceConstant::URL_USER_BY_PHONE, [ 'phone' => $term, 'fetch_parcel'=>true ], self::HTTP_GET);
    }

    public function changePassword($post){
        return  $this->request(ServiceConstant::URL_USER_CHANGE_PASSWORD, $post, self::HTTP_POST);
    }

    public function changeStatus($status){
        return  $this->request(ServiceConstant::URL_USER_CHANGE_STATUS, $status, self::HTTP_POST);
    }

    public function revalidate($staff=null, $password){
        $filter = is_null($staff) ? '':'identifier='.$staff;
        $filter .= is_null($password) ? '':'&password='.$password;
        return  $this->request(ServiceConstant::URL_USER_VALIDATE.'?'.$filter, [], self::HTTP_POST);
    }
}
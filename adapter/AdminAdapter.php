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

    public function apiLogin($registration_number, $private_key){
        return $this->request(ServiceConstant::URL_ADMIN_LOGIN,array(
            'registration_number' => $registration_number,
            'private_key' => $private_key,
            'api_login' => 1
        ),self::HTTP_POST);
    }

    public function getStaffMembers($offset,$count,$role='-1'){
        $role_filter = $role == '-1'?'':'&role_id='.$role;
        return  $this->request(ServiceConstant::URL_GET_USERS.'&offset='.$offset.'&count='.$count.$role_filter, [], self::HTTP_GET);
    }
    public function searchStaffMembers($key,$is_email,$offset,$count){
        $role_filter = $is_email?'&email='.$key:'&staff_id='.$key;
        return  $this->request(ServiceConstant::URL_GET_USERS.'&offset='.$offset.'&count='.$count.$role_filter, [], self::HTTP_GET);
    }
    public function getStaffByStaffID($staff_id){
        return $this->request(ServiceConstant::URL_GET_STAFF_BY_ID,['staff_id'=>$staff_id],self::HTTP_GET);
    }

    public function getStaff($staff_code) {
        return  $this->request(ServiceConstant::URL_GET_USER.'&staff_id='.$staff_code, [], self::HTTP_GET);
    }

    public function revalidate($staff=null, $password){
        $filter = is_null($staff) ? '':'identifier='.$staff;
        $filter .= is_null($password) ? '':'&password='.$password;
        return  $this->request(ServiceConstant::URL_USER_VALIDATE.'?'.$filter, [], self::HTTP_POST);
    }

    public function getStatus($data) {
        return  $this->request(ServiceConstant::URL_GET_STATUS, $data, self::HTTP_POST);
    }

    public function setStatusNotification($data) {
        return  $this->request(ServiceConstant::URL_SAVE_STATUSNOTIFICATION, $data, self::HTTP_POST);
    }

    public function setSetting($data) {
        return  $this->request(ServiceConstant::URL_SAVE_SETTING, $data, self::HTTP_POST);
    }

    public function getSettings($data) {
        return  $this->request(ServiceConstant::URL_GET_SETTINGS, $data, self::HTTP_POST);
    }

}
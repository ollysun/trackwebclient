<?php

namespace Adapter;

use Adapter\BaseAdapter;
use Adapter\Globals\ServiceConstant;

class UserAdapter extends BaseAdapter
{

    public function getUserDetails($term)
    {
        return $this->request(ServiceConstant::URL_USER_BY_PHONE, ['phone' => $term], self::HTTP_GET);
    }

    public function createNewUser($role_id, $branch_id, $staff_id, $email, $fullname, $phone)
    {

        return $this->request(ServiceConstant::URL_CREATE_USER, [
            'role_id' => $role_id,
            'branch_id' => $branch_id,
            'staff_id' => $staff_id,
            'email' => $email,
            'fullname' => $fullname,
            'phone' => $phone,
        ], self::HTTP_POST);
    }

    /**
     * Updates a user
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $id
     * @param $role_id
     * @param $branch_id
     * @param $staff_id
     * @param $email
     * @param $fullname
     * @param $phone
     * @return array|mixed|string
     */
    public function updateUser($id, $role_id, $branch_id, $staff_id, $email, $fullname, $phone, $status)
    {
        return $this->request(ServiceConstant::URL_EDIT_USER, [
            'admin_id' => $id,
            'role_id' => $role_id,
            'branch_id' => $branch_id,
            'staff_id' => $staff_id,
            'email' => $email,
            'fullname' => $fullname,
            'phone' => $phone,
            'status' => $status,
        ], self::HTTP_POST);
    }

    public function getUserDetailsWithParcels($term)
    {
        return $this->request(ServiceConstant::URL_USER_BY_PHONE, ['phone' => $term, 'fetch_parcel' => true], self::HTTP_GET);
    }

    public function changePassword($post)
    {
        return $this->request(ServiceConstant::URL_USER_CHANGE_PASSWORD, $post, self::HTTP_POST);
    }

    public function changeStatus($status)
    {
        return $this->request(ServiceConstant::URL_USER_CHANGE_STATUS, $status, self::HTTP_POST);
    }

    public function revalidate($staff = null, $password)
    {
        $filter = is_null($staff) ? '' : 'identifier=' . $staff;
        $filter .= is_null($password) ? '' : '&password=' . $password;
        return $this->request(ServiceConstant::URL_USER_VALIDATE . '?' . $filter, [], self::HTTP_POST);
    }

    /**
     * Changes a user's password
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $userAuthId
     * @param $password
     * @return bool
     */
    public function resetPassword($userAuthId, $password)
    {
        $response = $this->request(ServiceConstant::URL_USER_RESET_PASSWORD, ['user_auth_id' => $userAuthId, 'password' => $password], self::HTTP_POST);
        $response = new ResponseHandler($response);

        if($response->getStatus() == ResponseHandler::STATUS_OK) {
            return true;
        } else {
            return $response->getError();
        }
    }

    /**
     * Changes a user's password
     * @author Ademu Anthony <ademuanthony@gmail.com>
     * @param $company_id
     * @param $password
     * @return ResponseHandler
     */
    public function resetCompanyAdminPassword($company_id, $password)
    {
        $response = $this->request(ServiceConstant::URL_USER_RESET_COMPANY_ADMIN_PASSWORD, ['company_id' => $company_id, 'password' => $password], self::HTTP_POST);
        $response = new ResponseHandler($response);
        return $response;

        if($response->getStatus() == ResponseHandler::STATUS_OK) {
            return true;
        } else {
            return $response->getError();
        }
    }



    /**
     * Initiates the forgot password process if email exists
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $email
     * @return bool
     */
    public function forgotPassword($email)
    {
        $response = $this->request(ServiceConstant::URL_USER_FORGOT_PASSWORD, ['identifier' => $email], self::HTTP_POST);
        $response = new ResponseHandler($response);

        if($response->getStatus() == ResponseHandler::STATUS_OK) {
            return true;
        } else {
            return $response->getError();
        }
    }

    /**
     * Validates the password reset token
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $token
     * @param $key
     * @return bool
     */
    public function validatePasswordResetToken($token, $key)
    {
        $response = $this->request(ServiceConstant::URL_USER_VALIDATE_PASSWORD_RESET_TOKEN, ['token' => $token, 'key' => $key], self::HTTP_POST);
        $response = new ResponseHandler($response);

        if($response->getStatus() == ResponseHandler::STATUS_OK) {
            return true;
        } else {
            return $response->getError();
        }
    }
}
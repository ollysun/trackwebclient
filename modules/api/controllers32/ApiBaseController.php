<?php
/**
 * Created by PhpStorm.
 * User: ELACHI
 * Date: 11/3/2016
 * Time: 1:24 PM
 */

namespace app\modules\api\controllers;

use Adapter\Globals\ServiceConstant;
use app\models\User;
use Yii;
use Adapter\AdminAdapter;
use Adapter\ResponseHandler;
use Adapter\Util\Calypso;
use app\controllers\BaseController;

class ApiBaseController extends BaseController
{
    const SuccessCode = 200;
    const BadRequest = 400;
    const NotFound = 404;
    const EmptyCredential = 401.4;
    const InvalidCredentialCode = 401.1;
    const AccessDeniedCode = 401;
    const InternalError = 500;

    protected $private_key;
    protected $registration_number;

    /**
     * @return mixed
     */
    public function getPrivateKey()
    {
        return empty($this->private_key)?$this->get('private_key'):$this->private_key;
    }

    private function login()
    {
        $registration_number = Yii::$app->request->get('registration_number');
        $private_key = Yii::$app->request->get('private_key');
        if(!$registration_number || !$private_key){
            return false;
            //return $this->sendErrorResponse('You must enter your registration number and private key', self::EmptyCredential);
        }
        $this->private_key = $private_key;
        $this->registration_number = $registration_number;

        $admin = new AdminAdapter();
        $response = $admin->apiLogin(Yii::$app->request->get('registration_number', null), Yii::$app->request->get('private_key', null));
        $response = new ResponseHandler($response);

        if ($response->getStatus() != ResponseHandler::STATUS_OK) {
           return false;
        }

        $data = $response->getData();

        $user_status = Calypso::getValue($data, 'status');


        if ($user_status == ServiceConstant::ACTIVE) {
            User::login($data);

            return true;

        }
        return false;
    }


    public function beforeAction($action)
    {
        if(!$this->login()){
            $data = ['status' => 'error', 'code' => self::InvalidCredentialCode, 'message' => 'Invalid API credential'];
            $this->sendErrorResponse('Access denied', self::InvalidCredentialCode);
            echo json_encode($data);
            return false;
        }
        return parent::beforeAction($action);
    }

}
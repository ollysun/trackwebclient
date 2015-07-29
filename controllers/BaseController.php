<?php
/**
 * Created by PhpStorm.
 * User: RotelandO
 * Date: 7/11/15
 * Time: 7:01 PM
 */

namespace app\controllers;


use \yii\web\Controller,
    \yii\web\Response;
use Adapter\Globals\HttpStatusCodes;
use Adapter\Util\Calypso;
use Adapter\Globals\ServiceConstant;

class BaseController extends Controller {
    /*
     * const USER_TYPE_ADMIN = 1;
        const USER_TYPE_OFFICER = 2;
        const USER_TYPE_SWEEPER = 3;
        const USER_TYPE_DISPATCHER = 4;

    These are the restricted pages for these users. This can be made dynamic
     * */
    private $permissionMap = [];
    protected function setPermissionMap(){
        $this->permissionMap = Calypso::getInstance()->permissionMap();
    }
    public function beforeAction($action){
        if(!in_array($action->id,array('logout','login','gerraout','site'))){
            $this->setPermissionMap();
            $s = Calypso::getInstance()->session('user_session');
            if(!$s){
                return $this->redirect(['site/gerraout']);
            }
            if(!array_key_exists($s['role_id'],$this->permissionMap)){
                \Yii::$app->getUser()->logout();
                Calypso::getInstance()->setPageData("Invalid Login. Check username and password and try again");
               return $this->redirect(['site/logout']);
            }
            $map = $this->permissionMap[$s['role_id']];
            $current = $action->controller->id;
            //Wild card
            if(in_array($current.'/*',$map)){
                \Yii::$app->getUser()->logout();
                Calypso::getInstance()->setPageData("Invalid Login. Check username and password and try again");
                return $this->redirect(['site/logout']);
            }

            if(in_array($current.'/'.$action->id,$map)){
                \Yii::$app->getUser()->logout();
                Calypso::getInstance()->setPageData("Invalid Login. Check username and password and try again");
                return $this->redirect(['site/logout']);
            }
        }
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    /**
     * Allow sending success response
     * @param $data
     */
    public function sendSuccessResponse($data)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        \Yii::$app->response->setStatusCode(200, HttpStatusCodes::getMessage(200));

        return [
            'status' => 'success',
            'data' => $data
        ];
    }

    /**
     * Allows sending error response
     * @param $message
     * @param $code
     * @param null $data
     * @param $http_status_code
     */
    public function sendErrorResponse($message, $code, $data = null, $http_status_code = 200)
    {

        \Yii::$app->response->format = Response::FORMAT_JSON;
        \Yii::$app->response->setStatusCode($http_status_code, HttpStatusCodes::getMessage($http_status_code));

        $response = [
            'status' => 'error',
            'message' => $message,
            'code' => $code
        ];

        if (!is_null($data)) {
            $response["data"] = $data;
        }

        return $response;
    }

    /**
     * @param $data
     * @param int $http_status_code
     */
    public function sendFailResponse($data, $http_status_code = 500)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        \Yii::$app->response->setStatusCode($http_status_code, HttpStatusCodes::getMessage($http_status_code));

        return [
            'status' => 'fail',
            'data' => $data
        ];
    }

    /**
     * This flashes error message and sends to the view
     * @param $message
     */
    public function flashError($message) {

        \Yii::$app->session->setFlash('danger', $message);
    }

    /**
     * This flashes success message and sends to the view
     * @param $message
     */
    public function flashSuccess($message) {

        \Yii::$app->session->setFlash('success', $message);
    }
}
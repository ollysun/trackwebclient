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

class BaseController extends Controller {

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
}
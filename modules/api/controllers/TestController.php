<?php
/**
 * Created by PhpStorm.
 * User: ELACHI
 * Date: 11/8/2016
 * Time: 1:33 PM
 */

namespace app\modules\api\modules\v1\controllers;


use app\modules\api\controllers\ApiBaseController;

class TestController extends ApiBaseController
{
    public function actionGet(){
        return $this->sendSuccessResponse(['message'=>'hey']);
    }

}
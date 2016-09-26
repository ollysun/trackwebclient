<?php
/**
 * Created by PhpStorm.
 * User: ELACHI
 * Date: 9/15/2016
 * Time: 3:25 PM
 */

namespace app\controllers;


class ApiController extends BaseController
{

    public function actionCreateshipment()
    {
        return $this->sendSuccessResponse(['message' => 'hi']);
    }

}
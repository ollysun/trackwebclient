<?php
/**
 * Created by PhpStorm.
 * User: epapa
 * Date: 5/2/15
 * Time: 8:20 AM
 */

namespace app\libs;


class RequestHelper {

    /**
     * @return mixed
     */
    public static function getAccessToken()
    {
        return \Yii::$app->getSession()->get("access_token");
    }

    /**
     * @param mixed $accessToken
     */
    public static function setAccessToken($accessToken)
    {
        \Yii::$app->getSession()->set("access_token", $accessToken);
    }

}
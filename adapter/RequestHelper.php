<?php

namespace Adapter;


use Adapter\Util\Calypso;

class RequestHelper {

    /**
     * @return mixed
     */
    public static function getAccessToken()
    {
        //return \Yii::$app->getSession()->get("access_token");
        return Calypso::getInstance()->session("access_token");
    }

    /**
     * @param mixed $accessToken
     */
    public static function setAccessToken($accessToken)
    {
        //\Yii::$app->getSession()->set("access_token", $accessToken);
        Calypso::getInstance()->session("access_token", $accessToken);
    }
    public static function setClientID($client_id){
        //\Yii::$app->getSession()->set("client_id", $client_id);
        Calypso::getInstance()->session("client_id", $client_id);
    }
    public static function getClientID()
    {
        return Calypso::getInstance()->session("client_id");
       // return \Yii::$app->getSession()->get("client_id");
    }

}
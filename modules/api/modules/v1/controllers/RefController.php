<?php
/**
 * Created by PhpStorm.
 * User: ELACHI
 * Date: 11/4/2016
 * Time: 10:25 AM
 */

namespace app\modules\api\modules\v1\controllers;


use Adapter\BranchAdapter;
use Adapter\RefAdapter;
use Adapter\RegionAdapter;
use Adapter\RequestHelper;
use Adapter\ResponseHandler;
use app\modules\api\controllers\ApiBaseController;

class RefController extends ApiBaseController
{
    public function actionGetcountries(){
        $refAdapter = new RefAdapter();
        $response = new ResponseHandler($refAdapter->getCountries());
        return $response->isSuccess()?$this->sendSuccessResponse($response->getData()):$this->sendErrorResponse('Error in loading countries', self::InternalError);
    }

    public function actionGetstates($country_id = 1){
        $refAdapter = new RefAdapter();
        $response = new ResponseHandler($refAdapter->getStates($country_id));
        return $response->isSuccess()?$this->sendSuccessResponse($response->getData()):$this->sendErrorResponse('Error in loading states', self::InternalError);
    }

    public function actionGetcities($state_id = null){
        $regionAdapter = new RegionAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $response = new ResponseHandler($regionAdapter->getAllCity(1, 0, $state_id, 0));
        return $response->isSuccess()?$this->sendSuccessResponse($response->getData()):
            $this->sendErrorResponse('Error in loading cities '.$response->getError(), self::InternalError);
    }

    public function actionGethubs(){
        $hubAdp = new BranchAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $hubs = $hubAdp->getHubs();
        $hubs = new ResponseHandler($hubs);
        return $hubs->isSuccess()?$this->sendSuccessResponse($hubs->getData()):$this->sendErrorResponse('Error in loading hubs', self::InternalError);
    }
}
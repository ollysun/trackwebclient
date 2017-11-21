<?php
/**
 * @author Adegoke Obasa <goke@cottacush.com>
 */

namespace app\modules\corporate\controllers;


use Adapter\RegionAdapter;
use Adapter\RequestHelper;
use Adapter\ResponseHandler;
use Adapter\Util\ResponseCodes;
use Adapter\Util\ResponseMessages;
use app\controllers\BaseController;
use Yii;
use yii\helpers\Url;

class LocationsController extends BaseController
{
    /**
     * Returns JSON of cities
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return \yii\web\Response
     */
    public function actionCities()
    {
        if (!Yii::$app->request->isAjax) {
            return $this->redirect(Url::toRoute("/corporate"));
        }

        $stateId = Yii::$app->request->get('state_id');

        if (is_null($stateId)) {
            $this->sendErrorResponse(ResponseMessages::INVALID_PARAMETERS, ResponseCodes::INVALID_PARAMETERS, null, 400);
        }

        $regionAdapter = new RegionAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $cities = (new ResponseHandler($regionAdapter->getAllCity(1, 0, $stateId, 0)))->getData();

        return $this->sendSuccessResponse($cities);
    }
}
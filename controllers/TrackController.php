<?php

namespace app\controllers;

use Adapter\Globals\ServiceConstant;
use Adapter\RequestHelper;
use Adapter\TrackAdapter;
use Adapter\Util\Calypso;
use yii\helpers\HtmlPurifier;


/**
 * Class TrackController
 * @author Adeyemi Olaoye <yemi@cottacush.com>
 * @package app\controllers
 */
class TrackController extends BaseController
{

    /**
     * Overwrite before action in BaseController
     * @inheritdoc
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     */
    public function beforeAction($action)
    {
        $this->layout = 'tracking';
        if ($this->isUserLoggedIn()) {
            return parent::beforeAction($action);
        } else {
            return true;
        }
    }

    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     */
    public function actionIndex()
    {
        $tracking_number = \Yii::$app->request->getQueryParam('query', '');
        $tracking_number = HtmlPurifier::process($tracking_number);
        $tracking_number = trim($tracking_number);
        $tracking_number = str_replace(' ', '', $tracking_number);

        if (isset($tracking_number) && strlen($tracking_number) > 0) {
            $trackAdapter = new TrackAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
            $trackingInfo = $trackAdapter->getTrackingInfo($tracking_number);

            $currentStateInfo = null;
            if ($trackingInfo) {
                if (count($trackingInfo) == 1) {
                    $trackingInfo = array_values($trackingInfo)[0];
                    $history = Calypso::getValue($trackingInfo, 'history', []);
                    $currentStateInfo = $history[count($history) - 1];
                    $history = TrackAdapter::processHistory($history);
                    $trackingInfo['history'] = $history;
                } else {
                    return $this->render('track_search_details', ['tracking_infos' => $trackingInfo]);
                }
            }
            return $this->render('track',
                [
                    'tracking_number' => Calypso::getValue($trackingInfo, 'parcel.waybill_number', $tracking_number),
                    'tracking_info' => $trackingInfo,
                    'current_state_info' => $currentStateInfo
                ]);
        }
        return $this->render('track_search', ['tracking_number' => $tracking_number]);
    }
}
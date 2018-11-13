<?php

namespace app\controllers;

use Adapter\ExportedParcelAdapter;
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

        //handle imported parcel tracking
        $tracking_number = \Yii::$app->request->getQueryParam('query', '');
        $tracking_number = HtmlPurifier::process($tracking_number);
        $tracking_number = trim($tracking_number);
        $tracking_number = str_replace(' ', '', $tracking_number);
        $tracking_number = rtrim($tracking_number,',');
        $count = count(explode(',', $tracking_number));
        if ($count > 20) {
            return $this->render('track',
                [
                    'tracking_info' => $trackingInfoList = null,
                    'current_state_info' => $currentStateInfo = null,
                    'count' => $count
                ]);
        }

        $trackAdapter = new TrackAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());

        if($trackAdapter->getHistoryProvider($tracking_number) == TrackAdapter::ARAMEX){
            return $this->trackAramex($tracking_number, $tracking_number);
        }

        //handle imported parcel
        if(Calypso::isImportedTrackingNumber($tracking_number)){
            return $this->trackImportedParcel($tracking_number);
        }


        if (isset($tracking_number) && strlen($tracking_number) > 0) {
            $tracking_number  = implode(',', preg_split('/\r\n|[\r\n]/', $tracking_number));


            $trackingInfoList = $trackAdapter->getTrackingInfo($tracking_number);
            $export_agent_id = Calypso::getValue($trackingInfoList, 'export_agent_id');
            if($export_agent_id){
                $agent_tracking_number = Calypso::getValue($trackingInfoList, 'agent_tracking_number');
                switch($export_agent_id){
                    case ExportedParcelAdapter::AGENT_ARAMEX:
                        return $this->trackAramex($agent_tracking_number, $tracking_number);
                    case ExportedParcelAdapter::AGENT_UPS:
                        return $this->trackUpsParcel($agent_tracking_number, $tracking_number);
                    default:
                        return $this->trackExportParcel($tracking_number);
                }
            }

            //get the history by loop because of manual waybill number
            if(is_array($trackingInfoList)){
                $first_history = $trackingInfoList[array_keys($trackingInfoList)[0]];
                if($tracking_number != '2N20600855946' &&  count($trackingInfoList) === 1){
                    if(isset($first_history['is_exported']) && $first_history['is_exported']){
                        return $this->trackExportParcel($tracking_number);
                    }elseif (isset($first_history['is_aramex_exported']) && $first_history['is_aramex_exported']){
                        return $this->trackAramex($first_history['parcel']['order_number'], $tracking_number);
                    }
                }
            }


            $currentStateInfo = null;
            if ($trackingInfoList) {
                $currentStateInfo = [];

                foreach ($trackingInfoList as $key => $value) {
                    $history = Calypso::getValue($trackingInfoList[$key], 'history', []);

                   $currentStateInfo[$key] = count($history) > 0? $history[count($history) - 1]:[];
                    $history = TrackAdapter::processHistory($history);
                    $trackingInfoList[$key]['history'] = $history;
                }
            }


            return $this->render('track',
                [
                    'tracking_number' => Calypso::getValue($trackingInfoList, 'parcel.waybill_number', $tracking_number),
                    'tracking_info_list' => $trackingInfoList,
                    'current_state_info_list' => $currentStateInfo,
                    'count' => $count
                ]);
        }
        return $this->render('track_search', ['tracking_number' => $tracking_number]);
    }

    public function trackImportedParcel($tracking_number){
        $trackAdapter = new TrackAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $tracking_info = $trackAdapter->getImportedParcelTrackingInfo($tracking_number);


        return $this->render('track_imported_parcel', ['tracking_info' => $tracking_info, 'tracking_number' => $tracking_number]);
    }

    public function trackExportParcel($tracking_number){
        return $this->render('track_exported_parcel', ['tracking_number' => $tracking_number]);
    }

    public function trackAramex($aramex_number, $tracking_number){
        $trackAdapter = new TrackAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $tracking_info = $trackAdapter->trackAramex($aramex_number);

        return $this->render('track_aramex', ['tracking_info' => $tracking_info, 'tracking_number' => $tracking_number]);
    }

    public function trackUpsParcel($ups_number, $tracking_number){
        $trackAdapter = new TrackAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $tracking_info = $trackAdapter->trackUps($ups_number);
        //dd($tracking_info);
        //dd($tracking_info['TrackResponse']['Shipment']['Package']['Activity']);

        return $this->render('track_ups', ['tracking_info' => $tracking_info, 'tracking_number' => $tracking_number]);
    }


}

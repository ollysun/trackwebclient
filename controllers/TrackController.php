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
            return $this->trackAramex($tracking_number);
        }

        //handle imported parcel
        if(Calypso::isImportedTrackingNumber($tracking_number)){
            return $this->trackImportedParcel($tracking_number);
        }

        if (isset($tracking_number) && strlen($tracking_number) > 0) {
            $tracking_number  = implode(',', preg_split('/\r\n|[\r\n]/', $tracking_number));


            $trackingInfoList = $trackAdapter->getTrackingInfo($tracking_number);

            //dd($trackingInfoList);

            if($tracking_number != '2N20600855946' &&  count($trackingInfoList) === 1){
                if(isset($trackingInfoList[$tracking_number]['is_exported']) && $trackingInfoList[$tracking_number]['is_exported']){
                    return $this->trackExportParcel($tracking_number);
                }
            }

            $currentStateInfo = null;
            if ($trackingInfoList) {
                $currentStateInfo = [];

                foreach ($trackingInfoList as $key => $value) {
                    $history = Calypso::getValue($trackingInfoList[$key], 'history', []);

                    $currentStateInfo[$key] = count($history) > 0? $history[count($history) - 1]:[];
                    $history = TrackAdapter::processHistory($history);

                    if($tracking_number == '00004039' || $tracking_number == '2N20600855946'){
                        $history[] = [
                            'id' => '28146421',
                              'parcel_id' => '855946',
                              'from_branch_id' => '219',
                              'to_branch_id' => '2191',
                              'admin_id' => '526',
                              'status' => '5',
                              'created_date' => '2016-11-21 14:16:33',
                              'description' => 'Parcel is in transit',
                            'from_branch' => [
                                'id' => '219',
                                'name' => 'lagos mainland hub',
                                'code' => 'hub219',
                                'branch_type' => '2',
                                'state_id' => '25',
                                'address' => '1A Olabode Street Ajao Estate Isolo',
                                'created_date' => '2016-02-29 14:05:53',
                                'modified_date' => '2016-03-02 11:34:41' ,
                                'status' => '1' ,
                            ],
                            'to_branch' => [
                                'id' => '2191',
                                'name' => 'Krailling,Germany',
                                'code' => 'hub219',
                                'branch_type' => '2',
                                'state_id' => '25',
                                'address' => 'Krailling,Germany',
                                'created_date' => '2016-02-29 14:05:53',
                                'modified_date' => '2016-03-02 11:34:41' ,
                                'status' => '1' ,
                            ],
                            'type' => 'transitional'
                        ];
                    }


                    $trackingInfoList[$key]['history'] = $history;
                }


                /*

                if (count($trackingInfoList) == 1) {
                    $trackingInfoList = array_values($trackingInfoList)[0];

                    $history = Calypso::getValue($trackingInfoList, 'history', []);
                    $currentStateInfo = $history[count($history) - 1];
                    $history = TrackAdapter::processHistory($history);

                    $trackingInfoList['history'] = $history;
                } else {
                    return $this->render('track_search_details', ['tracking_infos' => $trackingInfoList]);
                }
                */
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

    public function trackAramex($tracking_number){
        $trackAdapter = new TrackAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $tracking_info = $trackAdapter->trackAramex($tracking_number);

        return $this->render('track_aramex', ['tracking_info' => $tracking_info, 'tracking_number' => $tracking_number]);
    }

}

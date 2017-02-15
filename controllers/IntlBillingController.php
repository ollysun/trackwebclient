<?php
namespace app\controllers;

use Adapter\BillingAdapter;
use Adapter\BillingPlanAdapter;
use Adapter\BusinessManagerAdapter;
use Adapter\CompanyAdapter;
use Adapter\Globals\ServiceConstant;
use Adapter\IntlAdapter;
use Adapter\Util\Calypso;
use Adapter\Util\Util;
use Adapter\WeightRangeAdapter;
use Adapter\BranchAdapter;
use Adapter\ZoneAdapter;
use app\services\BillingService;
use Yii;
use Adapter\RegionAdapter;
use Adapter\RefAdapter;
use Adapter\RequestHelper;
use Adapter\ResponseHandler;
use Adapter\Util\Response;
use yii\helpers\Url;

/**
 * Class IntlbillingController
 * @author Benedict Happy <happy_benedict@superfluxnigeria.com>
 * @package app\controllers
 */
class IntlbillingController extends BaseController
{
    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionIndex(){
        if (Yii::$app->request->isPost) {
            $entry = Yii::$app->request->post();
            $task = Calypso::getValue(Yii::$app->request->post(), 'task', '');
            $error = [];

            $data = [];
            $data['description'] = Calypso::getValue($entry, 'zone_desc');
            $data['code'] = Calypso::getValue($entry, 'zone_code');
            $data['zone_'] = Calypso::getValue($entry, 'zone_id');
            $data['country_'] = Calypso::getValue($entry, 'country_id');

            if ((($task == 'create') && (empty($data['description']) || empty($data['code']))) || (($task == 'addcountry') &&
                    (empty($data['zone_']) || empty($data['country_'])))) {
                $error[] = "All details are required!";
            }

            if (!empty($error)) {
                $errorMessages = implode('<br />', $error);
                Yii::$app->session->setFlash('danger', $errorMessages);
            } else {
                $zone = new IntlAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
                if ($task == 'create') {
                    $response = $zone->addZone($data['code'],$data['description']);
                    if ($response['status'] === Response::STATUS_OK) {
                        Yii::$app->session->setFlash('success', 'Intl. Zone has been created successfully.');
                    } else {
                        Yii::$app->session->setFlash('danger', 'There was a problem creating the zone.' . $response['message']);
                    }
                } else {
                    $response = $zone->addCountryToZone($data['country_'],$data['zone_']);
                    if ($response['status'] === Response::STATUS_OK) {
                        Yii::$app->session->setFlash('success', 'Country added to Zone successfully.');
                    } else {
                        Yii::$app->session->setFlash('danger', 'There was a problem adding country to the zone' . $response['message']);
                    }
                }
            }
        }
        $zAdp = new IntlAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $zones = $zAdp->getZones([]);
        $zones = new ResponseHandler($zones);
        $zones_list = $zones->getStatus() == ResponseHandler::STATUS_OK ? $zones->getData() : [];

        $refAdapter = new RefAdapter();
        $response = new ResponseHandler($refAdapter->getCountries());
        if($response->isSuccess()){
            $countries = $response->getData();
        }else $countries = [];
        //dd($zones_list);
        return $this->render('zones', array('zones' => $zones_list, 'countries' => $countries));
    }

    public function actionZones()
    {
        if (Yii::$app->request->isPost) {
            $entry = Yii::$app->request->post();
            $task = Calypso::getValue(Yii::$app->request->post(), 'task', '');
            $error = [];

            $data = [];
            $data['description'] = Calypso::getValue($entry, 'zone_desc');
            $data['code'] = Calypso::getValue($entry, 'zone_code');
            $data['zone_'] = Calypso::getValue($entry, 'zone_id');
            $data['country_'] = Calypso::getValue($entry, 'country_id');

            if ((($task == 'create') && (empty($data['description']) || empty($data['code']))) || (($task == 'addcountry') &&
                        (empty($data['zone_']) || empty($data['country_'])))) {
                $error[] = "All details are required!";
            }

            if (!empty($error)) {
                $errorMessages = implode('<br />', $error);
                Yii::$app->session->setFlash('danger', $errorMessages);
            } else {
                $zone = new IntlAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
                if ($task == 'create') {
                    $response = $zone->addZone($data['code'],$data['description']);
                    if ($response['status'] === Response::STATUS_OK) {
                        Yii::$app->session->setFlash('success', 'Intl. Zone has been created successfully.');
                    } else {
                        Yii::$app->session->setFlash('danger', 'There was a problem creating the zone.' . $response['message']);
                    }
                } else {
                    $response = $zone->addCountryToZone($data['country_'],$data['zone_']);
                    if ($response['status'] === Response::STATUS_OK) {
                        Yii::$app->session->setFlash('success', 'Country added to Zone successfully.');
                    } else {
                        Yii::$app->session->setFlash('danger', 'There was a problem adding country to the zone' . $response['message']);
                    }
                }
            }
        }
        $zAdp = new IntlAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $zones = $zAdp->getZones([]);
        $zones = new ResponseHandler($zones);
        $zones_list = $zones->getStatus() == ResponseHandler::STATUS_OK ? $zones->getData() : [];

        $refAdapter = new RefAdapter();
        $response = new ResponseHandler($refAdapter->getCountries());
        if($response->isSuccess()){
            $countries = $response->getData();
        }else $countries = [];
        return $this->render('zones', array('zones' => $zones_list, 'countries' => $countries));
    }

    public function actionCountriesbyzone()
    {
        $zone_id = Yii::$app->request->get('zone_id');
        $adapter = new IntlAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $response = new ResponseHandler($adapter->getCountriesByZoneId($zone_id));
        if($response->isSuccess()) $countries = $response->getData();
        else $countries = [];
        return $this->sendSuccessResponse($countries);
    }

    public function actionWeightranges()
    {
        $get_ranges = Yii::$app->request->get();
        if (Yii::$app->request->isPost) {
            $entry = Yii::$app->request->post();
            $task = Calypso::getValue(Yii::$app->request->post(), 'task', '');
            $error = [];

            $data = [];
            $data['min_weight'] = Calypso::getValue($entry, 'min_weight', null);
            $data['increment_weight'] = Calypso::getValue($entry, 'increment_weight', null);
            $data['max_weight'] = Calypso::getValue($entry, 'max_weight', null);
            $data['weight_range_id'] = Calypso::getValue($entry, 'id', null);
            $data['billing_plan_id'] = Calypso::getValue($entry, 'billing_plan_id', BillingPlanAdapter::DEFAULT_WEIGHT_RANGE_PLAN);

            if (($task == 'create' || $task == 'edit') && (Util::checkEmpty($data['min_weight']) || Util::checkEmpty($data['max_weight']))) {
                $error[] = "All details are required!";
            }
            if (!empty($error)) {
                $errorMessages = implode('<br />', $error);
                Yii::$app->session->setFlash('danger', $errorMessages);
            } else {
                $adp = new IntlAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
                if ($task == 'create') {
                    $response = $adp->addWeightRange($data);
                    if ($response['status'] === Response::STATUS_OK) {
                        Yii::$app->session->setFlash('success', 'Weight range has been created successfully.');
                    } else {
                        Yii::$app->session->setFlash('danger', 'There was a problem creating the weight range. ' . $response['message']);
                    }
                } else {
                    $response = $adp->editRange($data);
                    if ($response['status'] === Response::STATUS_OK) {
                        Yii::$app->session->setFlash('success', 'Weight range has been edited successfully.');
                    } else {
                        Yii::$app->session->setFlash('danger', 'There was a problem editing the weight range. ' . $response['message']);
                    }
                }
            }

            return $this->refresh();
        }
        $data_source = new IntlAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $response = $data_source->getWeightRange();
        $response = new ResponseHandler($response);

        if(!$response->isSuccess()) $this->flashError($response->getError());
        /*if($ranges->isSuccess()) $wranges = $ranges->getData();
        else $wranges = [];*/

        $ranges_list = $response->getStatus() == ResponseHandler::STATUS_OK ? $response->getData() : [];


        return $this->render('weight_ranges', array('ranges' => $ranges_list));
    }

}
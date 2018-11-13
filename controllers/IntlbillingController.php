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

    /**
     * Zones View
     * @author Rotimi Akintewe <akintewe.rotimi@gmail.com>
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return string
     */
    public function actionZones()
    {
        if (Yii::$app->request->isPost) {
            $entry = Yii::$app->request->post();
            $task = Calypso::getValue(Yii::$app->request->post(), 'task', '');
            $error = [];

            $data = [];
            $data['code'] = Calypso::getValue($entry, 'zone_code');
            $data['description'] = Calypso::getValue($entry, 'zone_desc');
            $data['extra'] = Calypso::getValue($entry, 'extra_percent_on_import');
            $data['zone'] = Calypso::getValue($entry, 'zone_id');
            $data['country_'] = Calypso::getValue($entry, 'country_id');
            $data['sign'] = Calypso::getValue($entry, 'sign');


            if ((($task == 'create' || $task == 'edit') && (empty($data['description']) || empty($data['code']))) || (($task == 'addcountry') &&
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
                }elseif ($task == 'edit'){
                   $response=$zone->updateZone($data['zone'],$data['code'],$data['description'],$data['extra'],$data['sign']);
                    if ($response['status'] === Response::STATUS_OK) {
                        Yii::$app->session->setFlash('success', 'Intl. Zone has been edited successfully.');
                    } else {
                        Yii::$app->session->setFlash('danger', 'There was a problem editing the zone.' . $response['message']);
                    }
                }
                else {
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

    public function actionGetzones(){
        $entry = Yii::$app->request->get();
        $data = [];
        $data['id']=Calypso::getValue($entry, 'id', null);
        $zon = new IntlAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $response=$zon->getZones($data);
        return $response;
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
            $data['billing_plan_id'] = Calypso::getValue($entry, 'billing_plan_id', BillingPlanAdapter::getDefaultBillingPlan());

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
                    $edit['min_weight'] = Calypso::getValue($entry, 'min_weight', null);
                    $edit['max_weight'] = Calypso::getValue($entry, 'max_weight', null);
                    $edit['id'] = Calypso::getValue($entry, 'id', null);
                    //dd($edit);
                    $response = $adp->editRange($edit);
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


    /**
     * Pricing View
     * @author Rotimi Akintewe <akintewe.rotimi@gmail.com>
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return string
     */
    public function actionPricing()
    {
        $viewBag = [
            'billings' => [],
            'zones' => [],
            'weightRanges' => []
        ];

        $billingAdp = new IntlAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $billings = $billingAdp->fetchAllBilling();
        if ($billings['status'] == ResponseHandler::STATUS_OK) {
            $viewBag['billings'] = $billings['data'];
        }
        $response = new ResponseHandler($billingAdp->getZones([]));
        if($response->isSuccess()){
            $viewBag['zones'] = $response->getData();
        }

        $response = new ResponseHandler($billingAdp->getWeightRange());
        if($response->isSuccess()){
            $viewBag['weightRanges'] = $response->getData();
        }

        $billingMatrix = $this->buildPricingTable($viewBag);


        return $this->render('pricing',
            [
                'billingMatrix' => $billingMatrix,
                'weightRanges' => $viewBag['weightRanges'],
                'zones' => $viewBag['zones']
            ]);
    }

    private function buildPricingTable($pricingData)
    {
        $matrix = [];
        $zones = [];
        foreach ($pricingData['zones'] as $zone) {
            $zones[$zone['id']] = $zone;
        }

        $weightRanges = [];
        foreach ($pricingData['weightRanges'] as $weightRange) {
            $weightRanges[$weightRange['id']] = $weightRange;
        }

        foreach ($pricingData['billings'] as $billing) {
            $matrix[$billing['weight_range_id']]['weight'] = $weightRanges[$billing['weight_range_id']];
            $matrix[$billing['weight_range_id']]['billing'][] = $billing;
        }

        return $matrix;
    }

    public function actionSave()
    {
        $rawData = \Yii::$app->request->getRawBody();
        $postParams = json_decode($rawData, true);
        $billingSrv = new BillingService();
        $data = $billingSrv->buildIntlPostData($postParams);

        if (!empty($data['error'])) {
            return $this->sendErrorResponse(implode($data['error']), null);
        }

        $billingAdp = new IntlAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $response = $billingAdp->saveTariff($data['payload']);
        $response = new ResponseHandler($response);
        if ($response->isSuccess()) {
            if (isset($response->getData()['id'])) {
                $data['payload']['id'] = $response->getData()['id'];
            }
            return $this->sendSuccessResponse($data['payload']);
        } else {
            return $this->sendErrorResponse($response->getError(), null);
        }
    }

    public function actionDelete()
    {

        $id = \Yii::$app->request->post('range_id');
        if (empty($id)) {
            return $this->sendErrorResponse('Invalid ', null);
        }

        $billingAdp = new IntlAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $response = $billingAdp->deleteTariff(['id' => $id]);
        if ($response['status'] === ResponseHandler::STATUS_OK) {
            $this->flashSuccess("Weight range was deleted successfully");
        } else {
            $this->flashError($response['message']);
        }
        return $this->redirect(Yii::$app->request->getReferrer());
    }

    /**
     * Delete Weight Range Action
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function actionDeleteweightrange()
    {
        if (Yii::$app->request->isPost) {
            $weightRangeAId = Yii::$app->request->post('range_id');

            $weightRangeAdapter = new IntlAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
            $status = $weightRangeAdapter->deleteRange($weightRangeAId);

            if ($status) {
                $this->flashSuccess("Weight range was deleted successfully");
            } else {
                $this->flashError($weightRangeAdapter->getLastErrorMessage());
            }
        }
        return $this->redirect(Yii::$app->request->getReferrer());
    }

    /**
     * Delete Weight Range Action
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function actionDeleteweightranges()
    {
        if (Yii::$app->request->isPost) {
            $weightRangeIds = Yii::$app->request->post('range_ids');
            $force_delete = Yii::$app->request->post('force_delete');

            $weightRangeAdapter = new IntlAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
            $status = $weightRangeAdapter->deleteRanges($weightRangeIds, $force_delete);

            if ($status) {
                $this->flashSuccess("Weight ranges were deleted successfully");
            } else {
                $this->flashError($weightRangeAdapter->getLastErrorMessage());
            }
        }
        return $this->redirect(Yii::$app->request->getReferrer());
    }


    public function actionFetchbyid()
    {

        $id = \Yii::$app->request->get('id');
        if (empty($id)) {
            return $this->sendErrorResponse('Invalid ', null);
        }

        $billingAdp = new IntlAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $response = $billingAdp->fetchBilling($id);
        if ($response['status'] === ResponseHandler::STATUS_OK) {
            return $this->sendSuccessResponse($response['data']);
        } else {
            return $this->sendErrorResponse($response['message'], null);
        }
    }

}
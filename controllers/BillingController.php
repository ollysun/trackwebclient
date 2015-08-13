<?php
namespace app\controllers;

use Adapter\BillingAdapter;
use Adapter\Util\Calypso;
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


class BillingController extends BaseController
{
    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        //redirect to the appropriate 'default' page
        return $this->redirect('billing/weightranges');
    }

    public function getRegion()
    {
        $region_id = \Yii::$app->request->get('region_id');
        if (!isset($region_id)) {
            return $this->sendErrorResponse("Invalid parameter(s) sent!", null);
        }
        $refData = new RegionAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $region = $refData->getRegion($region_id);
        if ($region['status'] === ResponseHandler::STATUS_OK) {
            return $this->sendSuccessResponse($region['data']);
        } else {
            return $this->sendErrorResponse($region['message'], null);
        }
    }

    public function actionWeightranges()
    {
        if (Yii::$app->request->isPost) {
            $entry = Yii::$app->request->post();
            $task = Calypso::getValue(Yii::$app->request->post(), 'task', '');
            $error = [];

            $data = [];
            $data['min_weight'] = Calypso::getValue($entry, 'min_weight', null);
            $data['increment_weight'] = Calypso::getValue($entry, 'increment_weight', null);
            $data['max_weight'] = Calypso::getValue($entry, 'max_weight', null);
            $data['weight_range_id'] = Calypso::getValue($entry, 'id',null);

            if (($task == 'create' || $task == 'edit') && (empty($data['min_weight']) || empty($data['max_weight']) || empty($data['increment_weight']))) {
                $error[] = "All details are required!";
            }
            if (!empty($error)) {
                $errorMessages = implode('<br />', $error);
                Yii::$app->session->setFlash('danger', $errorMessages);
            } else {
                $adp = new WeightRangeAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
                if ($task == 'create') {
                    $response = $adp->createRange($data);
                    if ($response['status'] === Response::STATUS_OK) {
                        Yii::$app->session->setFlash('success', 'Weight range has been created successfully.');
                    } else {
                        Yii::$app->session->setFlash('danger', 'There was a problem creating the weight range. '.$response['message']);
                    }
                } else {
                    $response = $adp->editRange($data, $task);
                    if ($response['status'] === Response::STATUS_OK) {
                        Yii::$app->session->setFlash('success', 'Weight range has been edited successfully.');
                    } else {
                        Yii::$app->session->setFlash('danger', 'There was a problem editing the weight range. '.$response['message']);
                    }
                }
            }
        }
        $data_source = new RefAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $ranges = $data_source->getWeightRanges();
        $ranges = new ResponseHandler($ranges);
        $ranges_list = $ranges->getStatus() == ResponseHandler::STATUS_OK ? $ranges->getData() : [];

        return $this->render('weight_ranges', array('ranges'=>$ranges_list));
    }

    public function actionMatrix()
    {
        $branchAdp = new BranchAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $response = new ResponseHandler($branchAdp->getAllHubs());
        $branchAdpMatrix = new BranchAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $responseMatrix = new ResponseHandler($branchAdpMatrix->getMatrix());
        $hubs = [];$hubsMatrix = [];
        if($response->getStatus() == ResponseHandler::STATUS_OK){
            $hubs = $response->getData();
        }
        if($responseMatrix->getStatus() == ResponseHandler::STATUS_OK){
            $hubsMatrix = $responseMatrix->getData();
        }
        $mapList=[];
        foreach($hubsMatrix as $mapping){
            $mapList[$mapping['from_branch_id'].'_'.$mapping['to_branch_id']] = $mapping;
        }
        $zAdp = new ZoneAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $zones = $zAdp->getZones();
        $zones = new ResponseHandler($zones);
        $zones_list = $zones->getStatus() == ResponseHandler::STATUS_OK ? $zones->getData() : [];
        return $this->render('matrix',["hubs"=>$hubs,"hubsMatrix"=>$hubsMatrix,"matrixMap"=>$mapList,"zones_list" => $zones_list]);
    }

    public function actionZones()
    {
        if (Yii::$app->request->isPost) {
            $entry = Yii::$app->request->post();
            $task = Calypso::getValue(Yii::$app->request->post(), 'task', '');
            $error = [];

            $data = [];
            $data['name'] = Calypso::getValue($entry, 'zone_name', null);
            $data['description'] = Calypso::getValue($entry, 'zone_desc');
            $data['code'] = Calypso::getValue($entry, 'zone_code');
            $data['type'] = Calypso::getValue($entry, 'zone_type');
            $data['zone_id'] = Calypso::getValue($entry, 'id',null);

            if (($task == 'create' || $task == 'edit') && (empty($data['name']) || empty($data['description']) || empty($data['code']))) {
                $error[] = "All details are required!";
            }
            if (!empty($error)) {
                $errorMessages = implode('<br />', $error);
                Yii::$app->session->setFlash('danger', $errorMessages);
            } else {
                $zone = new ZoneAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
                if ($task == 'create') {
                    $response = $zone->createZone($data);
                    if ($response['status'] === Response::STATUS_OK) {
                        Yii::$app->session->setFlash('success', 'Zone has been created successfully.');
                    } else {
                        Yii::$app->session->setFlash('danger', 'There was a problem creating the zone.'.$response['message']);
                    }
                } else {
                    $response = $zone->editZone($data);
                    if ($response['status'] === Response::STATUS_OK) {
                        Yii::$app->session->setFlash('success', 'Zone has been edited successfully.');
                    } else {
                        Yii::$app->session->setFlash('danger', 'There was a problem editing the zone'.$response['message']);
                    }
                }
            }
        }
        $zAdp = new ZoneAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $zones = $zAdp->getZones();
        $zones = new ResponseHandler($zones);
        $zones_list = $zones->getStatus() == ResponseHandler::STATUS_OK ? $zones->getData() : [];

        return $this->render('zones', array('zones'=>$zones_list));
    }

    public function actionRegions()
    {
        if (Yii::$app->request->isPost) {
            $entry = Yii::$app->request->post();
            $task = Calypso::getValue(Yii::$app->request->post(), 'task', '');
            $error = [];

            $data = [];
            $data['region_id'] = Calypso::getValue($entry, 'id', null);
            $data['name'] = Calypso::getValue($entry, 'name', null);
            $data['description'] = Calypso::getValue($entry, 'description');
            $data['status'] = Calypso::getValue($entry, 'status');
            $data['active_fg'] = $data['status'];
            $data['country_id'] = Calypso::getValue($entry, 'country_id', 1); //Hard coded to Nigeria

            if (($task == 'create' || $task == 'edit') && (empty($data['name']) || empty($data['description']))) {
                $error[] = "All details are required!";
            }
            if (!empty($error)) {
                $errorMessages = implode('<br />', $error);
                Yii::$app->session->setFlash('danger', $errorMessages);
            } else {
                $region = new RegionAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
                if ($task == 'create') {
                    $response = $region->addRegion($data);
                    if ($response['status'] === Response::STATUS_OK) {
                        Yii::$app->session->setFlash('success', 'Region has been created successfully.');
                    } else {
                        Yii::$app->session->setFlash('danger', 'There was a problem creating the region.'.$response['message']);
                    }
                } else {
                    $response = $region->editRegion($data, $task);
                    if ($response['status'] === Response::STATUS_OK) {
                        Yii::$app->session->setFlash('success', 'Region has been edited successfully.');
                    } else {
                        Yii::$app->session->setFlash('danger', 'There was a problem editing the region.'.$response['message']);
                    }
                }
            }
        }

        $filter_country = Calypso::getValue(Yii::$app->request->post(), 'filter_country', 1);
        $refAdp = new RefAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $regions = $refAdp->getRegions($filter_country);
        $regions = new ResponseHandler($regions);

        $refAdp = new RefAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $countries = $refAdp->getCountries(); // Hardcoded Nigeria for now
        $countries = new ResponseHandler($countries);

        $region_list = $regions->getStatus() == ResponseHandler::STATUS_OK ? $regions->getData() : [];
        $countries_list = $countries->getStatus() == ResponseHandler::STATUS_OK ? $countries->getData() : [];
        return $this->render('regions', array('regions' => $region_list, 'countries' => $countries_list,));
    }

    public function actionStatemapping()
    {
        if (Yii::$app->request->isPost) {
            $entry = Yii::$app->request->post();
            $task = Calypso::getValue(Yii::$app->request->post(), 'task', '');
            $error = [];

            $data = [];
            $data['region_id'] = Calypso::getValue($entry, 'region_id', null);
            $data['state_id'] = Calypso::getValue($entry, 'state_id', null);

            if (($task == 'create' || $task == 'edit') && (empty($data['region_id']) || empty($data['state_id']))) {
                $error[] = "All details are required!";
            }
            if (!empty($error)) {
                $errorMessages = implode('<br />', $error);
                Yii::$app->session->setFlash('danger', $errorMessages);
            } else {
                $region = new RegionAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
                $response = $region->mapState($data);
                if ($response['status'] === Response::STATUS_OK) {
                    Yii::$app->session->setFlash('success', 'State to Region has been edited successfully.');
                } else {
                    Yii::$app->session->setFlash('danger', 'There was a problem mapping state to Region.'.$response['message']);
                }
            }
        }

        $refAdp = new RefAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $regions = $refAdp->getRegions(1);
        $regions = new ResponseHandler($regions);
        $region_list = $regions->getStatus() == ResponseHandler::STATUS_OK ? $regions->getData() : [];

        $refAdp = new RefAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $states = $refAdp->getStates(1,1); // Hardcoded Nigeria for now
        $states = new ResponseHandler($states);
        $state_list = $states->getStatus() == ResponseHandler::STATUS_OK ? $states->getData() : [];

        $maps = array(
            array('state_id' => 1, 'state' => 'Lagos', 'region'=>array('region_id' => 1, 'name' => "Department")),
            array('state_id' => 2, 'state' => 'Oyo', 'region'=>array('region_id' => 2, 'name' => "Institute")),
            array('state_id' => 3, 'state' => 'Ogun', 'region'=>array('region_id' => 1, 'name' => "Department")),
            array('state_id' => 4, 'state' => 'Kwara', 'region'=>array('region_id' => 1, 'name' => "Department")),
            array('state_id' => 5, 'state' => 'Sokoto', 'region'=>array('region_id' => 2, 'name' => "Institute")),
        );

        $tmp = array();
        foreach($state_list as $arg)
        {
            $tmp[$arg['region']['name']][] = $arg;
        }

        $output = array();
        foreach($tmp as $type => $states)
        {
            $output[] = array('region' => $type,'states' => $states);
        }
		return $this->render('state_mapping', array('states' => $state_list, 'regions' => $region_list, 'output' => $output,));
	}

    public function actionCitymapping()
    {
        if (Yii::$app->request->isPost) {
            $entry = Yii::$app->request->post();
            $task = Calypso::getValue(Yii::$app->request->post(), 'task', '');
            $error = [];

            $data = [];
            $data['name'] = Calypso::getValue($entry, 'city_name', null);
            $data['state_id'] = Calypso::getValue($entry, 'state');
            $data['onforwarding_charge_id'] = Calypso::getValue($entry, 'charge');
            $data['transit_time'] = Calypso::getValue($entry, 'transit_time');
            $data['status'] = Calypso::getValue($entry, 'status');
            $data['branch_id'] = Calypso::getValue($entry, 'branch_id');
            $data['city_id'] = Calypso::getValue($entry, 'id',null);

            if (($task == 'create' || $task == 'edit') && (empty($data['name']) || !isset($data['transit_time']))) {
                $error[] = "All details are required!";
            }
            if (!empty($error)) {
                $errorMessages = implode('<br />', $error);
                Yii::$app->session->setFlash('danger', $errorMessages);
            } else {
                $city = new RegionAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
                if ($task == 'create') {
                    $response = $city->addCity($data);
                    if ($response['status'] === Response::STATUS_OK) {
                        Yii::$app->session->setFlash('success', 'City has been created successfully.');
                    } else {
                        Yii::$app->session->setFlash('danger', 'There was a problem editing the city. '.$response['messsage']);
                    }
                } else {
                    $response = $city->editCity($data);
                    if ($response['status'] === Response::STATUS_OK) {
                        Yii::$app->session->setFlash('success', 'City has been edited successfully.');
                    } else {
                        Yii::$app->session->setFlash('danger', 'There was a problem editing the city. '.$response['messsage']);
                    }
                }
            }
        }

        $refAdp = new RefAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        $states = $refAdp->getStates(1); // Hardcoded Nigeria for now
        $states = new ResponseHandler($states);
        $states_list = $states->getStatus()==ResponseHandler::STATUS_OK?$states->getData(): [];

        $refAdp = new RefAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        $charges = $refAdp->getOnforwadingCharges();
        $charges = new ResponseHandler($charges);
        $charges_list = $charges->getStatus()==ResponseHandler::STATUS_OK?$charges->getData(): [];

        $cAdp = new RegionAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $cities = $cAdp->getAllCity(1,1);
        $cities = new ResponseHandler($cities);
        $cities_list = $cities->getStatus() == ResponseHandler::STATUS_OK ? $cities->getData() : [];

        $hubAdp = new BranchAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        $hubs = $hubAdp->getAllHubs();
        $hubs = new ResponseHandler($hubs);
        $hub_list = $hubs->getStatus()==ResponseHandler::STATUS_OK?$hubs->getData(): [];

        return $this->render('city_mapping', array('cities'=>$cities_list,'states'=>$states_list,'hubs'=>$hub_list,'charges'=>$charges_list));
    }

    public function actionOnforwarding()
    {
        if (Yii::$app->request->isPost) {
            $entry = Yii::$app->request->post();
            $task = Calypso::getValue(Yii::$app->request->post(), 'task', '');
            $error = [];

            $data = [];
            $data['name'] = Calypso::getValue($entry, 'onforward_name', null);
            $data['code'] = Calypso::getValue($entry, 'onforward_code');
            $data['description'] = Calypso::getValue($entry, 'onforward_desc');
            $data['amount'] = Calypso::getValue($entry, 'onforward_amount');
            $data['percentage'] = Calypso::getValue($entry, 'onforward_percentage',0)/100;
            $data['status'] = Calypso::getValue($entry, 'status');
            $data['charge_id'] = Calypso::getValue($entry, 'id',null);

            if (($task == 'create' || $task == 'edit') && (empty($data['name']) || empty($data['code']) || empty($data['amount']))) {
                $error[] = "All details are required!";
            }
            if (!empty($error)) {
                $errorMessages = implode('<br />', $error);
                Yii::$app->session->setFlash('danger', $errorMessages);
            } else {
                $bill = new BillingAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
                if ($task == 'create') {
                    $response = $bill->addOnforwardingCharge($data);
                    if ($response['status'] === Response::STATUS_OK) {
                        Yii::$app->session->setFlash('success', 'Charge has been created successfully.');
                    } else {
                        Yii::$app->session->setFlash('danger', 'There was a problem creating the charge.'.$response['message']);
                    }
                } else {
                    $response = $bill->editOnforwardingCharge($data);
                    if ($response['status'] === Response::STATUS_OK) {
                        Yii::$app->session->setFlash('success', 'Charge has been edited successfully.');
                    } else {
                        Yii::$app->session->setFlash('danger', 'There was a problem editing the charge.'.$response['message']);
                    }
                }
            }
        }

        $refAdp = new RefAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        $charges = $refAdp->getOnforwadingCharges();
        $charges = new ResponseHandler($charges);
        $charges_list = $charges->getStatus()==ResponseHandler::STATUS_OK?$charges->getData(): [];

        return $this->render('onforwarding', array('charges'=>$charges_list));
    }

    public function actionPricing()
    {
        $viewBag = [
            'billings'      => [],
            'zones'         => [],
            'weightRanges'  => []
        ];
        $billingAdp = new BillingAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        $billings = $billingAdp->fetchAllBilling();
        if($billings['status'] == ResponseHandler::STATUS_OK) {
            $viewBag['billings'] = $billings['data'];
        }
        $zoneAdp = new ZoneAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        $zones = $zoneAdp->getZones();
        if($zones['status'] == ResponseHandler::STATUS_OK) {
            $viewBag['zones'] = $zones['data'];
        }
        $weightAdp = new WeightRangeAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        $weightRanges = $weightAdp->getRange();
        if($weightRanges['status'] == ResponseHandler::STATUS_OK) {
            $viewBag['weightRanges'] = $weightRanges['data'];
        }

        $billingMatrix = $this->buildPricingTable($viewBag);

        return $this->render('pricing',
            [
                'billingMatrix' => $billingMatrix,
                'weightRanges' => $viewBag['weightRanges'],
                'zones' => $viewBag['zones']
            ]);
    }

    private function buildPricingTable($pricingData) {
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

    public function actionSave() {

        $rawData = \Yii::$app->request->getRawBody();
        $postParams = json_decode($rawData, true);
        $billingSrv = new BillingService();
        $data = $billingSrv->buildPostData($postParams);

        if(!empty($data['error'])) {
            return $this->sendErrorResponse(implode($data['error']), null);
        }

        $billingAdp = new BillingAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        if(isset($data['payload']['weight_billing_id'])) {
            $response = $billingAdp->editBilling($data['payload']);
        } else {
            $response = $billingAdp->addBilling($data['payload']);
        }
        if ($response['status'] === ResponseHandler::STATUS_OK) {
            if(isset($response['data']['id'])) {
                $data['payload']['id'] = $response['data']['id'];
            }
            return $this->sendSuccessResponse($data['payload']);
        } else {
            return $this->sendErrorResponse($response['message'], null);
        }
    }

    public function actionDelete() {

        $id = \Yii::$app->request->get('id');
        if(empty($id)) {
            return $this->sendErrorResponse('Invalid ', null);
        }

        $billingAdp = new BillingAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        $response = $billingAdp->deleteBilling([ 'weight_billing_id' => $id ]);
        if ($response['status'] === ResponseHandler::STATUS_OK) {
            return $this->sendSuccessResponse($response['data']);
        } else {
            return $this->sendErrorResponse($response['message'], null);
        }
    }

    public function actionFetchbyid() {

        $id = \Yii::$app->request->get('id');
        if(empty($id)) {
            return $this->sendErrorResponse('Invalid ', null);
        }

        $billingAdp = new BillingAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        $response = $billingAdp->fetchBillingById([ 'weight_billing_id' => $id ]);
        if ($response['status'] === ResponseHandler::STATUS_OK) {
            return $this->sendSuccessResponse($response['data']);
        } else {
            return $this->sendErrorResponse($response['message'], null);
        }
    }
    public function actionUpdatemapping(){
        $entry = Yii::$app->request->post();
        if(!empty($entry)){
            $zAdp = new ZoneAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
            $zones = $zAdp->saveMatrix(json_encode([$entry]));
            $zones = new ResponseHandler($zones);
            if($zones->getStatus() == ResponseHandler::STATUS_OK){
                $d = $zones->getData();
                if(empty($d['bad_matrix_info'])){
                    Yii::$app->session->setFlash('success', 'Zone has been edited successfully.');
                }else{
                    Yii::$app->session->setFlash('danger', 'There was a problem editing the zone mapping. Please ensure these hubs have been mapped');
                }
            }else{
                Yii::$app->session->setFlash('danger', 'There was a problem editing the zone mapping. #Reason: Service refused request');
            }
            return $zones->getStatus() == ResponseHandler::STATUS_OK ? 1:0;
        }
        return 0;
    }
    public function actionRemovemapping(){
        $entry = Yii::$app->request->post();
        if(!empty($entry)){
            $zAdp = new ZoneAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
            $zones = $zAdp->removeMatrix($entry);
            $zones = new ResponseHandler($zones);
            if($zones->getStatus() == ResponseHandler::STATUS_OK){
                $d = $zones->getData();
                if(empty($d['bad_matrix_info'])){
                    Yii::$app->session->setFlash('success', 'Zone mapping removed successfully.');
                }else{
                    Yii::$app->session->setFlash('danger', 'There was a problem removing the zone mapping. Please ensure these hubs have been mapped');
                }
            }else{
                Yii::$app->session->setFlash('danger', 'There was a problem removing the zone mapping. #Reason: Service refused request');
            }
            return $zones->getStatus() == ResponseHandler::STATUS_OK ? 1:0;
        }
        return 0;
    }
}
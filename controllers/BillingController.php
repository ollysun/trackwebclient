<?php
namespace app\controllers;

use Adapter\Util\Calypso;
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
        return $this->render('weight_ranges');
    }

    public function actionMatrix()
    {
        return $this->render('matrix');
    }

    public function actionZones()
    {
        return $this->render('zones');
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
                        Yii::$app->session->setFlash('danger', $response['message'] . 'There was a problem creating the region. Please try again.');
                    }
                } else {
                    $response = $region->editRegion($data, $task);
                    if ($response['status'] === Response::STATUS_OK) {
                        Yii::$app->session->setFlash('success', 'Region has been edited successfully.');
                    } else {
                        Yii::$app->session->setFlash('danger', 'There was a problem editing the region. Please try again.');
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
                    Yii::$app->session->setFlash('danger', 'There was a problem mapping state to Region. Please try again.');
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
        return $this->render('city_mapping');
    }

    public function actionPricing()
    {
        return $this->render('pricing');
    }

    public function actionExceptions()
    {
        return $this->render('exceptions');
    }

    public function actionOnforwarding()
    {
        return $this->render('onforwarding');
    }
}
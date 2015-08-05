<?php
/**
 * Created by PhpStorm.
 * User: RotelandO
 * Date: 7/25/15
 * Time: 1:02 PM
 */

namespace app\controllers;
use Adapter\BranchAdapter;
use Adapter\Globals\ServiceConstant;
use Adapter\RefAdapter;
use Adapter\ResponseHandler;
use Adapter\Util\Calypso;
use Adapter\ZoneAdapter;
use Adapter\RequestHelper;
use Yii;
use Adapter\Util\Response;


class AdminController extends BaseController {
	public function beforeAction($action) {
		$this->enableCsrfValidation = false;
		return parent::beforeAction($action);
	}
	public function actionHubmapping()
	{
		if (Yii::$app->request->isPost) {
			$data = \Yii::$app->request->post();

			$from_id = Calypso::getValue($data, 'from_id');
			$to_ids = Calypso::getValue($data, 'branches');
			$zone_ids = Calypso::getValue($data, 'zones');

			$matrix_info = [];
			for ($k = 0; $k < count($to_ids); $k++) {
				$matrix_info[] = array('to_branch_id' => $to_ids[$k], 'from_branch_id' => $from_id, 'zone_id' => $zone_ids[$k]);
			}

			$zone = new ZoneAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
			$response = $zone->saveMatrix(json_encode($matrix_info));
			if ($response['status'] === Response::STATUS_OK) {
				$this->redirect('/admin/managebranches');
			} else {
				Yii::$app->session->setFlash('danger', 'There was a problem creating the zone.'.$response['message']);
			}
		}

		$hub_id = \Yii::$app->request->get('hub');
		$hubAdp = new BranchAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
		$hub = $hubAdp->getOneHub($hub_id);
		$hub = new ResponseHandler($hub);
		$hub = $hub->getStatus()==ResponseHandler::STATUS_OK?$hub->getData(): [];

		$hubAdp = new BranchAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
		$hubs = $hubAdp->getAllHubs();
		$hubs = new ResponseHandler($hubs);
		$hub_list = $hubs->getStatus()==ResponseHandler::STATUS_OK?$hubs->getData(): [];

		$zAdp = new ZoneAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
		$zones = $zAdp->getZones();
		$zones = new ResponseHandler($zones);
		$zones_list = $zones->getStatus() == ResponseHandler::STATUS_OK ? $zones->getData() : [];

		return $this->render('new_hub_mapping', array('hub'=>$hub, 'hubs'=>$hub_list, 'zones'=> $zones_list));
	}

	public function actionManagebranches()
	{
		if(Yii::$app->request->isPost){
			$entry = Yii::$app->request->post();
			$error = [];

			$hub_data = [];
			$hub_data['name'] = Calypso::getValue($entry, 'name', null);
			$hub_data['address'] = Calypso::getValue($entry, 'address');
			$hub_data['branch_type'] = ServiceConstant::BRANCH_TYPE_HUB;
			$hub_data['state_id'] = Calypso::getValue($entry, 'state_id');
			$hub_data['status'] =  Calypso::getValue($entry, 'status');
			$hub_data['branch_id'] = Calypso::getValue($entry, 'id', null);

			$task =  Calypso::getValue(Yii::$app->request->post(), 'task');

			if(($task == 'create' || $task == 'edit') && (empty($hub_data['name']) || empty($hub_data['address']))) {
				$error[] = "All details are required!";
			}
			if(!empty($error)) {
				$errorMessages = implode('<br />', $error);
				Yii::$app->session->setFlash('danger', $errorMessages);
			}
			else {
				$hub = new BranchAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
				if($task == 'create'){
					$response = $hub->createNewHub($hub_data);
					if ($response['status'] === Response::STATUS_OK) {
						Yii::$app->session->setFlash('success', 'Hub has been created successfully.');
						Yii::$app->response->redirect("/admin/hubmapping?hub={$response['data']['id']}");
					} else {
						Yii::$app->session->setFlash('danger', 'There was a problem creating the hub. Please try again.');
					}
				}
				else{
					$response = $hub->editOneHub($hub_data, $task);
					if ($response['status'] === Response::STATUS_OK) {
						Yii::$app->session->setFlash('success', 'Hub has been edited successfully.');
					} else {
						Yii::$app->session->setFlash('danger', 'There was a problem editing the hub. Please try again.'.$response['message']);
					}
				}
			}
		}

		$refAdp = new RefAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
		$states = $refAdp->getStates(1); // Hardcoded Nigeria for now
		$states = new ResponseHandler($states);

		$filter_state_id = Calypso::getValue(Yii::$app->request->post(), 'filter_state_id',null);
		$hubAdp = new BranchAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
		$hubs = $hubAdp->getHubs($filter_state_id);
		$hubs = new ResponseHandler($hubs);

		$state_list = $states->getStatus()==ResponseHandler::STATUS_OK?$states->getData(): [];
		$hub_list = $hubs->getStatus()==ResponseHandler::STATUS_OK?$hubs->getData(): [];
		return $this->render('managehubs',array('States'=>$state_list, 'filter_state_id'=>$filter_state_id, 'hubs'=>$hub_list));
	}

	public function actionManageecs()
	{
		if(Yii::$app->request->isPost){
			$entry = Yii::$app->request->post();
			$task =  Calypso::getValue($entry, 'task','');
			$error = [];

			$data = [];
			$data['name'] = Calypso::getValue($entry, 'name', null);
			$data['address'] = Calypso::getValue($entry, 'address');
			$data['branch_type'] = ServiceConstant::BRANCH_TYPE_EC;
			$data['status'] =  Calypso::getValue($entry, 'status');
			$data['hub_id'] = Calypso::getValue($entry, 'hub_id', null);
			$data['branch_id'] = Calypso::getValue($entry, 'id', null);
			$data['ec_id'] = Calypso::getValue($entry, 'id', null);
			$data['state_id'] = Calypso::getValue($entry, 'state_id', null);

			if(($task == 'create' || $task == 'edit') && (empty($data['name']) || empty($data['address']))) {
				$error[] = "All details are required!";
			}
			if(!empty($error)) {
				$errorMessages = implode('<br />', $error);
				Yii::$app->session->setFlash('danger', $errorMessages);
			}
			else {
				$center = new BranchAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
				if($task == 'create'){
					$response = $center->createNewCentre($data);
					if ($response['status'] === Response::STATUS_OK) {
						Yii::$app->session->setFlash('success', 'Centre has been created successfully.');
					} else {
						Yii::$app->session->setFlash('danger', 'There was a problem creating the centre. Please try again.');
					}
				}
				elseif($task != ''){
					$response = $center->editOneCentre($data, $task);
					if ($response['status'] === Response::STATUS_OK) {
						Yii::$app->session->setFlash('success', 'Centre has been edited successfully.');
					} else {
						Yii::$app->session->setFlash('danger', 'There was a problem editing the hub. Please try again.');
					}
				}
			}
		}
		$refAdp = new RefAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
		$states = $refAdp->getStates(1); // Hardcoded Nigeria for now
		$states = new ResponseHandler($states);

		$hubAdp = new BranchAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
		$hubs = $hubAdp->getHubs();
		$hubs = new ResponseHandler($hubs);

		$filter_hub_id = Calypso::getValue(Yii::$app->request->post(), 'filter_hub_id', null);
		$hubAdp = new BranchAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
		$centres = $hubAdp->getCentres($filter_hub_id);
		$centres = new ResponseHandler($centres);

		$state_list = $states->getStatus()==ResponseHandler::STATUS_OK?$states->getData(): [];
		$hub_list = $hubs->getStatus()==ResponseHandler::STATUS_OK?$hubs->getData(): [];
		$centres_list = $centres->getStatus()==ResponseHandler::STATUS_OK?$centres->getData(): [];
		return $this->render('manageecs',array('States'=>$state_list, 'hubs'=>$hub_list, 'centres'=>$centres_list, 'filter_hub_id'=>$filter_hub_id));
	}
}
<?php
/**
 * Created by PhpStorm.
 * User: RotelandO
 * Date: 7/25/15
 * Time: 1:02 PM
 */

namespace app\controllers;
use Adapter\BranchAdapter;
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
				$this->redirect('/site/managebranches');
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
}
<?php
/**
 * Created by PhpStorm.
 * User: RotelandO
 * Date: 7/25/15
 * Time: 1:02 PM
 */

namespace app\controllers;


class AdminController extends BaseController {
	public function beforeAction($action) {
		$this->enableCsrfValidation = false;
		return parent::beforeAction($action);
	}
	public function actionHubmapping() {
		return $this->render('new_hub_mapping');
	}
}
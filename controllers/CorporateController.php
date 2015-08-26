<?php

namespace app\controllers;
use Yii;
use Adapter\Globals\ServiceConstant;
use Adapter\Util\Calypso;
use Adapter\Util\Response;


class CorporateController extends BaseController {
	public function beforeAction($action) {
		$this->enableCsrfValidation = false;
		return parent::beforeAction($action);
	}

	public function actionRequests() {
		return $this->render('requests');
	}

	public function actionUsers() {
		return $this->render('users');
	}
}

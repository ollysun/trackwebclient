<?php

namespace app\controllers;

use Adapter\Util\Calypso;
use Adapter\Globals\ServiceConstant;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;

class ManifestController extends BaseController {
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'only' => ['logout'],
				'rules' => [
					[
						'actions' => ['logout'],
						'allow' => true,
						'roles' => ['@'],
					],
				],
			],
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'logout' => ['get'],
				],
			],
		];
	}

	public function actions(){
		return [
			'error' => [
				'class' => 'yii\web\ErrorAction',
			],
			'captcha' => [
				'class' => 'yii\captcha\CaptchaAction',
				'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
			],
		];
	}
	public function beforeAction($action){
		$this->enableCsrfValidation = false;
		if(Calypso::getInstance()->cookie('page_width')){
			$this->page_width = Calypso::getInstance()->cookie('page_width');
		}
		return parent::beforeAction($action);
	}
	public function actionIndex() {
		return $this->render('index');
	}
	public function actionView() {
		return $this->render('view');
	}
	public function actionPrint() {
		$this->layout = 'print';
		return $this->render('print');
	}
}
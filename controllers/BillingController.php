<?php

namespace app\controllers;


class BillingController extends BaseController {
	public function actionIndex() {
		//redirect to the appropriate 'default' page
		 return $this->redirect('billing/weightranges');
	}
	public function actionZones()
	{
		return $this->render('zones');
	}
	public function actionRegions() {
		 return $this->render('regions');
	}
	public function actionStatemapping()
	{
		return $this->render('state_mapping');
	}
	public function actionCitymapping()
	{
		return $this->render('city_mapping');
	}
	public function actionWeightranges() {
		 return $this->render('weight_ranges');
	}
	public function actionPricing()
	{
		return $this->render('pricing');
	}
}
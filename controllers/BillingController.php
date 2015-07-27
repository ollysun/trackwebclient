<?php

namespace app\controllers;


class BillingController extends BaseController {
	public function actionIndex() {
		//redirect to the appropriate 'default' page
		 return $this->redirect('billing/weightranges');
	}
	public function actionRegions() {
		 return $this->render('regions');
	}
	public function actionWeightranges() {
		 return $this->render('weight_ranges');
	}
}
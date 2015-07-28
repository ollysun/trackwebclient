<?php
namespace app\controllers;

use Yii;


class FinanceController extends BaseController
{
    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        //redirect to the appropriate 'default' page
        return $this->redirect('finance/customersall');
    }

    public function actionCustomersall()
    {
        return $this->render('customers_all');
    }

    public function actionMerchantspending()
    {
        return $this->render('merchants_pending');
    }

    public function actionMerchantsdue()
    {
        return $this->render('merchants_due');
    }

    public function actionMerchantspaid()
    {
        return $this->render('merchants_paid');
    }
}
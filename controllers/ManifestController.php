<?php

namespace app\controllers;

use Adapter\ManifestAdapter;
use Adapter\RequestHelper;
use Adapter\ResponseHandler;
use Adapter\Util\Calypso;

class ManifestController extends BaseController
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ]
        ];
    }

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        if (Calypso::getInstance()->cookie('page_width')) {
            $this->page_width = Calypso::getInstance()->cookie('page_width');
        }
        return parent::beforeAction($action);
    }

    /**
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return string
     */
    public function actionIndex()
    {
        $filters = [];

        $validFilters = ['status' => 'status', 'from' => 'start_created_date', 'to' => 'end_created_date'];
        foreach($validFilters as $clientFilter => $serverFilter) {
            $value = \Yii::$app->getRequest()->get($clientFilter, null);
            if(!is_null($value) && $value != -1){
                // Checks if filter is a date filter and add time to it
                if(preg_match('/\bstart\_\w+\_date\b/', $serverFilter)){
                    $filters[$serverFilter] = $value . " 00:00:00";
                }
                else if(preg_match('/\bend\_\w+\_date\b/', $serverFilter)) {
                    $filters[$serverFilter] = $value . " 23:59:59";
                }

                else if ($serverFilter == 'status') {
                    // Handle special filters
                    $tempArray = explode('=', $value);

                    if(count($tempArray) > 1) {
                        $filters[$tempArray[0]] = $tempArray[1];
                    }
                } else {
                    $filters[$serverFilter] = $value;
                }
            }
        }

        $filter = \Yii::$app->getRequest()->get('status');

        $defaultDate = date('Y/m/d');
        $fromDate = Calypso::getValue($filters, 'start_created_date', $defaultDate);
        $toDate = Calypso::getValue($filters, 'end_created_date', $defaultDate);

        $adapter = new ManifestAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        $response = new ResponseHandler($adapter->getManifests($filters));

        $session_data = Calypso::getInstance()->session('user_session');
        $branchId = Calypso::getValue($session_data, 'branch_id');

        $manifests = [];
        if($response->getStatus() == ResponseHandler::STATUS_OK){
            $manifests = $response->getData();
        }

        return $this->render('index', ['manifests' => $manifests, 'fromDate' => $fromDate, 'toDate' => $toDate, 'branchId' => $branchId, 'filter' => $filter]);
    }

    /**
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return string
     */
    public function actionView()
    {
        $id = \Yii::$app->getRequest()->get('id');
        $adapter = new ManifestAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        $response = new ResponseHandler($adapter->getManifest($id));

        // TODO handle errors
        $manifest = [];
        if($response->getStatus() == ResponseHandler::STATUS_OK) {
            $manifest = $response->getData();
        }
        return $this->render('view', ['manifest' => $manifest, 'id' => $id]);
    }

    public function actionPrint()
    {
        $this->layout = 'print';
        return $this->render('print');
    }

    public function actionPrintdelivery()
    {
        $this->layout = 'print';
        return $this->render('print_delivery_run');
    }
}
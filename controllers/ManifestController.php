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
        $defaultDate = date('Y/m/d');
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
            else {
                // Checks if filter is a date filter and add time to it
                if(preg_match('/\bstart\_\w+\_date\b/', $serverFilter)){
                    $filters[$serverFilter] = $defaultDate . " 00:00:00";
                }
                else if(preg_match('/\bend\_\w+\_date\b/', $serverFilter)) {
                    $filters[$serverFilter] = $defaultDate . " 23:59:59";
                }

            }
        }

        $filter = \Yii::$app->getRequest()->get('status');

        $fromDate = Calypso::getValue($filters, 'start_created_date', $defaultDate);
        $toDate = Calypso::getValue($filters, 'end_created_date', $defaultDate);

        $query = \Yii::$app->getRequest()->get('search');

        if(!is_null($query)) {
            $filters = ['id' => $query];
        }

        $adapter = new ManifestAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        $response = new ResponseHandler($adapter->getManifests($filters));

        $session_data = Calypso::getInstance()->session('user_session');
        $branchId = Calypso::getValue($session_data, 'branch_id');

        $manifests = [];
        if($response->getStatus() == ResponseHandler::STATUS_OK){
            $manifests = $response->getData();
        }

        return $this->render('index', ['manifests' => $manifests, 'fromDate' => $fromDate, 'toDate' => $toDate, 'branchId' => $branchId, 'filter' => $filter, 'offset' => 0]);
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

        $manifest = [];
        if($response->getStatus() == ResponseHandler::STATUS_OK) {
            $manifest = $response->getData();
        } else {
            $this->flashError('An error occurred while trying to fetch manifest details. Please try again.');
        }
        return $this->render('view', ['manifest' => $manifest, 'id' => $id]);
    }

    /**
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return string
     */
    public function actionPrint()
    {
        $this->layout = 'print';
        $id = \Yii::$app->getRequest()->get('id');
        $adapter = new ManifestAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        $response = new ResponseHandler($adapter->getManifest($id));

        $manifest = [];
        if($response->getStatus() == ResponseHandler::STATUS_OK) {
            $manifest = $response->getData();
        } else {
            $this->flashError('An error occurred while trying to fetch manifest details. Please try again.');
        }
        return $this->render('print', ['manifest' => $manifest, 'id' => $id]);
    }

    public function actionPrintdelivery()
    {
        $this->layout = 'print';
        return $this->render('print_delivery_run');
    }
}
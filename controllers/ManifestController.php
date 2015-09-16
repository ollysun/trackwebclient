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
    public function actionIndex($page = 1)
    {
        $page_width = 50;
        $offset = $page_width * ($page - 1);

        // Filters
        $filters = \Yii::$app->getRequest()->get();

        $adapter = new ManifestAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        $response = new ResponseHandler($adapter->getManifests($filters));

        $total_count = 0;

        $manifests = [];
        if($response->getStatus() == ResponseHandler::STATUS_OK){
            $manifests = $response->getData();
            $total_count = count($manifests);
        }
        return $this->render('index', ['manifests' => $manifests]);
    }

    public function actionView()
    {
        return $this->render('view');
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
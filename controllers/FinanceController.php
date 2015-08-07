<?php
namespace app\controllers;

use Yii;
use Adapter\AdminAdapter;
use Adapter\Globals\ServiceConstant;
use Adapter\ParcelAdapter;
use Adapter\UserAdapter;
use Adapter\RequestHelper;
use Adapter\ResponseHandler;
use Adapter\Util\Calypso;
use app\services\HubService;
use yii\data\Pagination;

class FinanceController extends BaseController
{
    var $page_width = 10;
    var $offset = 0;

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
        $page_width = 50;//Calypso::getValue(Calypso::getInstance()->get(),'page_width',40);
        $offset = $page_width * (Calypso::getValue(Calypso::getInstance()->get(),'page',1) - 1);

        $parcel = new ParcelAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        $response = $parcel->getParcels(null,null,null,null,$offset,$page_width,1,1);
        $response = new ResponseHandler($response);
        $data = [];
        if($response->getStatus() ==  ResponseHandler::STATUS_OK){
            $data = $response->getData();
        }
        $total_count = empty($data['total_count'])?0:$data['total_count'];
        $pages = new Pagination(['totalCount' => $total_count,'pageSize'=>$page_width,'pageSizeParam'=>'page_width']);
        return $this->render('customers_all',array('parcels'=>$data,'offset'=>$offset,'pages'=>$pages, 'total_count'=>$total_count));
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
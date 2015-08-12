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

    public function actionCustomersall($page=1)
    {
        $from_date = Calypso::getValue(Calypso::getInstance()->get(),'from',date('Y-m-d'));
        $to_date = Calypso::getValue(Calypso::getInstance()->get(),'to',date('Y-m-d'));
        $method = Calypso::getValue(Calypso::getInstance()->get(),'payment_type',null);
        $method = $method===''?null:$method;
        $waybillnumber = Calypso::getValue(Calypso::getInstance()->get(),'waybillnumber',null);

        $page_width = 50;//Calypso::getValue(Calypso::getInstance()->get(),'page_width',40);
        $offset = $page_width * ($page - 1);

        $parcel = new ParcelAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        $response = $parcel->getParcelsByPayment($waybillnumber,$method,$from_date,$to_date,$offset,$page_width,1);
        $response = new ResponseHandler($response);
        $data = [];
        if($response->getStatus() ==  ResponseHandler::STATUS_OK){
            $data = $response->getData();
        }
        $total_count = empty($data['total_count'])?0:$data['total_count'];
        $pages = new Pagination(['totalCount' => $total_count,'defaultPageSize'=>$page_width]);
        return $this->render('customers_all',array('parcels'=>$data,'payment_type'=>$method,'from_date'=>$from_date,'to_date'=>$to_date,'offset'=>$offset,'pages'=>$pages, 'total_count'=>$total_count));
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
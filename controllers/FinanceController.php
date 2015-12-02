<?php
namespace app\controllers;

use Adapter\CompanyAdapter;
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
        $from_date = Calypso::getValue(Calypso::getInstance()->get(),'from',date('Y/m/d'));
        $to_date = Calypso::getValue(Calypso::getInstance()->get(),'to',date('Y/m/d'));
        $method = Calypso::getValue(Calypso::getInstance()->get(),'payment_type',null);
        $method = $method===''?null:$method;
        $waybillnumber = Calypso::getValue(Calypso::getInstance()->get(),'waybillnumber',null);

        $page_width = 50;//Calypso::getValue(Calypso::getInstance()->get(),'page_width',40);
        $offset = $page_width * ($page - 1);

        $parcel = new ParcelAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        $response = $parcel->getParcelsByPayment($waybillnumber,$method,$from_date.'%2000:00:00',$to_date.'%2023:59:59',$offset,$page_width,1,null,1);
        $response = new ResponseHandler($response);
        $data = [];
        $total_count = 0;
        if($response->getStatus() ==  ResponseHandler::STATUS_OK){
            $data = $response->getData();
            $total_count = $data['total_count'];
            $data = $data['parcels'];
        }
        return $this->render('customers_all',array('search'=>$waybillnumber,'parcels'=>$data,'payment_type'=>$method,'from_date'=>$from_date,'to_date'=>$to_date,'offset'=>$offset,'page_width'=>$page_width, 'total_count'=>$total_count));
    }

    /**
     * List Corporate Shipments Action
     * @author Adegoke Obasa <goke@cottacush.com>
     * @author Bolade Oye <bolade@cottacush.com>
     * @param int $page
     * @return string
     */
    public function actionCorporateshipment($page = 1)
    {
        $offset = ($page - 1) * $this->page_width;

        $parcelAdapter = new ParcelAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        $corporateParcelsResponse = $parcelAdapter->getCorporateParcels(0, 50);
        $corporateParcels = Calypso::getValue($corporateParcelsResponse, 'parcels');
        $totalCount = Calypso::getValue($corporateParcelsResponse, 'total_count');

        $companies = (new CompanyAdapter())->getAllCompanies([]);
        $statuses = ServiceConstant::getStatusRef();

        return $this->render('corporateshipment',[
            'statuses' => $statuses,
            'companies' => $companies,
            'corporateParcels' => $corporateParcels,
            'offset' => $offset,
            'total_count' => $totalCount,
            'page_width' => $this->page_width]);
    }

    public function actionCreditnote()
    {
        return $this->render('creditnote');
    }

    public function actionInvoice()
    {
        return $this->render('invoice');
    }

    public function actionMerchantspending()
    {
        return $this->render('merchants_pending');
    }

    public function actionMerchantsdue($page=1)
    {
        $waybillnumber = Calypso::getValue(Calypso::getInstance()->get(),'waybillnumber',null);

        $page_width = $this->page_width;
        $offset = $page_width * ($page - 1);

        $parcel = new ParcelAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        $response = $parcel->getMerchantParcels(1, null, $offset,$page_width);
        $response = new ResponseHandler($response);
        $data = [];
        if($response->getStatus() ==  ResponseHandler::STATUS_OK){
            $data = $response->getData();
        }
        $total_count = empty($data['total_count'])?0:$data['total_count'];
        return $this->render('merchants_due',array('parcels'=>$data, 'offset'=>$offset, 'total_count'=>$total_count));
    }

    public function actionMerchantspaid()
    {
        return $this->render('merchants_paid');
    }

    /**
     * Print Invoice page
     * @author Olajide Oye <jide@cottacush.com>
     */
    public function actionPrintinvoice() {
        $this->layout = 'print';
        return $this->render('print_invoice');
    }

    /**
     * Print Credit Note page
     * @author Olajide Oye <jide@cottacush.com>
     */
    public function actionPrintcreditnote() {
        $this->layout = 'print';
        return $this->render('print_credit_note');
    }
}

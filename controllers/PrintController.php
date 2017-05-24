<?php
/**
 * Created by PhpStorm.
 * User: Kalu
 * Date: 24/05/2017
 * Time: 12:00
 */

namespace app\controllers;

use Adapter\AdminAdapter;
use Adapter\BankAdapter;
use Adapter\BillingPlanAdapter;
use Adapter\BranchAdapter;
use Adapter\CodTellerAdapter;
use Adapter\CompanyAdapter;
use Adapter\Globals\HttpStatusCodes;
use Adapter\Globals\ServiceConstant;
use Adapter\ParcelAdapter;
use Adapter\RtdTellerAdapter;
use Adapter\UserAdapter;
use Adapter\RefAdapter;
use Adapter\RegionAdapter;
use Adapter\RequestHelper;
use Adapter\ResponseHandler;
use Adapter\Util\Calypso;
use Adapter\Util\Util;
use app\models\BulkShipmentModel;
use app\services\BulkWaybillPrinting;
use app\services\HubService;
use Adapter\TellerAdapter;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Response;
use Adapter\RouteAdapter;
use yii\web\UploadedFile;

class PrintController extends BaseController
{
    /**
     * Prepares the report based based on filters
     * @param int $page
     * @param null $page_width
     * @return string
     */
    public function actionPrintall($page = 1, $page_width = null)
    {

        $page_width = is_null($page_width) ? $this->page_width : $page_width;
        $offset = ($page - 1) * $page_width;

        $filter_params = ['company_id', 'start_pickup_date', 'end_pickup_date', 'start_modified_date', 'end_modified_date', 'for_return', 'parcel_type',
            'status', 'min_weight', 'max_weight', 'min_amount_due', 'max_amount_due', 'cash_on_delivery', 'delivery_type',
            'payment_type', 'shipping_type', 'start_created_date', 'end_created_date', 'created_branch_id', 'route_id', 'request_type',
            'from_branch_id', 'branch_type', 'return_reason_comment', 'business_manager_staff_id',
            'delivery_branch_id', 'with_sales_teller', 'with_cod_teller', 'no_cod_teller'];
        $extra_details = [
            'with_sender',
            'with_sender_address',
            'with_receiver',
            'with_receiver_address',
            'with_route',
            'with_from_branch',
            'with_to_branch'
        ];


        $filters = [];
        foreach ($filter_params as $param) {
            $filters[$param] = trim(Yii::$app->request->get($param));
        }

        foreach ($extra_details as $extra) {
            $filters[$extra] = true;
        }

        $start_modified_date = Yii::$app->request->get('start_modified_date', null);
        $end_modified_date = Yii::$app->request->get('end_modified_date', null);
        $filters['start_modified_date'] = (Util::checkEmpty($start_modified_date)) ? null : $start_modified_date . ' 00:00:00';
        $filters['end_modified_date'] = (Util::checkEmpty($end_modified_date)) ? null : $end_modified_date . ' 23:59:59';

        $start_pickup_date = Yii::$app->request->get('start_pickup_date', null);
        $end_pickup_date = Yii::$app->request->get('end_pickup_date', null);
        $filters['start_pickup_date'] = (Util::checkEmpty($start_pickup_date)) ? null : $start_pickup_date . ' 00:00:00';
        $filters['end_pickup_date'] = (Util::checkEmpty($end_pickup_date)) ? null : $end_pickup_date . ' 23:59:59';


        $start_created_date = Yii::$app->request->get('start_created_date', Util::getToday('/'));
        $end_created_date = Yii::$app->request->get('end_created_date', Util::getToday('/'));

        $filters['start_created_date'] = $start_created_date . ' 00:00:00';
        $filters['end_created_date'] = $end_created_date . ' 23:59:59';

        $filters['offset'] = $offset;
        $filters['count'] = $page_width;
        $filters['with_total_count'] = true;
        //$filters['report'] = 1;
        //$filters['show_both_parent_and_splits'] = 1;

        $parcelAdapter = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $filtered_parcels = $parcelAdapter->getParcelsByFilters(array_filter($filters, 'strlen'));

        $response = new ResponseHandler($filtered_parcels);

        $parcels = [];
        if ($response->getStatus() == ResponseHandler::STATUS_OK) {
            $data = $response->getData();
            $parcels = $data['parcels'];
        } else {
            $this->flashError('Could not load parcels');
        }

        $waybills = [];
        foreach ($parcels as $parcel) {
            $waybills[] = $parcel['waybill_number'];
        }
        (new BulkWaybillPrinting())->createPdf($waybills);

//        $refData = new RefAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
//        $refResponse = new ResponseHandler($refData->getShipmentType());
//        if ($refResponse->getStatus() == ResponseHandler::STATUS_OK) {
//            $serviceType = $refResponse->getData();
//        }
//        $parcelTypeResponse = new ResponseHandler($refData->getparcelType());
//        if ($parcelTypeResponse->getStatus() == ResponseHandler::STATUS_OK) {
//            $parcelType = $parcelTypeResponse->getData();
//        }
//
//
//        $this->layout = 'print';
//
//        return $this->render('printall', array(
//            'parcels' => $parcels,
//            'serviceType' => $serviceType,
//            'parcelType' => $parcelType,
//        ));
    }



}
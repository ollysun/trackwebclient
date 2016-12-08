<?php
/**
 * Created by PhpStorm.
 * User: ELACHI
 * Date: 11/3/2016
 * Time: 1:24 PM
 */

namespace app\modules\api\controllers;


use Adapter\ParcelAdapter;
use Adapter\RequestHelper;
use Adapter\ResponseHandler;
use Yii;
use Adapter\Util\Util;

class ParcelController extends ApiBaseController
{
    private function sanitizeParcel(array $parcel){

        $keys = ['created_by', 'created_branch', 'to_branch', 'from_branch', 'parent', 'parcels', 'created_by', 'sender_id',
        'sender_address_id', 'receiver_id', 'receiver_address_id', 'from_branch_id', 'to_branch_id', 'seal_id', 'created_branch_id',
            'route_id', 'others', 'bank_account_id', 'created_branch_id', 'pos_trans_id',
        ];

        foreach ($keys as $key) {
            if(array_key_exists($key, $parcel)){
                unset($parcel[$key]);
            }
        }

        return $parcel;
    }

    public function actionGet(){
        $waybill_number = \Yii::$app->request->get('waybill_number');
        $parcelAdapter = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());

        $response = $parcelAdapter->getParcelByWayBillNumber($waybill_number);
        $response = new ResponseHandler($response);

        if($response->getStatus() ==  ResponseHandler::STATUS_OK){

            $histories = [];

            $data = $response->getData();
            if(is_array($data)){
                //get histories
                $response = $parcelAdapter->getParcelHistories($waybill_number);
                if($response['status'] == ResponseHandler::STATUS_OK){
                    $histories = $response['data']['history'];
                }

                return $this->sendSuccessResponse(
                    array(
                        'parcel_data' => $this->sanitizeParcel($data),
                        'parcel_history' => $histories
                    ));
            }
            return $this->sendErrorResponse('Parcel not found', self::NotFound);
        }else{
            return $this->sendErrorResponse('Parcel not found', self::NotFound);
        }

    }

    public function actionGetreport($page = 1, $page_width = null){
        $page_width = is_null($page_width) ? $this->page_width : $page_width;
        $offset = ($page - 1) * $page_width;

        $filter_params = ['start_pickup_date', 'end_pickup_date', 'start_modified_date', 'end_modified_date', 'for_return', 'parcel_type',
            'status', 'min_weight', 'max_weight', 'min_amount_due', 'max_amount_due', 'cash_on_delivery', 'delivery_type',
            'payment_type', 'shipping_type', 'start_created_date', 'end_created_date', 'request_type',
            'branch_type', 'return_reason_comment'];
        $extra_details = ['with_receiver', 'with_receiver_address', 'with_sender', 'with_sender_address'];


        $filters = [];
        foreach ($filter_params as $param) {
            $filters[$param] = Yii::$app->request->get($param);
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
        $filters['show_both_parent_and_splits'] = 1;

        $parcelAdapter = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $filtered_parcels = $parcelAdapter->getParcelsByFilters(array_filter($filters, 'strlen'));

        $response = new ResponseHandler($filtered_parcels);

        if ($response->getStatus() == ResponseHandler::STATUS_OK) {
            $data = $response->getData();
            $parcels = $data['parcels'];
            $total_count = $data['total_count'];
        } else {
            return $this->sendErrorResponse('Could not load reports', self::InternalError);
        }

        $parcels = array_map(function($item){ return $this->sanitizeParcel($item);}, $parcels);

        return $this->sendSuccessResponse(['parcels' => $parcels,  'total_count' => $total_count]);
    }

    public function actionCreate()
    {
        $parameters = ['shipment_order_number' => 'order_number', 'shipment_customer_reference' => 'reference_number', 'shipment_consignee_name' => ['required' => true, 'key' => 'receiver_name'], 'shipment_consignee_address1' => ['required' => true, 'key' => 'receiver_address_1'], 'shipment_consignee_address2' => 'receiver_address_2', 'shipment_consignee_city' => ['required' => true, 'key' => 'receiver_city'], 'shipment_consignee_state' => 'receiver_state', 'shipment_consignee_country' => 'receiver_country', 'shipment_consignee_email' => 'receiver_email', 'shipment_consignee_tel' => ['key' => 'receiver_phone_number', 'required' => true], 'shipment_weight' => ['required' => true, 'key' => 'weight'], 'shipment_pieces' => ['required' => true, 'key' => 'no_of_package'], 'shipment_value' => 'package_value',
            'shipment_description_1' => 'description_1', 'shipment_description_2' => 'description_2', 'shipment_sender_name' => 'sender_name', 'shipment_sender_country' => 'sender_country', 'shipment_sender_state' => 'sender_state', 'shipment_sender_city' => 'sender_city', 'shipment_sender_address_1' => 'sender_address_1', 'shipment_sender_address_2' => 'sender_address_2', 'is_cash_on_delivery' => 'cash_on_delivery', 'cash_on_delivery_amount' => 'cash_on_delivery_amount'];

        $data = [];

        $postedData = json_decode(Yii::$app->request->rawBody, true);
        foreach ($parameters as $key => $value) {
            $data_key = $value;
            $is_required = false;
            if(is_array($value)){
                $data_key = $value['key'];
                $is_required = $value['required'];
            }
            $data[$data_key] = array_key_exists($key, $postedData)?$postedData[$key]:null;
            if(empty($data[$data_key]) && $is_required){
                return $this->sendErrorResponse("$key is required", self::BadRequest);
            }
        }

        $parcelAdapter = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());

        $response = $parcelAdapter->createNewParcelFromApi($data);
        $response = new ResponseHandler($response);

        $data = $response->getData();
        if($response->getStatus() ==  ResponseHandler::STATUS_OK){
            return $this->sendSuccessResponse(['waybill_number' => $data['waybill_number']]);
        }else{
            return $this->sendErrorResponse($response->getError(), self::InternalError);
        }
    }

    //tracking
    public function actionStatus(){
        $waybill_number = Yii::$app->request->get('waybill_number');
        if(empty($waybill_number)){
            return $this->sendErrorResponse('Invalid waybill number', self::BadRequest);
        }
        $adapter = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $response = new ResponseHandler($adapter->getParcelStatusForApi($waybill_number));

        if(!$response->isSuccess()){
            return $this->sendErrorResponse('History not fetched', self::InternalError);
        }
        return $this->sendSuccessResponse($response->getData());
    }

    public function actionHistory(){
        $waybill_number = Yii::$app->request->get('waybill_number');
        if(empty($waybill_number)){
            return $this->sendErrorResponse('Invalid waybill number', self::BadRequest);
        }
        $adapter = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $response = new ResponseHandler($adapter->getParcelHistoriesForApi($waybill_number));

        if(!$response->isSuccess()){
            return $this->sendErrorResponse('History not fetched', self::InternalError);
        }
        return $this->sendSuccessResponse($response->getData());
    }
}
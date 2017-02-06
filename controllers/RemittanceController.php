<?php
/**
 * Created by PhpStorm.
 * User: ELACHI
 * Date: 1/23/2017
 * Time: 2:32 PM
 */

namespace app\controllers;


use Adapter\Globals\ServiceConstant;
use Adapter\RemittanceAdapter;
use Adapter\RequestHelper;
use Adapter\ResponseHandler;
use Adapter\Util\Calypso;

class RemittanceController extends BaseController
{
    public function actionIndex1($page = 1, $page_width = null){
        $page_width = is_null($page_width) ? $this->page_width : $page_width;
        $offset = ($page - 1) * $page_width;
        $adapter = new RemittanceAdapter();

        $company_registration_number = \Yii::$app->request->get('company_registration_number');
        $start_date = \Yii::$app->request->get('start_date');
        $end_date = \Yii::$app->request->get('end_date');
        $min_amount = \Yii::$app->request->get('min_amount');
        $max_amount = \Yii::$app->request->get('max_amount');

        $filters = [
            'send_all' => 1, 'with_total_count' => 1,
            'company_registration_number' =>$company_registration_number,
            'start_date' => $start_date, 'end_date' => $end_date,
            'min_amount' => $min_amount, 'max_amount' => $max_amount,
            'offset' => $offset, 'count' => $page_width
        ];

        $result = new ResponseHandler($adapter->getAll($filters));

        if($result->isSuccess()){
            $remittance = $result->getData();
        }else {
            $this->flashError($result->getError());
            $remittance = [];
        }
        return $this->render('index', ['filters' => $filters, 'remittance' => $remittance]);
    }

    public function actionIndex($page = 1, $page_width = null){
        $page_width = is_null($page_width) ? $this->page_width : $page_width;
        $offset = ($page - 1) * $page_width;
        $adapter = new RemittanceAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());

        $ref = \Yii::$app->request->get('ref');
        if($ref) return $this->downloadAdvice($ref);

        if(\Yii::$app->request->isPost){
            $status = \Yii::$app->request->post()['current_status'];
            $company_ids = \Yii::$app->request->post()['companies'];
            $response = new ResponseHandler($adapter->save($company_ids, $status));
            if(!$response->isSuccess()){
                $this->flashError($response->getError());
            }else{
                $ref = $response->getData();
                $this->downloadAdvice($ref);
            }
        }

        $status = \Yii::$app->request->get('status');
        $registration_number = \Yii::$app->request->get('registration_number');
        $start_delivery_date = \Yii::$app->request->get('start_delivery_date');
        $end_delivery_date = \Yii::$app->request->get('end_delivery_date');

        $filters = ['registration_number' => $registration_number,
            'start_delivery_date' => $start_delivery_date,
            'end_delivery_date' => $end_delivery_date,
            'with_total_count' => 1, 'offset' => $offset, 'count' => $page_width, 'status' => $status ? $status : 25];

        $response = new ResponseHandler($adapter->getPaymentAdvice($filters));
        if($response->isSuccess()){
            $payments = $response->getData()['payments'];
            $total = $response->getData()['total_count'];
        }else{
            $this->flashError($response->getError());
            $payments = [];
            $total = 0;
        }

        return $this->render('index', array_merge(['payments' => $payments, 'total_count' => $total,
            'page_width' => $page_width], $filters));
    }

    public function actionDetails(){
        $company_registration_number = \Yii::$app->request->get('reg_no');
        $ref = \Yii::$app->request->get('ref');
        $status = \Yii::$app->request->get('status');

        $filter_by = ['company_registration_number' => $company_registration_number, 'ref' => $ref, 'status' => $status];
        $adapter = new RemittanceAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $response = new ResponseHandler($adapter->getAll($filter_by));
        $parcels = [];
        if($response->isSuccess()){
            $parcels = $response->getData();
        }else{
            $this->flashError($response->getError());
        }
        return $this->render('remittance_parcels', ['parcels' => $parcels]);
    }

    public function downloadAdvice($ref){
        $adapter = new RemittanceAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $response = new ResponseHandler($adapter->getAdviceForDownload(['ref' => $ref]));

        if($response->isSuccess()){
            $payments = $response->getData();
            if(count($payments) < 1){
                $this->flashError('No record found');
                return $this->redirect('index' /*\Yii::$app->getRequest()->getReferrer()*/);
            }
        }else{
            $this->flashError($response->getError());
            return $this->redirect(\Yii::$app->getRequest()->getReferrer());
        }

        $name = 'payment_advice_' . $ref . '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename=' . $name);
        header('Pragma: no-cache');
        header("Expires: 0");
        $stream = fopen("php://output", "w");


        $headers = array('SN', 'Company Name', 'Registration Number', 'Email', 'Phone Number', 'Amount');

        /*if(array_key_exists('with_sales_teller', $filters) && $filters['with_sales_teller'] == '1'){
            $headers[] = 'Sales Banks';
            $headers[] = 'Sales Account No.';
            $headers[] = 'Sales Teller No.';
        }
        if(array_key_exists('with_cod_teller', $filters) && $filters['with_cod_teller'] == '1'){
            $headers[] = 'COD Banks';
            $headers[] = 'COD Account No.';
            $headers[] = 'COD Teller No.';
        }*/
        fputcsv($stream, $headers);


        $serial_number = 1;
        $exportData = [];
        foreach ($payments as $key => $result) {
            $exportData[] = [
                $serial_number++,
                Calypso::getValue($result, 'name'),
                Calypso::getValue($result, 'reg_no'),
                Calypso::getValue($result, 'email'),
                Calypso::getValue($result, 'phone_number'),
                Calypso::getValue($result, 'amount')
            ];

            /*if(array_key_exists('with_sales_teller', $filters) && $filters['with_sales_teller'] == '1'){
                $exportData[] = Calypso::getValue($result, 'teller_bank_name', '');
                $exportData[] = Calypso::getValue($result, 'teller_account_no', '');
                $exportData[] = Calypso::getValue($result, 'teller_teller_no', '');
            }
            if(array_key_exists('with_cod_teller', $filters) && $filters['with_cod_teller'] == '1'){
                $exportData[] = Calypso::getValue($result, 'cod_teller_bank_name', '');
                $exportData[] = Calypso::getValue($result, 'cod_teller_account_no', '');
                $exportData[] = Calypso::getValue($result, 'cod_teller_teller_no', '');
            }*/
        }


        foreach ($exportData as $row) {
            fputcsv($stream, $row);
        }

        fclose($stream);
        exit;
    }

    public function actionPendingparcels(){

    }
}
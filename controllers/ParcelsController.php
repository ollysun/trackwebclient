<?php
/**
 * Created by PhpStorm.
 * User: RotelandO
 * Date: 7/25/15
 * Time: 8:30 AM
 */

namespace app\controllers;


use Adapter\BankAdapter;
use Adapter\BillingPlanAdapter;
use Adapter\CompanyAdapter;
use Adapter\Globals\ServiceConstant;
use Adapter\ParcelAdapter;
use Adapter\RefAdapter;
use Adapter\RegionAdapter;
use Adapter\RequestHelper;
use Adapter\ResponseHandler;
use Adapter\UserAdapter;
use Adapter\Util\Response;
use Adapter\BranchAdapter;
use Adapter\Util\Util;
use app\services\ParcelService;
use Adapter\Util\Calypso;
use Yii;
use yii\helpers\ArrayHelper;

class ParcelsController extends BaseController
{

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionNew()
    {
        //ini_set('memory_limit', '-1');//to be removed

        if (Yii::$app->request->isPost) {

            $data = Yii::$app->request->post();

            $parcelService = new ParcelService();
            $payload = $parcelService->buildPostData($data);

            //$this->sendAsyncFormResponse(1, $payload, "Parcel.onFormErrorCallback");

            if (isset($payload['status'])) {
                $this->sendAsyncFormResponse(1, array('message' => implode('<br />', $payload['messages'])), "Parcel.onFormErrorCallback");

            } else {
                $parcel = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
                $response = $parcel->createNewParcel(json_encode($payload));
                if ($response['status'] === Response::STATUS_OK) {
                    $this->sendAsyncFormResponse(1, $response['data'], "Parcel.onFormSuccessCallback");
                } else {
                    $payload['response'] = $response;

                    $this->sendAsyncFormResponse(1, $response, "Parcel.onFormErrorCallback");
                    //$this->sendAsyncFormResponse(1, $response, "Parcel.onFormErrorCallback");
                }
            }
        }

        $parcel = [];
        $id = Yii::$app->request->get('id');
        $edit = Yii::$app->request->get('edit');
        $pickupRequestId = Yii::$app->request->get('pickup_request_id');
        $shipmentRequestId = Yii::$app->request->get('shipment_request_id');


        $companyAdapter = new CompanyAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $companies = $companyAdapter->getAllCompanies(['status' => ServiceConstant::ACTIVE]);


        $this_company = null;
        if(Calypso::isCooperateUser()){
            $this_company_id = Calypso::getInstance()->session('user_session')['company']['id'];
            foreach ($companies as $comp) {
                if($comp['id'] == $this_company_id){
                    $this_company = $comp;
                }
            }
            $parcel = ParcelService::initializeCooperateShipment($this_company);
        }else{
            if (isset($id)) {
                $parcel = ParcelService::getParcelDetails($id);
            } else if (isset($pickupRequestId)) {
                $pickupRequest = $companyAdapter->getPickupRequest($pickupRequestId);
                $parcel = ParcelService::convertPickupRequest($pickupRequest);
            } else if (isset($shipmentRequestId)) {
                $shipmentRequest = $companyAdapter->getShipmentRequest($shipmentRequestId);
                $parcel = ParcelService::convertShipmentRequest($shipmentRequest);
            }
        }


        if (isset($edit)) {
            $parcel['info']['edit'] = true;
        }

        $refData = new RefAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());

        $banks = $refData->getBanks();
        $shipmentType = $refData->getShipmentType();
        $deliveryType = $refData->getdeliveryType();
        $parcelType = $refData->getparcelType();
        $paymentMethod = $refData->getPaymentMethods();
        $countries = $refData->getCountries();
        $states = (new ResponseHandler($refData->getStates(ServiceConstant::DEFAULT_COUNTRY)))->getData();

        $hubAdp = new BranchAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $centres = $hubAdp->getAllHubs(false);
        $centres = new ResponseHandler($centres);
        $hubs_list = $centres->getStatus() == ResponseHandler::STATUS_OK ? $centres->getData() : [];

        $user = Calypso::getInstance()->session('user_session');
        $hubAdp = new BranchAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $centres = $hubAdp->getCentres(Calypso::isCooperateUser()?null:$user['branch']['id'], 0, $this->page_width, false);
        $centres = new ResponseHandler($centres);
        $centres_list = $centres->getStatus() == ResponseHandler::STATUS_OK ? $centres->getData() : [];

        $centres_list = array_merge($centres_list, $hubs_list);

        $billingPlanAdapter = new BillingPlanAdapter();
        /*$billingPlans = $billingPlanAdapter->getBillingPlans(['no_paginate' => '1', 'type' => BillingPlanAdapter::TYPE_WEIGHT_AND_ON_FORWARDING, 'status' => ServiceConstant::ACTIVE]);*/

        $billingPlans = $billingPlanAdapter->getCompanyBillingPlans();

        //dd($billingPlans);

        $billingPlans = ArrayHelper::map($billingPlans, 'id', 'name', 'company_id', 'p');

        //dd($billingPlans);

        $viewBag = array(
            'Banks' => $banks,
            'ShipmentType' => $shipmentType,
            'deliveryType' => $deliveryType,
            'parcelType' => $parcelType,
            'countries' => $countries,
            'states' => $states,
            'paymentMethod' => $paymentMethod,
            'centres' => $centres_list,
            'branch' => Calypso::isCooperateUser()?null:$user['branch'],
            'company' => $this_company,
            'parcel' => $parcel,
            'companies' => $companies,
            'billingPlans' => $billingPlans
        );

        return $this->render('new', $viewBag);
    }

    /**
     * Ajax calls to get states when a country is selected
     */
    public function actionGetstates()
    {
        $country_id = \Yii::$app->request->get('id');
        if (!isset($country_id)) {
            return $this->sendErrorResponse("Invalid parameter(s) sent!", null);
        }

        $cacheKey = Calypso::getValue(Yii::$app->params, 'cacheAppPrefix') . "states_$country_id";

        $states = Yii::$app->cache->get($cacheKey);
        if (!$states) {
            $refData = new RefAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
            $response = new ResponseHandler($refData->getStates($country_id));

            if (!$response->isSuccess()) {
                return $this->sendErrorResponse($response->getError(), null);
            }

            $states = $response->getData();
//            Yii::$app->cache->set($cacheKey, $states, 3600);
        }

        return $this->sendSuccessResponse($states);
    }

    /**
     * Ajax calls to fetch cities when a state is selected
     */
    public function actionGetcities()
    {

        $state_id = \Yii::$app->request->get('id');
        if (!isset($state_id)) {
            return $this->sendErrorResponse("Invalid parameter(s) sent!", null);
        }

        $cacheKey = Calypso::getValue(Yii::$app->params, 'cacheAppPrefix') . "cities_$state_id";

        $cities = Yii::$app->cache->get($cacheKey);
        if (!$cities) {
            $refData = new RegionAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
            $response = new ResponseHandler($refData->getAllCity(1, 1, $state_id));

            if (!$response->isSuccess()) {
                return $this->sendErrorResponse($response->getError(), null);
            }

            $cities = $response->getData();
//            Yii::$app->cache->set($cacheKey, $cities, 3600);
        }

        return $this->sendSuccessResponse($cities);
    }

    /**
     * Ajax calls to get user details using phone number
     */
    public function actionUserdetails()
    {

        $term = \Yii::$app->request->get('term');
        if (!isset($term)) {
            return $this->sendErrorResponse("Invalid parameter(s) sent!", null);
        }

        $userData = new UserAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $userInfo = $userData->getUserDetails($term);
        if ($userInfo['status'] === ResponseHandler::STATUS_OK) {
            return $this->sendSuccessResponse($userInfo['data']);
        } else {
            return $this->sendErrorResponse($userInfo['message'], null);
        }
    }

    /**
     * Ajax calls to get Account details of sender
     */
    public function actionAccountdetails()
    {

        $owner_id = \Yii::$app->request->get('owner_id');
        if (!isset($owner_id)) {
            return $this->sendErrorResponse("Invalid parameter(s) sent!", null);
        }

        $bankAdapter = new BankAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $bankInfo = $bankAdapter->getSenderBankAccout($owner_id);
        if ($bankInfo['status'] === ResponseHandler::STATUS_OK) {
            $resp = [];
            if (!empty($bankInfo['data'])) {
                $resp = $bankInfo['data'][0];
            }

            return $this->sendSuccessResponse($resp);
        } else {
            return $this->sendErrorResponse($bankInfo['message'], null);
        }
    }

    /**
     * This action (ajax) calculates the billing amounts for parcels
     *
     * @return array
     */
    public function actionCalculatebilling()
    {

        $rawData = \Yii::$app->request->getRawBody();
        $postParams = json_decode($rawData, true);
        $parcelSrv = new ParcelService();
        $data = $parcelSrv->buildBillingCalculationData($postParams);

        if (!empty($data['error'])) {
            return $this->sendErrorResponse(implode($data['error']), null);
        }

        $parcelAdp = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $response = new ResponseHandler($parcelAdp->calcBilling($data['payload']));

        if ($response->isSuccess()) {
            return $this->sendSuccessResponse($response->getData());
        } else {
            $error_message = $response->getError();
            return $this->sendErrorResponse($error_message, null);
        }

    }

    public function actionGetquote(){
        $rawData = \Yii::$app->request->getRawBody();
        $postParams = json_decode($rawData, true);
        $parcelSrv = new ParcelService();
        $data = $parcelSrv->buildBillingCalculationData($postParams);

        if (!empty($data['error'])) {
            return $this->sendErrorResponse(implode($data['error']), null);
        }

        //return $this->sendSuccessResponse($data['payload']);

        $parcelAdp = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $response = new ResponseHandler($parcelAdp->getQuote($data['payload']));

        if ($response->isSuccess()) {
            $quote = $response->getData();
            $quote['total_amount'] = number_format($quote['total_amount'], 2);
            $quote['discount'] = number_format($quote['discount'], 2);
            $quote['amount_due'] = number_format($quote['amount_due'], 2);
            $quote['discount_percentage'] = number_format($quote['discount_percentage'], 2);
            $quote['vat'] = number_format($quote['vat'], 2);
            $quote['gross_amount'] = number_format($quote['gross_amount'], 2);
            return $this->sendSuccessResponse($quote);
        } else {
            $error_message = $response->getError();
            return $this->sendErrorResponse($error_message, null);
        }
    }

    /**
     * @return string
     * This function gets posted value as 'set' and also gets an uploaded csv with maximum of 1000 entries
     * It removes the title which enforces also that the right format is followed
     * It then sends it to the middleware for further processing
     */
    public function actionDiscount()
    {
        $setting = Yii::$app->getRequest()->post('set');

        if(!empty($_FILES['batchcsv']['name'])) {

            $fileName=$_FILES['batchcsv']['name'];
            if( stristr($fileName,'.csv')==".csv"){
                $fh = fopen($_FILES['batchcsv']['tmp_name'], 'r') or die($this->flashError("Unable to open file!"));

                if (count($fh) > 1000) {
                    $this->flashError("Waybill Batching is not allowed to exceed 1000, please check as required");
                }
                else
                    while(! feof($fh))
                        $theArray[]=fgetcsv($fh,200);
                $titles=$theArray[0];
                if($titles[0]=='WayBill Number' && $titles[1]=='Percentage Discount' && $titles[2]=='Fixed Discount'){
                    array_shift($theArray);
                    //var_dump($theArray);die;
                }
                else {
                    $this->flashError("Please use the standard Formatted Sample. You can download it <a href='samplecsv'><i class=\"fa fa-hand-o-right\" aria-hidden=\"true\"></i> here</a>");
                    return $this->render('batch_discount');
                }
                $param['override']=$setting;
                $param['data']=$theArray;
                fclose($fh);
                $batching = new ParcelAdapter();
                $response['status']=$batching->batchDiscount($param);
                // var_dump($param);die();
                if ($response['status'] == Response::STATUS_OK) {
                    Yii::$app->session->setFlash('success', 'Awesome, All Done!.');
                } else {
                    Yii::$app->session->setFlash('danger', 'There was a problem executing the full batch discounting. ');
                }
            }
            else
                $this->flashError("Only CSV Documents are accepted");
        }
        return $this->render('batch_discount');
    }

    /**
     * This function generates a sample CSV file format that the system has been
     * designed to work with. This is done so as to standardize the data format
     * The CSV is expected to be filled and uploaded with the header (title row)
     */
    public function actionSamplecsv(){
        // Headings and rows
        $headings = array('WayBill Number', 'Percentage Discount', 'Fixed Discount');
        $array = array(
        );
        $fh = fopen('php://output', 'w');
        ob_start();
        fputcsv($fh, $headings);
// Future System creation of data code
//        if (! empty($array)) {
//            foreach ($array as $item) {
//                fputcsv($fh, $item);
//            }
//        }

        $string = ob_get_clean();
        $filename='samplecsv';

// Output CSV-specific headers
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private",false);
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"$filename.csv\";" );
        header("Content-Transfer-Encoding: binary");
        exit($string);
    }
}
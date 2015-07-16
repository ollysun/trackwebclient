<?php

namespace app\controllers;

use Adapter\ParcelAdapter;
use Adapter\RefAdapter;
use Adapter\UserAdapter;
use app\services\ParcelService;
use Yii;
use Adapter\RequestHelper;
use Adapter\ResponseHandler;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use Adapter\AdminAdapter;
use Adapter\Util\Calypso;
use Adapter\Globals\ServiceConstant;
use Adapter\Util\Response;

class SiteController extends BaseController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['get'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }
    public function beforeAction($action){
        if($action->id != 'login'){
            $s = Calypso::getInstance()->session('user_session');

            if(!$s){
               // Calypso::getInstance()->AppRedirect('site','login');
                return $this->redirect('site/logout');
            }
        }
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }
    public function actionIndex()
    {

        $session_data = Calypso::getInstance()->session('user_session');

        return $this->render('index',array('session_data'=>$session_data));
    }
    public function actionGerraout(){
        Calypso::getInstance()->session('user_session',null);

        Yii::$app->user->logout();
        session_destroy();
        return $this->redirect('logout');
    }
    public function actionLogin()
    {
        $this->enableCsrfValidation = false;
        $this->layout = 'login';
        $data = (Yii::$app->request->post());
        if($data){
            $admin = new AdminAdapter();
            $response = $admin->login($data['email'],$data['password']);
            $response = new ResponseHandler($response);
            if($response->getStatus() == ResponseHandler::STATUS_OK){
                $data = $response->getData();
                if($data != null && isset($data['id'])){
                    RequestHelper::setClientID($data['id']);
                }
                Calypso::getInstance()->session("user_session",$response->getData());
                return $this->redirect('processedparcels');
            }else{
                Calypso::getInstance()->setPageData("Invalid Login. Check username and password and try again");
            }

        }
        return $this->render('login');
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->redirect('login');
    }

    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionNewparcel()
    {

        if(Yii::$app->request->isPost){
            $data = Yii::$app->request->post();

            $parcelService = new ParcelService();
            $payload = $parcelService->buildPostData($data);

            $parcel = new ParcelAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
            $response = $parcel->createNewParcel(json_encode($payload));
            if($response['status'] === Response::STATUS_OK) {
                Yii::$app->session->setFlash('success', 'Parcel has been created successfully. <a href="#" class="btn btn-mini">Print Waybill</a>');
                Yii::$app->response->redirect('parcels');
            } else {
                Yii::$app->session->setFlash('danger', 'There was a problem creating the value. Please try again.');
                Yii::$app->response->redirect('newparcel');
            }
        }
        $refData = new RefAdapter();

        $banks = $refData->getBanks();
        $shipmentType = $refData->getShipmentType();
        $deliveryType = $refData->getdeliveryType();
        $parcelType = $refData->getparcelType();
        $paymentMethod = $refData->getPaymentMethods();
        $countries = $refData->getCountries();

        return $this->render('new_parcel',array(
            'Banks'=>$banks,
            'ShipmentType' => $shipmentType,
            'deliveryType'=>$deliveryType,
            'parcelType'=>$parcelType,
            'countries'=>$countries,
            'paymentMethod'=>$paymentMethod
        ));
    }

    public function actionParcels()
    {
        $parcel = new ParcelAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        if(isset(Calypso::getInstance()->get()->from,Calypso::getInstance()->get()->to)){
            $from_date = Calypso::getInstance()->get()->from.' 00:00:00';
            $to_date = Calypso::getInstance()->get()->to.' 23:59:59';
            $filter = isset(Calypso::getInstance()->get()->date_filter) ? Calypso::getInstance()->get()->date_filter : '-1';
            $response = $parcel->getFilterParcelsByDateAndStatus($from_date,$to_date,$filter);
        }
        elseif(isset(Calypso::getInstance()->get()->search) ){
            $search = Calypso::getInstance()->get()->search;
            $response = $parcel->getSearchParcels('-1',$search);
        }else{
            $response = $parcel->getParcels();
        }
        $response = new ResponseHandler($response);
        $data = [];
        if($response->getStatus() ==  ResponseHandler::STATUS_OK){
            $data = $response->getData();
        }
        return $this->render('parcels',array('parcels'=>$data));
    }

    public function actionProcessedparcels()
    {
        $parcel = new ParcelAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        if(isset(Calypso::getInstance()->get()->from,Calypso::getInstance()->get()->to)){
            $from_date = Calypso::getInstance()->get()->from.' 00:00:00';
            $to_date = Calypso::getInstance()->get()->to.' 23:59:59';
            $filter = isset(Calypso::getInstance()->get()->date_filter) ? Calypso::getInstance()->get()->date_filter : '-1';
            $response = $parcel->getFilterParcelsByDateAndStatus($from_date,$to_date,$filter);
        }
        elseif(isset(Calypso::getInstance()->get()->search) ){
            $search = Calypso::getInstance()->get()->search;
            $response = $parcel->getSearchParcels('-1',$search);
        }else{
            $response = $parcel->getNewParcelsByDate(date('Y-m-d'));
        }
        $response = new ResponseHandler($response);
        $data = [];
        if($response->getStatus() ==  ResponseHandler::STATUS_OK){
            $data = $response->getData();
        }
        return $this->render('processed_parcels',array('parcels'=>$data));
    }

     public function actionParcelsfordelivery()
    {
        $parcel = new ParcelAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        if(isset(Calypso::getInstance()->get()->from,Calypso::getInstance()->get()->to)){
            $from_date = Calypso::getInstance()->get()->from.' 00:00:00';
            $to_date = Calypso::getInstance()->get()->to.' 23:59:59';
            $filter = isset(Calypso::getInstance()->get()->date_filter) ? Calypso::getInstance()->get()->date_filter : '-1';
            $response = $parcel->getFilterParcelsByDateAndStatus($from_date,$to_date,$filter);
        }
        elseif(isset(Calypso::getInstance()->get()->search) ){
            $search = Calypso::getInstance()->get()->search;
            $response = $parcel->getSearchParcels('-1',$search);
        }else{
            $response = $parcel->getParcels(ServiceConstant::FOR_DELIVERY);
        }
        $response = new ResponseHandler($response);
        $data = [];
        if($response->getStatus() ==  ResponseHandler::STATUS_OK){
            $data = $response->getData();
        }
        return $this->render('parcels_for_delivery',array('parcels'=>$data));
    }

    public function actionParcelsforsweep()
    {
        $parcel = new ParcelAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        if(isset(Calypso::getInstance()->get()->from,Calypso::getInstance()->get()->to)){
            $from_date = Calypso::getInstance()->get()->from.' 00:00:00';
            $to_date = Calypso::getInstance()->get()->to.' 23:59:59';
            $filter = isset(Calypso::getInstance()->get()->date_filter) ? Calypso::getInstance()->get()->date_filter : '-1';
            $response = $parcel->getFilterParcelsByDateAndStatus($from_date,$to_date,$filter);
        }
        elseif(isset(Calypso::getInstance()->get()->search) ){
            $search = Calypso::getInstance()->get()->search;
            $response = $parcel->getSearchParcels('-1',$search);
        }else{
            $response = $parcel->getParcels(ServiceConstant::FOR_SWEEPER);
        }
        $response = new ResponseHandler($response);
        $data = [];
        if($response->getStatus() ==  ResponseHandler::STATUS_OK){
            $data = $response->getData();
        }
        return $this->render('parcels_for_sweep',array('parcels'=>$data));
    }
    public function actionViewwaybill()
    {
        $data = [];
        if(isset(Calypso::getInstance()->get()->id)){
            $id = Calypso::getInstance()->get()->id;
            $parcel = new ParcelAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
            $response = $parcel->getOneParcel($id);
            $response = new ResponseHandler($response);
            if($response->getStatus() == ResponseHandler::STATUS_OK){
                $data = $response->getData();
            }
        }


        return $this->render('view_waybill',array('parcelData'=>$data));
    }

    /**
     * Ajax calls to get states when a country is selected
     */
    public function actionGetstates() {

        $country_id = \Yii::$app->request->get('id');
        if(!isset($country_id)) {
            return $this->sendErrorResponse("Invalid parameter(s) sent!", null);
        }

        $refData = new RefAdapter();
        $states = $refData->getStates($country_id);
        if ($states['status'] === ResponseHandler::STATUS_OK) {
            return $this->sendSuccessResponse($states['data']);
        } else {
            return $this->sendErrorResponse($states['message'], null);
        }
    }

    /**
     * Ajax calls to get states when a country is selected
     */
    public function actionUserdetails() {

        $term = \Yii::$app->request->get('term');
        if(!isset($term)) {
            return $this->sendErrorResponse("Invalid parameter(s) sent!", null);
        }

        $userData = new UserAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        $userInfo = $userData->getUserDetails($term);
        if ($userInfo['status'] === ResponseHandler::STATUS_OK) {
            return $this->sendSuccessResponse($userInfo['data']);
        } else {
            return $this->sendErrorResponse($userInfo['message'], null);
        }
    }

    public function actionPrintwaybill()
    {
        $this->layout = 'waybill';
        return $this->render('print_waybill');
    }

    public function actionManagebranches()
    {
        return $this->render('managehubs');
    }
    public function actionManageecs()
    {
        return $this->render('manageecs');
    }
}

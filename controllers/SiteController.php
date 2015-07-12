<?php

namespace app\controllers;

use Adapter\ParcelAdapter;
use Adapter\RefAdapter;
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

class SiteController extends Controller
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
                    'logout' => ['post'],
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
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }
    public function actionIndex()
    {
        if(!Calypso::getInstance()->isLoggedIn()){
            Calypso::getInstance()->AppRedirect('site','login');
        }
        $session_data = Calypso::getInstance()->session('user_session');

        return $this->render('index',array('session_data'=>$session_data));
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
                return $this->redirect('/');
            }else{
                Calypso::getInstance()->setPageData("Invalid Login. Check username and password and try again");
            }

        }
        return $this->render('login');
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        Calypso::getInstance()->unsetSession();
        Calypso::getInstance()->AppRedirect('site','login');
        return $this->goHome();
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
        $data = (Yii::$app->request->post());
        if($data){
            $senderData = array();
            $receiverData = array();
            $addressData = array();

            $payload = [];
            $senderData['firstname'] = $data['firstname']['shipper'];
            $senderData['lastname'] = $data['lastname']['shipper'];
            $senderData['phone'] = $data['phone']['shipper'];
            $senderData['email'] = $data['email']['shipper'];

            $receiverData['firstname'] = $data['firstname']['receiver'];
            $receiverData['lastname'] = $data['lastname']['receiver'];
            $receiverData['phone'] = $data['phone']['receiver'];
            $receiverData['email'] = $data['email']['receiver'];

            $receiverAddressData['id'] = null;
            $receiverAddressData['street1'] = $data['address']['shipper'][0];
            $receiverAddressData['street2'] = $data['address']['shipper'][1];
            $receiverAddressData['city'] = $data['city']['shipper'];
            $receiverAddressData['state_id'] = $data['state']['shipper'];
            $receiverAddressData['country_id'] = $data['country']['shipper'];
            $bankData['account_name'] = $data['account_name'];
            $bankData['bank_id'] = $data['account_name'];
            $bankData['account_no'] = $data['account_name'];
            $bankData['sort_code'] = $data['sort_code'];
            $bankData['id'] = null;



            $payload['sender'] = $senderData;
            $payload['receiver'] = $receiverData;
            $payload['sender_address'] = $addressData;
            $payload['receiver_address'] = $receiverAddressData;
        }
        $refData = new RefAdapter();

        $banks = $refData->getBanks();
        $shipmentType = $refData->getShipmentType();
        $deliveryType = $refData->getdeliveryType();
        $parcelType = $refData->getparcelType();

        return $this->render('new_parcel',array('Banks'=>$banks,'ShipmentType' => $shipmentType,'deliveryType'=>$deliveryType,'parcelType'=>$parcelType));
    }

    public function actionParcels()
    {
        $parcel = new ParcelAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        if(isset(Calypso::getInstance()->get()->search,Calypso::getInstance()->get()->filter) ){
            $search = Calypso::getInstance()->get()->search;
            $filter = Calypso::getInstance()->get()->filter;
            $response = $parcel->getSearchParcels($filter,$search);
        }else{
            $response = $parcel->getParcels(null);
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
        $response = $parcel->getParcels(ServiceConstant::FOR_ARRIVAL);
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
        $response = $parcel->getParcels(ServiceConstant::FOR_DELIVERY);
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
        $response = $parcel->getParcels(ServiceConstant::FOR_SWEEPER);
        $response = new ResponseHandler($response);
        $data = [];
        if($response->getStatus() ==  ResponseHandler::STATUS_OK){
            $data = $response->getData();
        }
        return $this->render('parcels_for_sweep',array('parcels'=>$data));
    }
    /*
    public function actionParcelscollected()
    {
        $parcel = new ParcelAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        $response = $parcel->getParcels(ServiceConstant::COLLECTED);
        $response = new ResponseHandler($response);
        $data = [];
        if($response->getStatus() ==  ResponseHandler::STATUS_OK){
            $data = $response->getData();
        }
        return $this->render('parcels_for_sweep',array('parcels'=>$data));
    }
    public function actionParcelsintransit()
    {
        $parcel = new ParcelAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        $response = $parcel->getParcels(ServiceConstant::IN_TRANSIT);
        $response = new ResponseHandler($response);
        $data = [];
        if($response->getStatus() ==  ResponseHandler::STATUS_OK){
            $data = $response->getData();
        }
        return $this->render('parcels_for_sweep',array('parcels'=>$data));
    }
    public function actionParcelscancelled()
    {
        $parcel = new ParcelAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        $response = $parcel->getParcels(ServiceConstant::CANCELLED);
        $response = new ResponseHandler($response);
        $data = [];
        if($response->getStatus() ==  ResponseHandler::STATUS_OK){
            $data = $response->getData();
        }
        return $this->render('parcels_for_sweep',array('parcels'=>$data));
    }
    public function actionParcelsdelivered()
    {
        $parcel = new ParcelAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        $response = $parcel->getParcels(ServiceConstant::DELIVERED);
        $response = new ResponseHandler($response);
        $data = [];
        if($response->getStatus() ==  ResponseHandler::STATUS_OK){
            $data = $response->getData();
        }
        return $this->render('parcels_for_sweep',array('parcels'=>$data));
    }
    */
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
}

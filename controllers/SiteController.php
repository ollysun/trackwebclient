<?php

namespace app\controllers;

use Adapter\RefAdapter;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use Adapter\AdminAdapter;
use Adapter\Util\Calypso;

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
        /*$this->enableCsrfValidation = false;
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $this->layout = 'login';

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }*/
        $this->enableCsrfValidation = false;
        $this->layout = 'login';
        $data = (Yii::$app->request->post());
        if($data){
            $admin = new AdminAdapter();
            $response = $admin->login($data['email'],$data['password']);
            if($response['status'] == 1){
                Calypso::getInstance()->session("user_session",$response['data']);
               // Calypso::getInstance()->AppRedirect('site','');
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

            $addressData['id'] = null;
            $addressData['street1'] = $data['address']['shipper'][0];
            $addressData['street2'] = $data['address']['shipper'][1];
            $addressData['city'] = $data['city']['shipper'];
            //$addressData['state_id'] = $data['state']['shipper'];
            //$addressData['country_id'] = $data['country']['shipper'];

            $receiverAddressData['id'] = null;
            $receiverAddressData['street1'] = $data['address']['shipper'][0];
            $receiverAddressData['street2'] = $data['address']['shipper'][1];
            $receiverAddressData['city'] = $data['city']['shipper'];
           // $receiverAddressData['state_id'] = $data['state']['shipper'];
            //$receiverAddressData['country_id'] = $data['country']['shipper'];

            $bankData['account_name'] = $data['account_name'];
            $bankData['bank_id'] = $data['account_name'];
            $bankData['account_no'] = $data['account_name'];
           // $bankData['sort_code'] = $data['sort_code'];
            $bankData['id'] = null;



            $payload['sender'] = $senderData;
            $payload['receiver'] = $receiverData;
            $payload['sender_address'] = $addressData;
            $payload['receiver_address'] = $receiverAddressData;

            //$receiverData[''] = $data['$receiverData'];
            //print_r($data);
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
        return $this->render('parcels');
    }

    public function actionProcessedparcels()
    {
        return $this->render('processed_parcels');
    }
}
<?php

namespace app\controllers;

use Adapter\BankAdapter;
use Adapter\BranchAdapter;
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
        if(!in_array($action->id,array('logout','changepassword','login','gerraout','site'))){
            $s = Calypso::getInstance()->session('user_session');
            if(!$s){
               // Calypso::getInstance()->AppRedirect('site','login');
                return $this->redirect(['site/logout']);
            }
        }
        $this->enableCsrfValidation = false;
        if(Calypso::getInstance()->cookie('page_width')){
            $this->page_width = Calypso::getInstance()->cookie('page_width');
        }
//        var_dump($action->id);
//        exit;
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
                if($data['created_date']==$data['modified_date'] && $data['status']== ServiceConstant::INACTIVE){
                    return $this->render('changepassword');
                }
                return $this->redirect('/site');
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
            $error = 1;
            $data = Yii::$app->request->post();

            $parcelService = new ParcelService();
            $payload = $parcelService->buildPostData($data);
            $flash_msg = '';
            if(isset($payload['status'])) {
                $errorMessages = implode('<br />', $payload['messages']);
                //Yii::$app->session->setFlash('danger', $errorMessages);
                $flash_msg = $errorMessages;
            } else {

                $parcel = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
                $response = $parcel->createNewParcel(json_encode($payload));
                if ($response['status'] === Response::STATUS_OK) {
                   // Yii::$app->response->redirect("viewwaybill?id={$response['data']['id']}");
                    $flash_msg = "viewwaybill?id=".$response['data']['id'];
                    $error = 0;
                } else {
                    //$this->flashError('There was a problem creating the value. Please try again.');
                    $flash_msg =  ('There was a problem creating the value. Please try again. #Reason:'.$response['message']);
                }
            }
            echo "<script>window.top.getServerResponse('".$error."','".$flash_msg."');</script>";
        }


        $refData = new RefAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());

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

    public function actionViewwaybill()
    {
        $data = [];
        $id = "-1";
        if(isset(Calypso::getInstance()->get()->id)){
            $id = Calypso::getInstance()->get()->id;
            /*$parcel = new ParcelAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
            $response = $parcel->getOneParcel($id);
            $response = new ResponseHandler($response);
            if($response->getStatus() == ResponseHandler::STATUS_OK){
                $data = $response->getData();
            }*/
        }

        return $this->redirect("/shipments/view?id={$id}");
        //return $this->render('view_waybill',array('parcelData'=>$data,'id'=> $id));
    }

    /**
     * It requires atleast a state_id or branch_id, or both
     * @return array
     */
    public function actionGetbranches(){
        $state_id = \Yii::$app->request->get('id');
        $branch_id = \Yii::$app->request->get('branch_id');
        if(!isset($state_id)) {
            return $this->sendErrorResponse("Invalid parameter(s) sent!", null);
        }
        $refData = new RefAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        $branches = $refData->getBranch($state_id,$branch_id);
        if ($branches['status'] === ResponseHandler::STATUS_OK) {
            return $this->sendSuccessResponse($branches['data']);
        } else {
            return $this->sendErrorResponse($branches['message'], null);
        }
    }
    public function actionValidatestaff(){
        $staff_id = \Yii::$app->request->get('staff_id');
        if(!isset($staff_id)) {
            return $this->sendErrorResponse("Invalid parameter(s) sent!", null);
        }
        $adminAdp = new AdminAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        $response = $adminAdp->getStaffByStaffID($staff_id);
        $response = new ResponseHandler($response);
        if($response->getStatus() == ResponseHandler::STATUS_OK){
            return $this->sendSuccessResponse($response->getData());
        } else {
            return $this->sendErrorResponse($response->getError(), null);
        }
    }
    public function actionCheckinparcel(){
        if(isset(Calypso::getInstance()->post()->held_by_id,Calypso::getInstance()->post()->waybill_numbers)){
            $parcel = new ParcelAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
            $response = $parcel->moveToArrival([
                'held_by_id' => Calypso::getInstance()->post()->held_by_id,
                'waybill_numbers' => (Calypso::getInstance()->post()->waybill_numbers)
            ]);
            $response = new ResponseHandler($response);
            if($response->getStatus() == ResponseHandler::STATUS_OK){
                return $this->sendSuccessResponse($response->getData());
            } else {
                return $this->sendErrorResponse($response->getError(), null);
            }
        }else{
            return $this->sendErrorResponse("Invalid data", null);
        }
    }
    public function actionMovetofordelivery(){
        if(isset(Calypso::getInstance()->post()->waybill_numbers)){
            $parcel = new ParcelAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
            $response = $parcel->moveForDelivery([
                'waybill_numbers' => (Calypso::getInstance()->post()->waybill_numbers)
            ]);
            $response = new ResponseHandler($response);
            if($response->getStatus() == ResponseHandler::STATUS_OK){
                return $this->sendSuccessResponse($response->getData());
            } else {
                return $this->sendErrorResponse($response->getError(), null);
            }
        }else{
            return $this->sendErrorResponse("Invalid data", null);
        }
    }

    public function actionGetarrivedparcel(){
        $staff_no = \Yii::$app->request->get('staff_no');
        if(!isset($staff_no)) {
            return $this->sendErrorResponse("Invalid parameter(s) sent!", null);
        }
        $parcel = new  ParcelAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        $response = $parcel->getParcel($staff_no,ServiceConstant::IN_TRANSIT);

        if ($response['status'] === ResponseHandler::STATUS_OK) {
            return $this->sendSuccessResponse($response['data']);
        } else {
            return $this->sendErrorResponse($response['message'], null);
        }
    }

    public function actionPrintwaybill()
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
        $this->layout = 'waybill';
        return $this->render('print_waybill',array('parcelData'=>$data));
    }

    /**
     * Ajax calls to get Branch details
     */
    public function actionBranchdetails(){
        $branch_id = \Yii::$app->request->get('id');
        if(!isset($branch_id)) {
            return $this->sendErrorResponse("Invalid parameter(s) sent!", null);
        }
        $refData = new RefAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        $branch = $refData->getBranchbyId($branch_id);
        if ($branch['status'] === ResponseHandler::STATUS_OK) {
            return $this->sendSuccessResponse($branch['data']);
        } else {
            return $this->sendErrorResponse($branch['message'], null);
        }
    }

    public function actionHubnextdestination()
    {
        $parcelsAdapter = new ParcelAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());

        if(\Yii::$app->request->isPost) {
            $branch = \Yii::$app->request->post('branch');
            $waybill_numbers = \Yii::$app->request->post('waybills');
            if(!isset($branch) || empty($waybill_numbers)) {
                $this->flashError('Please ensure you set destinations at least a (one) for the parcels');
            }

            $postParams['waybill_numbers'] = implode(',', $waybill_numbers);
            $postParams['to_branch_id'] = $branch;
            $response = $parcelsAdapter->moveToForSweeper($postParams);
            if($response['status'] === ResponseHandler::STATUS_OK) {
                $this->flashSuccess('Parcels have been successfully moved to the next destination. <a href="hubmovetodelivery">Generate Manifest</a>');
            } else {
                $this->flashError('An error occured while trying to move parcels to next destination. Please try again.');
            }
        }
        $user_session = Calypso::getInstance()->session("user_session");
        $parcelsAdapter = new ParcelAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        $arrival_parcels = $parcelsAdapter->getParcelsForNextDestination(ServiceConstant::FOR_ARRIVAL, $user_session['branch_id']);
        if($arrival_parcels['status'] === ResponseHandler::STATUS_OK) {
            $viewData['parcel_next'] = $arrival_parcels['data'];
        } else {
            $this->flashError('An error occured while trying to fetch parcels. Please try again.');
            $viewData['parcel_next'] = [];
        }
        return $this->render('hub_next_destination', $viewData);
    }

    /**
     * Ajax calls to get all hubs
     */
    public function actionAllhubs() {

        $branchAdapter = new BranchAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        $allHubs = $branchAdapter->getAllHubs();
        if ($allHubs['status'] === ResponseHandler::STATUS_OK) {
            return $this->sendSuccessResponse($allHubs['data']);
        } else {
            return $this->sendErrorResponse($allHubs['message'], null);
        }
    }

    /**
     * Ajax calls to get all ec in the present hub
     */
    public function actionAllecforhubs() {

        $branchAdapter = new BranchAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        $user_session = Calypso::getInstance()->session("user_session");
        $allEcsInHubs = $branchAdapter->listECForHub($user_session['branch_id']);
        if ($allEcsInHubs['status'] === ResponseHandler::STATUS_OK) {
            return $this->sendSuccessResponse($allEcsInHubs['data']);
        } else {
            return $this->sendErrorResponse($allEcsInHubs['message'], null);
        }
    }
    public function actionChangepassword()
    {
        $post = (Yii::$app->request->post());

        if(isset($post['task']) && $post['task']=='change'){
            $new_password = $post['new_password'];
            $old_password = $post['old_password'];
            $password = $post['password'];

            if(in_array(null, [$new_password, $old_password, $password])){
                $this->flashError('All fields are required');
            }
            elseif($new_password == $old_password){
                $this->flashError('Change the password');
            }
            elseif($new_password !== $password){
                $this->flashError('Password mismatch');
            }
            else{
                $adm = new UserAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
                $resp = $adm->revalidate(null,$old_password);
                $resp = new ResponseHandler($resp);
                if ($resp->getStatus() == ResponseHandler::STATUS_OK) {
                    $user = new UserAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
                    $resp = $user->changePassword(['password'=>$password]);

                    $user = new UserAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
                    $resp = $user->changeStatus(['status'=>ServiceConstant::ACTIVE]);

                    $creationResponse = new ResponseHandler($resp);
                    if ($creationResponse->getStatus() == ResponseHandler::STATUS_OK) {
                        $this->flashSuccess('Password successfully changed.');
                        $this->redirect('logout');
                    } else {
                       $this->flashError('Password not changed.');
                    }
                    $this->redirect('login');
                }
                else{
                    $this->flashError('Invalid credential.');
                }
            }
        }
        $this->layout = 'login';
        return $this->render('changepassword');
    }
}

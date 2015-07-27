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
    private $page_width = 5;
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
        if(!in_array($action->id,array('logout','login','gerraout','site'))){
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
                    $flash_msg =  ('There was a problem creating the value. Please try again.');
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

    public function actionParcels($offset=0,$search=false,$page_width=null)
    {
        $from_date = date('Y/m/d');
        $to_date = date('Y/m/d');
        $search_action = $search;
        if($page_width != null){
            $this->page_width = $page_width;
            Calypso::getInstance()->cookie('page_width',$page_width);
        }
        $parcel = new ParcelAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        if(isset(Calypso::getInstance()->get()->from,Calypso::getInstance()->get()->to)){
            $from_date = Calypso::getInstance()->get()->from.' 00:00:00';
            $to_date = Calypso::getInstance()->get()->to.' 23:59:59';
            $filter = isset(Calypso::getInstance()->get()->date_filter) ? Calypso::getInstance()->get()->date_filter : '-1';
            $response = $parcel->getFilterParcelsByDateAndStatus($from_date,$to_date,$filter,$offset,$this->page_width);
            $search_action = true;
        }
        elseif(isset(Calypso::getInstance()->get()->search) ){
            $search = Calypso::getInstance()->get()->search;
            $response = $parcel->getSearchParcels('-1',$search,$offset,$this->page_width);
            $search_action = true;
            $filter = null;
        }else{
            //$response = $parcel->getParcels(null,null,$offset,$this->page_width);
            $response = $parcel->getNewParcelsByDate(date('Y-m-d'),$offset,$this->page_width);
            $search_action = false;
            $filter = null;
        }
        $response = new ResponseHandler($response);
        $data = [];
        if($response->getStatus() ==  ResponseHandler::STATUS_OK){
            $data = $response->getData();
        }
        return $this->render('parcels',array('filter'=>$filter,'parcels'=>$data,'from_date'=>$from_date,'to_date'=>$to_date,'offset'=>$offset,'page_width'=>$this->page_width,'search'=>$search_action));
    }

    public function actionProcessedparcels($offset=0,$search=false,$page_width=null)
    {
        $from_date = date('Y/m/d');
        $to_date = date('Y/m/d');
        $search_action = $search;
        if($page_width != null){
            $this->page_width = $page_width;
            Calypso::getInstance()->cookie('page_width',$page_width);
        }
        $parcel = new ParcelAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        if(isset(Calypso::getInstance()->get()->from,Calypso::getInstance()->get()->to)){
            $from_date = Calypso::getInstance()->get()->from.' 00:00:00';
            $to_date = Calypso::getInstance()->get()->to.' 23:59:59';
            $filter = isset(Calypso::getInstance()->get()->date_filter) ? Calypso::getInstance()->get()->date_filter : '-1';
            $response = $parcel->getFilterParcelsByDateAndStatus($from_date,$to_date,$filter,$offset,$this->page_width);
            $search_action = true;
        }
        elseif(isset(Calypso::getInstance()->get()->search) ){
            $search = Calypso::getInstance()->get()->search;
            $response = $parcel->getSearchParcels('-1',$search,$offset,$this->page_width);
            $search_action = true;
            $filter = null;
        }else{
            $response = $parcel->getNewParcelsByDate(date('Y-m-d'),$offset,$this->page_width);
            $search_action = false;
            $filter = null;
        }
        $response = new ResponseHandler($response);
        $data = [];
        if($response->getStatus() ==  ResponseHandler::STATUS_OK){
            $data = $response->getData();
        }
        return $this->render('processed_parcels',array('filter'=>$filter,'parcels'=>$data,'from_date'=>$from_date,'to_date'=>$to_date,'offset'=>$offset,'page_width'=>$this->page_width,'search'=>$search_action));
    }

     public function actionParcelsfordelivery($offset=0,$search=false)
    {
        $from_date =  date('Y/m/d');
        $to_date = date('Y/m/d');
        $search_action = $search;
        $parcel = new ParcelAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        if(isset(Calypso::getInstance()->get()->from,Calypso::getInstance()->get()->to)){
            $from_date = Calypso::getInstance()->get()->from.' 00:00:00';
            $to_date = Calypso::getInstance()->get()->to.' 23:59:59';
            $filter = isset(Calypso::getInstance()->get()->date_filter) ? Calypso::getInstance()->get()->date_filter : '-1';
            $response = $parcel->getFilterParcelsByDateAndStatus($from_date,$to_date,$filter,$offset,$this->page_width);
            $search_action = true;
        }
        elseif(isset(Calypso::getInstance()->get()->search) ){
            $search = Calypso::getInstance()->get()->search;
            $response = $parcel->getSearchParcels('-1',$search,$offset,$this->page_width);
            $search_action = true;
        }else{
            $response = $parcel->getParcels(ServiceConstant::FOR_DELIVERY,null,$offset,$this->page_width);
            $search_action = false;
        }
        $response = new ResponseHandler($response);
        $data = [];
        if($response->getStatus() ==  ResponseHandler::STATUS_OK){
            $data = $response->getData();
        }
        return $this->render('parcels_for_delivery',array('parcels'=>$data,'from_date'=>$from_date,'to_date'=>$to_date,'offset'=>$offset,'page_width'=>$this->page_width,'search'=>$search_action));
    }

    public function actionParcelsforsweep($offset=0,$search=false)
    {
        $from_date = date('Y/m/d');
        $to_date = date('Y/m/d');
        $search_action = $search;
        $parcel = new ParcelAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        if(isset(Calypso::getInstance()->get()->from,Calypso::getInstance()->get()->to)){
            $from_date = Calypso::getInstance()->get()->from.' 00:00:00';
            $to_date = Calypso::getInstance()->get()->to.' 23:59:59';
            $filter = isset(Calypso::getInstance()->get()->date_filter) ? Calypso::getInstance()->get()->date_filter : '-1';
            $response = $parcel->getFilterParcelsByDateAndStatus($from_date,$to_date,$filter,$offset,$this->page_width);
            $search_action = true;
        }
        elseif(isset(Calypso::getInstance()->get()->search) ){
            $search = Calypso::getInstance()->get()->search;
            $response = $parcel->getSearchParcels('-1',$search,$offset,$this->page_width);
            $search_action = true;
        }else{
            $response = $parcel->getParcels(ServiceConstant::FOR_SWEEPER,null,$offset,$this->page_width);
            $search_action = false;
        }
        $response = new ResponseHandler($response);
        $data = [];
        if($response->getStatus() ==  ResponseHandler::STATUS_OK){
            $data = $response->getData();
        }
        return $this->render('parcels_for_sweep',array('parcels'=>$data,'from_date'=>$from_date,'to_date'=>$to_date,'offset'=>$offset,'page_width'=>$this->page_width,'search'=>$search_action));
    }
    public function actionViewwaybill()
    {
        $data = [];
        $id = "-1";
        if(isset(Calypso::getInstance()->get()->id)){
            $id = Calypso::getInstance()->get()->id;
            $parcel = new ParcelAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
            $response = $parcel->getOneParcel($id);
            $response = new ResponseHandler($response);
            if($response->getStatus() == ResponseHandler::STATUS_OK){
                $data = $response->getData();
            }
        }


        return $this->render('view_waybill',array('parcelData'=>$data,'id'=> $id));
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

    public function actionManagebranches()
    {
        if(Yii::$app->request->isPost){
            $entry = Yii::$app->request->post();
            $error = [];

            $hub_data = [];
            $hub_data['name'] = Calypso::getValue($entry, 'name', null);
            $hub_data['address'] = Calypso::getValue($entry, 'address');
            $hub_data['branch_type'] = ServiceConstant::BRANCH_TYPE_HUB;
            $hub_data['state_id'] = Calypso::getValue($entry, 'state_id');
            $hub_data['status'] =  Calypso::getValue($entry, 'status');
            $hub_data['branch_id'] = Calypso::getValue($entry, 'id', null);

            $task =  Calypso::getValue(Yii::$app->request->post(), 'task');

            if(($task == 'create' || $task == 'edit') && (empty($hub_data['name']) || empty($hub_data['address']))) {
                $error[] = "All details are required!";
            }
            if(!empty($error)) {
                $errorMessages = implode('<br />', $error);
                Yii::$app->session->setFlash('danger', $errorMessages);
            }
            else {
                $hub = new BranchAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
                if($task == 'create'){
                    $response = $hub->createNewHub($hub_data);
                    if ($response['status'] === Response::STATUS_OK) {
                        Yii::$app->session->setFlash('success', 'Hub has been created successfully.');
                    } else {
                        Yii::$app->session->setFlash('danger', 'There was a problem creating the hub. Please try again.');
                    }
                }
                else{
                    $response = $hub->editOneHub($hub_data, $task);
                    if ($response['status'] === Response::STATUS_OK) {
                        Yii::$app->session->setFlash('success', 'Hub has been edited successfully.');
                    } else {
                        Yii::$app->session->setFlash('danger', 'There was a problem editing the hub. Please try again.'.$response['message']);
                    }
                }
            }
        }

        $refAdp = new RefAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        $states = $refAdp->getStates(1); // Hardcoded Nigeria for now
        $states = new ResponseHandler($states);

        $filter_state_id = Calypso::getValue(Yii::$app->request->post(), 'filter_state_id',null);
        $hubAdp = new BranchAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        $hubs = $hubAdp->getHubs($filter_state_id);
        $hubs = new ResponseHandler($hubs);

        $state_list = $states->getStatus()==ResponseHandler::STATUS_OK?$states->getData(): [];
        $hub_list = $hubs->getStatus()==ResponseHandler::STATUS_OK?$hubs->getData(): [];
        return $this->render('managehubs',array('States'=>$state_list, 'filter_state_id'=>$filter_state_id, 'hubs'=>$hub_list));
    }
    public function actionManageecs()
    {
        if(Yii::$app->request->isPost){
            $entry = Yii::$app->request->post();
            $task =  Calypso::getValue(Yii::$app->request->post(), 'task','');
            $error = [];

            $data = [];
            $data['name'] = Calypso::getValue($entry, 'name', null);
            $data['address'] = Calypso::getValue($entry, 'address');
            $data['branch_type'] = ServiceConstant::BRANCH_TYPE_EC;
            $data['status'] =  Calypso::getValue($entry, 'status');
            $data['hub_id'] = Calypso::getValue($entry, 'hub_id', null);
            $data['branch_id'] = Calypso::getValue($entry, 'id', null);
            $data['ec_id'] = Calypso::getValue($entry, 'id', null);
            $data['state_id'] = Calypso::getValue($entry, 'state_id', null);

            if(($task == 'create' || $task == 'edit') && (empty($data['name']) || empty($data['address']))) {
                $error[] = "All details are required!";
            }
            if(!empty($error)) {
                $errorMessages = implode('<br />', $error);
                Yii::$app->session->setFlash('danger', $errorMessages);
            }
            else {
                $center = new BranchAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
                if($task == 'create'){
                    $response = $center->createNewCentre($data);
                    if ($response['status'] === Response::STATUS_OK) {
                        Yii::$app->session->setFlash('success', 'Centre has been created successfully.');
                    } else {
                        Yii::$app->session->setFlash('danger', 'There was a problem creating the centre. Please try again.');
                    }
                }
                else{
                    $response = $center->editOneCentre($data, $task);
                    if ($response['status'] === Response::STATUS_OK) {
                        Yii::$app->session->setFlash('success', 'Centre has been edited successfully.');
                    } else {
                        Yii::$app->session->setFlash('danger', 'There was a problem editing the hub. Please try again.');
                    }
                }
            }
        }

        $refAdp = new RefAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        $states = $refAdp->getStates(1); // Hardcoded Nigeria for now
        $states = new ResponseHandler($states);

        $hubAdp = new BranchAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        $hubs = $hubAdp->getHubs();
        $hubs = new ResponseHandler($hubs);

        $filter_hub_id = Calypso::getValue(Yii::$app->request->post(), 'filter_hub_id', null);

        $hubAdp = new BranchAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        $centres = $hubAdp->getCentres($filter_hub_id);
        $centres = new ResponseHandler($centres);

        $state_list = $states->getStatus()==ResponseHandler::STATUS_OK?$states->getData(): [];
        $hub_list = $hubs->getStatus()==ResponseHandler::STATUS_OK?$hubs->getData(): [];
        $centres_list = $centres->getStatus()==ResponseHandler::STATUS_OK?$centres->getData(): [];
        return $this->render('manageecs',array('States'=>$state_list, 'hubs'=>$hub_list, 'centres'=>$centres_list, 'filter_hub_id'=>$filter_hub_id));
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

    public function actionManagestaff($offset=0,$role='-1')
    {

        if(Yii::$app->request->isPost){
            $data = Yii::$app->request->post();
            $user = new UserAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
            $resp = $user->createNewUser(Calypso::getInstance()->getValue($data,'role'),
                Calypso::getInstance()->getValue($data,'branch'),Calypso::getInstance()->getValue($data,'staff_id'),
                Calypso::getInstance()->getValue($data,'email'),Calypso::getInstance()->getValue($data,'firstname').' '.Calypso::getInstance()->getValue($data,'lastname'),
                Calypso::getInstance()->getValue($data,'phone'));

            $creationResponse = new ResponseHandler($resp);
            if ($creationResponse->getStatus() == ResponseHandler::STATUS_OK) {
                Yii::$app->session->setFlash('success', 'User has been created successfully.');
                //Yii::$app->response->redirect('managestaff');
            } else {
                Yii::$app->session->setFlash('danger', 'There was a problem creating this User. Please try again.');
                //Yii::$app->response->redirect('managestaff');
            }

        }


        $refAdp = new RefAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        $states = $refAdp->getStates(1);//Nigeria hardcoded for now ... No offense please.
        $states = new ResponseHandler($states);
        $rolesAdp = new RefAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        $roles = $rolesAdp->getRoles();
        $roles = new ResponseHandler($roles);
        $state_list = $states->getStatus()==ResponseHandler::STATUS_OK?$states->getData(): [];
        $role_list =  $roles->getStatus()==ResponseHandler::STATUS_OK?$roles->getData(): [];
        return $this->render('managestaff',['states' => $state_list,'roles'=> $role_list]);
    }
    public function actionHubarrival()
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

        return $this->render('hub_arrival',$viewData);
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
    //@TODO: Implement the display of the content
    public function actionHubdispatch()
    {
        $data = Yii::$app->request->post();
        $sweeper_id = Calypso::getValue($data, 'sweeper_id', null);
        $from_branch_id = Calypso::getValue($data, 'from_branch_id', null);
        $user_session = Calypso::getInstance()->session("user_session");

        $hubAdp = new BranchAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        $hubs = $hubAdp->getHubs();
        $hubs = new ResponseHandler($hubs);
        $hub_list = $hubs->getStatus()==ResponseHandler::STATUS_OK?$hubs->getData(): [];

        $parcelsAdapter = new ParcelAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        $dispatch_parcels = $parcelsAdapter->getDispatchedParcels($user_session['branch_id']);
        $parcels = new ResponseHandler($dispatch_parcels);
        $parcel_list = $parcels->getStatus()==ResponseHandler::STATUS_OK?$parcels->getData(): [];

        return $this->render('hub_dispatch', array('sweeper'=>[], 'hubs'=>$hub_list,'parcels'=>$parcel_list, 'filter_hub_id'=>$from_branch_id));
    }
    public function actionBillingmatrix()
    {
        return $this->render('billing_matrix');
    }
    public function actionBillingonforwarding()
    {
        return $this->render('billing_onforwarding');
    }
    public function actionCustomerhistory()
    {
        return $this->render('customer_history');
    }
    public function actionCustomerhistorydetails()
    {
        return $this->render('customer_history_details');
    }
}

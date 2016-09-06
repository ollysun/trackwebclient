<?php

namespace app\controllers;

use Adapter\BankAdapter;
use Adapter\BranchAdapter;
use Adapter\ParcelAdapter;
use Adapter\RegionAdapter;
use Adapter\RefAdapter;
use Adapter\UserAdapter;
use app\models\User;
use app\services\ParcelService;
use Yii;
use Adapter\RequestHelper;
use Adapter\ResponseHandler;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use Adapter\AdminAdapter;
use Adapter\Util\Calypso;
use Adapter\Globals\ServiceConstant;
use Adapter\Util\Response;
use Adapter\Util\Util;

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

    public function beforeAction($action)
    {
        if (!in_array($action->id, array('logout', 'changepassword', 'login', 'gerraout', 'site', 'track', 'tracksearchdetails', 'forgotpassword', 'resetpassword', 'passwordresetsuccess'))) {
            $s = Calypso::getInstance()->session('user_session');
            if (!$s) {
                // Calypso::getInstance()->AppRedirect('site','login');
                return $this->redirect(['site/logout']);
            }
        }
        $this->enableCsrfValidation = false;
        if (Calypso::getInstance()->cookie('page_width')) {
            $this->page_width = Calypso::getInstance()->cookie('page_width');
        }
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $user_data = Calypso::getInstance()->session('user_session');

        if (in_array(Calypso::getValue($user_data, 'role_id'), [ServiceConstant::USER_TYPE_COMPANY_ADMIN, ServiceConstant::USER_TYPE_COMPANY_OFFICER])) {
            return $this->redirect('/shipments/processed');
        }

        $branch_type = $user_data['branch']['branch_type'];
        $alternative = $branch_type == ServiceConstant::BRANCH_TYPE_HQ ? null : $user_data['branch_id'];
        $branch_to_view = Calypso::getValue(Calypso::getInstance()->get(), 'branch', $alternative);
        $user_type = $user_data['role_id'];

        $from_date = Util::getToday();
        $to_date = Util::getToday();
        $date = '0d';

        if (isset(Calypso::getInstance()->get()->from, Calypso::getInstance()->get()->to, Calypso::getInstance()->get()->date)) {
            $from_date = Calypso::getInstance()->get()->from;
            $to_date = Calypso::getInstance()->get()->to;
            $date = Calypso::getInstance()->get()->date;
        }

        $parcel = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $filters = array('created_branch_id' => $branch_to_view, 'start_created_date' => $from_date . ' 00:00:00', 'end_created_date' => $to_date . ' 23:59:59');
        $stats['created'] = $parcel->getParcelCount($filters);

        $filters = array('request_type' => ServiceConstant::REQUEST_OTHERS, 'history_from_branch_id' => $branch_to_view, 'history_status' => ServiceConstant::FOR_SWEEPER, 'history_start_created_date' => $from_date . ' 00:00:00', 'history_end_created_date' => $to_date . ' 23:59:59');
        $stats['for_sweep'] = $parcel->getParcelCount($filters);

        $filters = array('request_type' => ServiceConstant::REQUEST_ECOMMERCE, 'history_from_branch_id' => $branch_to_view, 'history_status' => ServiceConstant::FOR_SWEEPER, 'history_start_created_date' => $from_date . ' 00:00:00', 'history_end_created_date' => $to_date . ' 23:59:59');
        $stats['for_sweep_ecommerce'] = $parcel->getParcelCount($filters);

        $filters = array('from_branch_id' => $branch_to_view, 'status' => ServiceConstant::FOR_DELIVERY);
        $stats['for_delivery'] = $parcel->getParcelCount($filters);

        if ($branch_type != ServiceConstant::BRANCH_TYPE_EC) {
            $filters = array('history_to_branch_id' => $branch_to_view, 'history_status' => ServiceConstant::FOR_ARRIVAL, 'history_start_created_date' => $from_date . ' 00:00:00', 'history_end_created_date' => $to_date . ' 23:59:59');
            $stats['received'] = $parcel->getParcelCount($filters);

            $filters = array('to_branch_id' => $branch_to_view, 'status' => ServiceConstant::FOR_ARRIVAL);
            $stats['ready_for_sorting'] = $parcel->getParcelCount($filters);

            $filters = array('from_branch_id' => $branch_to_view, 'status' => ServiceConstant::ASSIGNED_TO_GROUNDSMAN);
            $stats['groundsman'] = $parcel->getParcelCount($filters);

            $filters = array('from_branch_id' => $branch_to_view, 'status' => ServiceConstant::FOR_SWEEPER);
            $stats['sorted'] = $parcel->getParcelCount($filters);

            $filters = array('from_branch_id' => $branch_to_view, 'status' => ServiceConstant::BEING_DELIVERED);
            $stats['transit_to_customer'] = $parcel->getParcelCount($filters);

            $filters = array('from_branch_id' => $branch_to_view, 'status' => ServiceConstant::FOR_SWEEPER, 'history_start_created_date' => $from_date . ' 00:00:00', 'history_end_created_date' => $to_date . ' 23:59:59');
            $stats['sorted_still_at_hub'] = $parcel->getParcelCount($filters);

            $filters = array('history_from_branch_id' => $branch_to_view, 'history_status' => ServiceConstant::DELIVERED, 'history_start_created_date' => $from_date . ' 00:00:00', 'history_end_created_date' => $to_date . ' 23:59:59');
            $stats['delivered'] = $parcel->getParcelCount($filters);
        }

        return $this->render('index', array('date' => $date, 'from_date' => $from_date, 'to_date' => $to_date, 'stats' => $stats, 'branch_type' => $branch_type, 'user_type' => $user_type, 'branch' => $branch_to_view));
    }

    public function actionGerraout()
    {
        Calypso::getInstance()->session('user_session', null);

        Yii::$app->user->logout();
        session_destroy();
        return $this->redirect('logout');
    }

    public function actionAccessdenied()
    {
        return $this->render('accessdenied');
    }

    public function actionLogin()
    {
        $this->enableCsrfValidation = false;
        $this->layout = 'login';
        if (!Yii::$app->request->isPost) {
            return $this->render('login');
        }

        $admin = new AdminAdapter();
        $response = $admin->login(Yii::$app->request->post('email', null), Yii::$app->request->post('password', null));
        $response = new ResponseHandler($response);

        if ($response->getStatus() != ResponseHandler::STATUS_OK) {
            Calypso::getInstance()->setPageData($response->getError());
            return $this->render('login');
        }

        $data = $response->getData();
        $user_status = Calypso::getValue($data, 'status');

        if ($user_status == ServiceConstant::ACTIVE) {
            User::login($data);

            // Check Corporate User
            if (!is_null(Calypso::getValue(Calypso::getInstance()->session("user_session"), 'company'))) {
                return $this->redirect('/shipments/processed');
            }
            return $this->redirect('/site');

        } else if ($user_status == ServiceConstant::INACTIVE && Calypso::getValue($data, 'last_login_time') == 0) {
            User::login($data);
            return $this->redirect('/site/changepassword');
        } else if ($user_status == ServiceConstant::INACTIVE) {
            Calypso::getInstance()->setPageData("You are not eligible to access this system, kindly contact your administrator");
            return $this->render('login');
        } else {
            Calypso::getInstance()->setPageData("An error occurred during login. Please try again later.");
            return $this->render('login');
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        Calypso::getInstance()->session('user_session', null);
        Yii::$app->user->logout();
        session_destroy();
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

        if (Yii::$app->request->isPost) {
            $error = 1;
            $data = Yii::$app->request->post();

            $parcelService = new ParcelService();
            $payload = $parcelService->buildPostData($data);
            $flash_msg = '';
            if (isset($payload['status'])) {
                $errorMessages = implode('<br />', $payload['messages']);
                //Yii::$app->session->setFlash('danger', $errorMessages);
                $flash_msg = $errorMessages;
            } else {

                $parcel = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
                $response = $parcel->createNewParcel(json_encode($payload));
                if ($response['status'] === Response::STATUS_OK) {
                    $flash_msg = "viewwaybill?waybill_number=" . $response['data']['waybill_number'];
                    $error = 0;
                } else {
                    $flash_msg = ('There was a problem creating the value. Please try again. #Reason:' . $response['message']);
                }
            }
            echo "<script>window.top.getServerResponse('" . $error . "','" . $flash_msg . "');</script>";
        }


        $refData = new RefAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());

        $banks = $refData->getBanks();
        $shipmentType = $refData->getShipmentType();
        $deliveryType = $refData->getdeliveryType();
        $parcelType = $refData->getparcelType();
        $paymentMethod = $refData->getPaymentMethods();
        $countries = $refData->getCountries();

        return $this->render('new_parcel', array(
            'Banks' => $banks,
            'ShipmentType' => $shipmentType,
            'deliveryType' => $deliveryType,
            'parcelType' => $parcelType,
            'countries' => $countries,
            'paymentMethod' => $paymentMethod
        ));
    }

    public function actionViewwaybill()
    {
        $data = [];
        $id = "-1";
        if (isset(Calypso::getInstance()->get()->id)) {
            $id = Calypso::getInstance()->get()->id;
        }
        return $this->redirect("/shipments/view?id={$id}");
    }

    /**
     * It requires atleast a state_id or branch_id, or both
     * @return array
     */
    public function actionGetbranches()
    {
        $state_id = \Yii::$app->request->get('id');
        $branch_id = \Yii::$app->request->get('branch_id');
        if (!isset($state_id)) {
            return $this->sendErrorResponse("Invalid parameter(s) sent!", null);
        }
        $refData = new RefAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $branches = $refData->getBranch($state_id, $branch_id);
        if ($branches['status'] === ResponseHandler::STATUS_OK) {
            return $this->sendSuccessResponse($branches['data']);
        } else {
            return $this->sendErrorResponse($branches['message'], null);
        }
    }

    public function actionValidatestaff()
    {
        $staff_id = \Yii::$app->request->get('staff_id');
        if (!isset($staff_id)) {
            return $this->sendErrorResponse("Invalid parameter(s) sent!", null);
        }
        $adminAdp = new AdminAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $response = $adminAdp->getStaffByStaffID($staff_id);
        $response = new ResponseHandler($response);
        if ($response->getStatus() == ResponseHandler::STATUS_OK) {
            return $this->sendSuccessResponse($response->getData());
        } else {
            return $this->sendErrorResponse($response->getError(), null);
        }
    }

    public function actionCheckinparcel()
    {
        if (isset(Calypso::getInstance()->post()->held_by_id, Calypso::getInstance()->post()->waybill_numbers)) {
            $parcel = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
            $response = $parcel->moveToArrival([
                'held_by_id' => Calypso::getInstance()->post()->held_by_id,
                'waybill_numbers' => (Calypso::getInstance()->post()->waybill_numbers),
                'force_receive' => Calypso::getInstance()->post()->force_receive,
                'previous_branch' => (Calypso::getInstance()->post()->previous_branch)
            ]);
            $response = new ResponseHandler($response);
            if ($response->getStatus() == ResponseHandler::STATUS_OK) {
                return $this->sendSuccessResponse($response->getData());
            } else {
                return $this->sendErrorResponse($response->getError(), null);
            }
        } else {
            return $this->sendErrorResponse("Invalid data", null);
        }
    }

    public function actionMovetofordelivery()
    {
        if (isset(Calypso::getInstance()->post()->waybill_numbers)) {
            $parcel = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
            $response = $parcel->moveForDelivery([
                'held_by_id' => Calypso::getInstance()->post()->held_by_id,
                'waybill_numbers' => (Calypso::getInstance()->post()->waybill_numbers)
            ]);
            $response = new ResponseHandler($response);
            if ($response->getStatus() == ResponseHandler::STATUS_OK) {
                return $this->sendSuccessResponse($response->getData());
            } else {
                return $this->sendErrorResponse($response->getError(), null);
            }
        } else {
            return $this->sendErrorResponse("Invalid data", null);
        }
    }

    public function actionGetarrivedparcel()
    {
        $staff_no = \Yii::$app->request->get('staff_no');
        $session_data = Calypso::getInstance()->session('user_session');
        $branch_id = $session_data['branch']['id'];

        if (!isset($staff_no)) {
            return $this->sendErrorResponse("Invalid parameter(s) sent!", null);
        }
        $parcel = new  ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $response = $parcel->getParcel($staff_no, ServiceConstant::IN_TRANSIT, $branch_id, true);

        if ($response['status'] === ResponseHandler::STATUS_OK) {
            return $this->sendSuccessResponse($response['data']);
        } else {
            return $this->sendErrorResponse($response['message'], null);
        }
    }


    public function actionGetparcel(){
        $waybill_number = \Yii::$app->request->get('waybill_number');


        $parcelsAdapter = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $response = $parcelsAdapter->getSearchParcels(null, $waybill_number);
        //$response = $parcelsAdapter->getSearchParcels(null, $waybill_number, 0, 1, 1, null, 1);

        if ($response['status'] === ResponseHandler::STATUS_OK) {
            return $this->sendSuccessResponse($response['data']);
        } else {
            return $this->sendErrorResponse($response['message'], null);
        }
    }

    public function actionPrintwaybill()
    {
        $data = [];
        $sender_location = [];
        $receiver_location = [];
        $serviceType = [];
        $parcelType = [];
        if (isset(Calypso::getInstance()->get()->id)) {
            $id = Calypso::getInstance()->get()->id;
            $parcel = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
            $response = $parcel->getOneParcel($id);
            $response = new ResponseHandler($response);
            if ($response->getStatus() == ResponseHandler::STATUS_OK) {
                $data = $response->getData();
                if (isset($data['sender_address']) && isset($data['sender_address']['city_id'])) {
                    $city_id = $data['sender_address']['city_id'];
                    $regionAdp = new RegionAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
                    $sender_location = $regionAdp->getCity($city_id);
                    $resp = new ResponseHandler($sender_location);
                    if ($resp->getStatus() == ResponseHandler::STATUS_OK) {
                        $sender_location = $resp->getData();
                    }
                }
                if (isset($data['receiver_address']) && isset($data['receiver_address']['city_id'])) {
                    $city_id = $data['receiver_address']['city_id'];
                    $regionAdp = new RegionAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
                    $receiver_location = $regionAdp->getCity($city_id);
                    $resp = new ResponseHandler($receiver_location);
                    if ($resp->getStatus() == ResponseHandler::STATUS_OK) {
                        $receiver_location = $resp->getData();
                    }
                }
            }

            $refData = new RefAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
            $refResponse = new ResponseHandler($refData->getShipmentType());
            if ($refResponse->getStatus() == ResponseHandler::STATUS_OK) {
                $serviceType = $refResponse->getData();
            }
            $parcelTypeResponse = new ResponseHandler($refData->getparcelType());
            if ($parcelTypeResponse->getStatus() == ResponseHandler::STATUS_OK) {
                $parcelType = $parcelTypeResponse->getData();
            }
        }
        $this->layout = 'print';

        return $this->render('print_waybill', array(
            'parcelData' => $data,
            'sender_location' => $sender_location,
            'receiver_location' => $receiver_location,
            'serviceType' => $serviceType,
            'parcelType' => $parcelType,
        ));
    }

    /**
     * Ajax calls to get Branch details
     */
    public function actionBranchdetails()
    {
        $branch_id = \Yii::$app->request->get('id');
        if (!isset($branch_id)) {
            return $this->sendErrorResponse("Invalid parameter(s) sent!", null);
        }
        $refData = new RefAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $branch = $refData->getBranchbyId($branch_id);
        if ($branch['status'] === ResponseHandler::STATUS_OK) {
            return $this->sendSuccessResponse($branch['data']);
        } else {
            return $this->sendErrorResponse($branch['message'], null);
        }
    }

    public function actionHubnextdestination()
    {
        $parcelsAdapter = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());

        if (\Yii::$app->request->isPost) {
            $branch = \Yii::$app->request->post('branch');
            $waybill_numbers = \Yii::$app->request->post('waybills');
            if (!isset($branch) || empty($waybill_numbers)) {
                $this->flashError('Please ensure you set destinations at least a (one) for the parcels');
            }

            $postParams['waybill_numbers'] = implode(',', $waybill_numbers);
            $postParams['to_branch_id'] = $branch;
            $response = $parcelsAdapter->moveToForSweeper($postParams);
            if ($response['status'] === ResponseHandler::STATUS_OK) {
                $this->flashSuccess('Parcels have been successfully moved to the next destination. <a href="hubmovetodelivery">Generate Manifest</a>');
            } else {
                $this->flashError('An error occured while trying to move parcels to next destination. Please try again.');
            }
        }
        $user_session = Calypso::getInstance()->session("user_session");
        $parcelsAdapter = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $arrival_parcels = $parcelsAdapter->getParcelsForNextDestination(ServiceConstant::FOR_ARRIVAL, $user_session['branch_id']);
        if ($arrival_parcels['status'] === ResponseHandler::STATUS_OK) {
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
    public function actionAllhubs()
    {

        $branchAdapter = new BranchAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $allHubs = $branchAdapter->getAllHubs(false);
        if ($allHubs['status'] === ResponseHandler::STATUS_OK) {
            return $this->sendSuccessResponse($allHubs['data']);
        } else {
            return $this->sendErrorResponse($allHubs['message'], null);
        }
    }

    /**
     * Ajax calls to get all ec in the present hub
     */
    public function actionAllecforhubs()
    {

        $branchAdapter = new BranchAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
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

        if (isset($post['task']) && $post['task'] == 'change') {
            $new_password = $post['new_password'];
            $old_password = $post['old_password'];
            $password = $post['password'];

            if (in_array(null, [$new_password, $old_password, $password])) {
                $this->flashError('All fields are required');
            } elseif ($new_password == $old_password) {
                $this->flashError('Change the password');
            } elseif ($new_password !== $password) {
                $this->flashError('Password mismatch');
            } else {
                $adm = new UserAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
                $resp = $adm->revalidate(null, $old_password);
                $resp = new ResponseHandler($resp);
                if ($resp->getStatus() == ResponseHandler::STATUS_OK) {
                    $user = new UserAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
                    $resp = $user->changePassword(['password' => $password]);

                    $creationResponse = new ResponseHandler($resp);
                    if ($creationResponse->getStatus() == ResponseHandler::STATUS_OK) {
                        $this->flashSuccess('Password successfully changed.');
                        $this->redirect('/logout');
                    } else {
                        $this->flashError('Password not changed.');
                    }
                    $this->redirect('login');
                } else {
                    $this->flashError('Invalid credentials.');
                }
            }
        }
        $this->layout = 'login';
        return $this->render('changepassword');
    }

    /**
     * Forgot Password Action
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function actionForgotpassword()
    {
        $this->layout = 'login';

        if (Yii::$app->request->isPost) {
            $email = Yii::$app->request->post('email');

            if (is_null($email)) {
                $this->flashError("Please enter your email");
            } else {
                $userAdapter = new UserAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
                $status = $userAdapter->forgotPassword($email);
                if (is_bool($status)) {
                    $this->flashSuccess("Your password reset link has been sent to you");
                } else {
                    $this->flashError($status);
                }
            }
        }
        return $this->render('forgotpassword');
    }

    /**
     * Reset Password Action
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function actionResetpassword()
    {
        $this->layout = 'login';

        $token = Yii::$app->request->get('token');
        $key = Yii::$app->request->get('_key_');

        if (!isset($token, $key)) {
            return $this->redirect(Url::toRoute('site/index'));
        }

        $userAdapter = new UserAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $status = $userAdapter->validatePasswordResetToken($token, $key);

        if (!is_bool($status)) {
            $this->flashError($status);
        }

        if (Yii::$app->request->isPost) {
            $password = Yii::$app->request->post('password');
            $confirmPassword = Yii::$app->request->post('c_password');

            if (in_array(null, [$password, $confirmPassword])) {
                $this->flashError("Please enter your new password");
            } else if ($password != $confirmPassword) {
                $this->flashError("Passwords don't match");
            } else {
                $userAdapter = new UserAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
                $resetStatus = $userAdapter->resetPassword($key, $password);
                if (is_bool($resetStatus)) {
                    Yii::$app->session->setFlash('password_reset_success', true);
                    return $this->redirect(Url::toRoute("site/passwordresetsuccess"));
                } else {
                    $this->flashError($resetStatus);
                }
            }
        }
        return $this->render('resetpassword', ['showForm' => $status]);
    }

    public function actionTrack()
    {
        $this->layout = 'tracking';
        return $this->render('track');
    }

    public function actionTracksearch()
    {
        return $this->render('track_search');
    }

    /**
     * Password Reset Success Action
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return string
     */
    public function actionPasswordresetsuccess()
    {
        $this->layout = "login";

        if (!Yii::$app->session->hasFlash('password_reset_success')) {
            return $this->redirect(Url::toRoute("site"));
        }

        Yii::$app->session->removeFlash('password_reset_success');
        return $this->render('resetpassword_success');
    }

    public function actionTracksearchdetails()
    {
        $s = Calypso::getInstance()->session('user_session');
        if (!$s) {
            $this->layout = 'tracking';
        }
        return $this->render('track_search_details');
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: RotelandO
 * Date: 7/25/15
 * Time: 8:30 AM
 */

namespace app\controllers;


use Adapter\BankAdapter;
use Adapter\ParcelAdapter;
use Adapter\RefAdapter;
use Adapter\RequestHelper;
use Adapter\ResponseHandler;
use Adapter\UserAdapter;
use Adapter\Util\Response;
use app\services\ParcelService;
use Yii;

class ParcelsController extends BaseController {

    public function beforeAction($action){
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }
    public function actionNew()
    {

        if(Yii::$app->request->isPost){
            $data = Yii::$app->request->post();

            $parcelService = new ParcelService();
            $payload = $parcelService->buildPostData($data);
            if(isset($payload['status'])) {
                $errorMessages = implode('<br />', $payload['messages']);
                Yii::$app->session->setFlash('danger', $errorMessages);

            } else {

                $parcel = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
                $response = $parcel->createNewParcel(json_encode($payload));
                if ($response['status'] === Response::STATUS_OK) {
                    Yii::$app->response->redirect("/site/viewwaybill?id={$response['data']['id']}");
                } else {
                    $this->flashError('There was a problem creating the value. Please try again.');
                }
            }
        }

        $refData = new RefAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());

        $banks = $refData->getBanks();
        $shipmentType = $refData->getShipmentType();
        $deliveryType = $refData->getdeliveryType();
        $parcelType = $refData->getparcelType();
        $paymentMethod = $refData->getPaymentMethods();
        $countries = $refData->getCountries();

        return $this->render('new',array(
            'Banks'=>$banks,
            'ShipmentType' => $shipmentType,
            'deliveryType'=>$deliveryType,
            'parcelType'=>$parcelType,
            'countries'=>$countries,
            'paymentMethod'=>$paymentMethod
        ));
    }

    /**
     * Ajax calls to get states when a country is selected
     */
    public function actionGetstates() {

        $country_id = \Yii::$app->request->get('id');
        if(!isset($country_id)) {
            return $this->sendErrorResponse("Invalid parameter(s) sent!", null);
        }

        $refData = new RefAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
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

    /**
     * Ajax calls to get Account details of sender
     */
    public function actionAccountdetails() {

        $owner_id = \Yii::$app->request->get('owner_id');
        if(!isset($owner_id)) {
            return $this->sendErrorResponse("Invalid parameter(s) sent!", null);
        }

        $bankAdapter = new BankAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        $bankInfo = $bankAdapter->getSenderBankAccout($owner_id);
        if ($bankInfo['status'] === ResponseHandler::STATUS_OK) {
            $resp = [];
            if(!empty($bankInfo['data'])) {
                $resp = $bankInfo['data'][0];
            }

            return $this->sendSuccessResponse($resp);
        } else {
            return $this->sendErrorResponse($bankInfo['message'], null);
        }
    }
}
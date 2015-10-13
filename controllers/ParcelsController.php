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
use Adapter\RegionAdapter;
use Adapter\RequestHelper;
use Adapter\ResponseHandler;
use Adapter\UserAdapter;
use Adapter\Util\Response;
use Adapter\BranchAdapter;
use app\services\ParcelService;
use Adapter\Util\Calypso;
use Yii;

class ParcelsController extends BaseController
{

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionNew()
    {

        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();

            $parcelService = new ParcelService();
            $payload = $parcelService->buildPostData($data);
            if (isset($payload['status'])) {
                $this->sendAsyncFormResponse(1, array('message' => implode('<br />', $payload['messages'])), "Parcel.onFormErrorCallback");

            } else {

                $parcel = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
                $response = $parcel->createNewParcel(json_encode($payload));
                if ($response['status'] === Response::STATUS_OK) {
                    $this->sendAsyncFormResponse(1, $response['data'], "Parcel.onFormSuccessCallback");
                } else {
                    $this->sendAsyncFormResponse(1, $response, "Parcel.onFormErrorCallback");
                }
            }
        }

        $parcel = [];
        $id = Yii::$app->request->get('id');
        if (isset($id)) {
            $parcel = ParcelService::getParcelDetails($id);
        }

        $refData = new RefAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());

        $banks = $refData->getBanks();
        $shipmentType = $refData->getShipmentType();
        $deliveryType = $refData->getdeliveryType();
        $parcelType = $refData->getparcelType();
        $paymentMethod = $refData->getPaymentMethods();
        $countries = $refData->getCountries();
        $states = (new ResponseHandler($refData->getStates(1)))->getData();

        $hubAdp = new BranchAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $centres = $hubAdp->getAllHubs(false);
        $centres = new ResponseHandler($centres);
        $hubs_list = $centres->getStatus() == ResponseHandler::STATUS_OK ? $centres->getData() : [];

        $user = Calypso::getInstance()->session('user_session');
        $hubAdp = new BranchAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $centres = $hubAdp->getCentres($user['branch']['id']);
        $centres = new ResponseHandler($centres);
        $centres_list = $centres->getStatus() == ResponseHandler::STATUS_OK ? $centres->getData() : [];

        $centres_list = array_merge($centres_list, $hubs_list);

        return $this->render('new', array(
            'Banks' => $banks,
            'ShipmentType' => $shipmentType,
            'deliveryType' => $deliveryType,
            'parcelType' => $parcelType,
            'countries' => $countries,
            'states' => $states,
            'paymentMethod' => $paymentMethod,
            'centres' => $centres_list,
            'branch' => $user['branch'],
            'parcel' => $parcel
        ));
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
        $response = $parcelAdp->calcBilling($data['payload']);

        if ($response['status'] === ResponseHandler::STATUS_OK) {
            return $this->sendSuccessResponse($response['data']);
        } else {
            $error_message = $response['message'] . '. Please ensure the source and destination branch are properly mapped in the Zone matrix';
            return $this->sendErrorResponse($error_message, null);
        }

    }
}
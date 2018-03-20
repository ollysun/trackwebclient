<?php
/**
 * Created by PhpStorm.
 * User: ELACHI
 * Date: 1/13/2017
 * Time: 9:03 AM
 */

namespace app\modules\api\controllers;


use Adapter\WmsAdapter;

class StocksController extends ApiBaseController
{
    public function actionGetall(){
        $adapter = new WmsAdapter();
        $adapter->setRegNo(Yii::$app->request->get('registration_number'));
        $adapter->setPrivateKey(Yii::$app->request->get('private_key'));
        return $this->sendSuccessResponse($adapter->getStocks($this->getPrivateKey()));
    }

    public function actionGetstockbysku(){
        $adapter = new WmsAdapter();
        $adapter->setRegNo(Yii::$app->request->get('registration_number'));
        $adapter->setPrivateKey(Yii::$app->request->get('private_key'));
        return $this->sendSuccessResponse($adapter->getStockBySku($this->getPrivateKey(), $this->get('sku')));
    }

    public function actionGetbylocation(){
        $adapter = new WmsAdapter();
        $adapter->setRegNo(Yii::$app->request->get('registration_number'));
        $adapter->setPrivateKey(Yii::$app->request->get('private_key'));
        $location = $this->get('location');
        if($location == null) return $this->sendErrorResponse('Location is required', self::BadRequest);
        return $this->sendSuccessResponse($adapter->getStocksByLocation($this->getPrivateKey(), $location));
    }

    public function actionGetstockbyskuandloc(){
        $location = $this->get('location');
        $sku = $this->get('sku');
        if(in_array(null, [$location, $sku])){
            return $this->sendErrorResponse('Location and sku are required', self::BadRequest);
        }
        $adapter = new WmsAdapter();
        $adapter->setRegNo(Yii::$app->request->get('registration_number'));
        $adapter->setPrivateKey(Yii::$app->request->get('private_key'));
        return $this->sendSuccessResponse($adapter->getStocksBySkuAndLocation($this->getPrivateKey(), $sku, $location));
    }

    public function actionGetreceivedstocksbyref(){
        $ref = $this->get('reference_number');
        if(empty($ref)) return $this->sendErrorResponse('Ref is required', self::BadRequest);

        $adapter = new WmsAdapter();
        $adapter->setRegNo(Yii::$app->request->get('registration_number'));
        $adapter->setPrivateKey(Yii::$app->request->get('private_key'));
        return $this->sendSuccessResponse($adapter->callApi('receivebyref',
            ['key' => $this->getPrivateKey(), 'ref' => $ref]));
    }

    public function actionGetreceivedstocksbytrans(){
        $txn = $this->get('transaction_number');
        if(empty($txn)) return $this->sendErrorResponse('Transaction number is required', self::BadRequest);
        return $this->sendSuccessResponse((new WmsAdapter())->callApi('receivebytrx', ['key' => $this->getPrivateKey(), 'trx' => $txn]));
    }

    public function actionGetreturnedstocksbyref(){
        $ref = $this->get('reference_number');
        if(empty($ref)) return $this->sendErrorResponse('Reference number is required', self::BadRequest);
        return $this->sendSuccessResponse((new WmsAdapter())->callApi('returnsbyRef', ['key' => $this->getPrivateKey(), 'ref' => $ref]));
    }
}
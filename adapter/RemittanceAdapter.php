<?php
/**
 * Created by PhpStorm.
 * User: ELACHI
 * Date: 1/23/2017
 * Time: 3:46 PM
 */

namespace Adapter;


use Adapter\Globals\ServiceConstant;

class RemittanceAdapter extends BaseAdapter
{
    public function getAll($filter){
        return $this->request(ServiceConstant::URL_REMITTANCE_GET_ALL, $filter, self::HTTP_GET);
    }

    public function getOne($filter){
        return $this->request(ServiceConstant::URL_REMITTANCE_GET_ONE, $filter, self::HTTP_GET);
    }

    public function getDueParcels($filter){
        return $this->request(ServiceConstant::URL_REMITTANCE_GET_DUE_PARCELS, $filter, self::HTTP_GET);
    }

    public function save($company_ids, $current_status){
        $company_ids = implode(',', $company_ids);
        return $this->request(ServiceConstant::URL_REMITTANCE_SAVE, ['company_ids' => $company_ids,
            'current_status' => $current_status], self::HTTP_POST);
    }

    public function getPendingPayments($filter){
        return $this->request(ServiceConstant::URL_GET_PENDING_PAYMENTS, $filter, self::HTTP_GET);
    }

    public function getPaymentAdvice($filter){
        return $this->request(ServiceConstant::URL_REMITTANCE_PAYMENT_ADVICE, $filter, self::HTTP_GET);
    }

    public function getAdviceForDownload($filter){
        return $this->request(ServiceConstant::URL_REMITTANCE_GET_ADVICE_FOR_DOWNLOAD, $filter, self::HTTP_GET);
    }
}
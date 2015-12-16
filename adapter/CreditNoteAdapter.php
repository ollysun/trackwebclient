<?php

namespace Adapter;

use Adapter\Globals\ServiceConstant;
use Adapter\Util\Calypso;
use yii\helpers\Json;

/**
 * Class CreditNoteAdapter
 * @package adapter
 * @author Adegoke Obasa <goke@cottacush.com>
 */
class CreditNoteAdapter extends BaseAdapter
{

    public function __construct()
    {
        parent::__construct(RequestHelper::getClientID(), RequestHelper::getAccessToken());
    }

    /**
     * Creates a credit note
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $data
     * @return bool
     */
    public function generateCreditNote($data)
    {
        $data = Json::encode($data);
        $rawResponse = $this->request(ServiceConstant::URL_CREDIT_NOTE_ADD, $data, self::HTTP_POST);
        $response = new ResponseHandler($rawResponse);

        if (!$response->isSuccess()) {
            $this->lastErrorMessage = $response->getError();
        }

        return $response->isSuccess();
    }

    /**
     * Get all credit notes based on filters
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param array $filters
     * @return array|mixed
     */
    public function getCreditNotes($filters = [])
    {
        $filters = array_merge($filters, [
            'with_company' => '1',
            'with_invoice' => '1',
            'with_total_count' => '1',
        ]);

        $response = $this->request(ServiceConstant::URL_CREDIT_NOTE_ALL,
            array_filter($filters), self::HTTP_GET);

        $response = new ResponseHandler($response);

        if ($response->isSuccess()) {
            return $response->getData();
        }
        return [];
    }

//    /**
//     * Get all invoices based on filters
//     * @author Adegoke Obasa <goke@cottacush.com>
//     * @param array $filters
//     * @return array|mixed
//     */
//    public function getInvoiceParcels($filters = [])
//    {
//        $filters = array_merge($filters, [
//            'with_parcel' => '1',
//            'no_paginate' => '1',
//        ]);
//
//        $response = $this->request(ServiceConstant::URL_INVOICE_PARCELS,
//            array_filter($filters), self::HTTP_GET);
//
//        $response = new ResponseHandler($response);
//
//        if ($response->isSuccess()) {
//            return $response->getData();
//        }
//        return [];
//    }
}
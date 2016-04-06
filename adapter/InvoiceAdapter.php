<?php

namespace Adapter;

use Adapter\Globals\ServiceConstant;
use Adapter\Util\Calypso;
use yii\helpers\Json;

/**
 * Class InvoiceAdapter
 * @package adapter
 * @author Adegoke Obasa <goke@cottacush.com>
 */
class InvoiceAdapter extends BaseAdapter
{

    public function __construct()
    {
        parent::__construct(RequestHelper::getClientID(), RequestHelper::getAccessToken());
    }

    /**
     * Creates an invoice
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $data
     * @return bool
     */
    public function createInvoice($data)
    {
        $data = Json::encode($data);
        $rawResponse = $this->request(ServiceConstant::URL_INVOICE_ADD, $data, self::HTTP_POST);
        $response = new ResponseHandler($rawResponse);

        if (!$response->isSuccess()) {
            $this->lastErrorMessage = $response->getError();
        }

        return $response->isSuccess();
    }

    /**
     * @param $data
     * @author Akindolani Akinboyewa Richard
     * @return bool
     */
    public function createBulkInvoice($data)
    {
        $data = Json::encode($data);
        $rawResponse = $this->request(ServiceConstant::URL_BULK_INVOICE_ADD, $data, self::HTTP_POST);
        $response = new ResponseHandler($rawResponse);

        if (!$response->isSuccess()) {
            $this->lastErrorMessage = $response->getError();
        }

        return $response->isSuccess();
    }

    /**
     * Get's the details of an invoice
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $filters
     * @return array|mixed
     */
    public function getInvoice($filters)
    {
        $filters = array_merge($filters, [
            'with_company' => '1',
            'with_credit_note' => '1',
        ]);

        $response = $this->request(ServiceConstant::URL_INVOICE_GET,
            array_filter($filters), self::HTTP_GET);

        $response = new ResponseHandler($response);

        if ($response->isSuccess()) {
            return $response->getData();
        }
        return [];
    }

    /**
     * Get all invoices based on filters
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param array $filters
     * @return array|mixed
     */
    public function getInvoices($filters = [])
    {
        $filters = array_merge($filters, [
            'with_company' => '1',
            'with_credit_note' => '1',
            'with_total_count' => '1',
        ]);

        $response = $this->request(ServiceConstant::URL_INVOICE_ALL,
            array_filter($filters), self::HTTP_GET);

        $response = new ResponseHandler($response);

        if ($response->isSuccess()) {
            return $response->getData();
        }
        return [];
    }

    /**
     * Get all invoices based on filters
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param array $filters
     * @return array|mixed
     */
    public function getInvoiceParcels($filters = [])
    {
        $filters = array_merge($filters, [
            'with_parcel' => '1',
            'no_paginate' => '1',
        ]);

        $response = $this->request(ServiceConstant::URL_INVOICE_PARCELS,
            array_filter($filters), self::HTTP_GET);

        $response = new ResponseHandler($response);

        if ($response->isSuccess()) {
            return $response->getData();
        }
        return [];
    }

    /**
     * @author Olajide Oye <jide@cottacush.com>
     * @param $count
     * @param $numberPerSheet
     * @param int $extras
     * @return float
     */
    function getNumberOfSheets($count, $numberPerSheet, $extras = 0)
    {
        $count = (int)$count;
        $numberPerSheet = (int)$numberPerSheet;
        $extras = (int)$extras;

        $numberOfSheets = round((($count + $extras) / $numberPerSheet));

        return $numberOfSheets;
    }

    /**
     * @author Olajide Oye <jide@cottacush.com>
     * @param $no_of_pages
     * @return string
     */
    function getPageHeight($no_of_pages)
    {
        $pageHeight = 1000;
        $no_of_pages = (int)$no_of_pages;

        return 'height:' . ($pageHeight * $no_of_pages) . 'px';
    }

    /**
     * Get bulk invoice tasks
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return array|mixed|string
     */
    public function getBulkInvoiceTasks()
    {
        $response = $this->request(ServiceConstant::URL_INVOICE_GET_BULK_INVOICE_TASKS, [], self::HTTP_GET);
        $response = new ResponseHandler($response);

        if ($response->isSuccess()) {
            return $response->getData();
        }
        return [];
    }

    /**
     * Get bulk invoice task
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $task_id
     * @return array|mixed|string
     */
    public function getBulkInvoiceTask($task_id)
    {
        $response = $this->request(ServiceConstant::URL_INVOICE_GET_BULK_INVOICE_TASK, ['task_id' => $task_id], self::HTTP_GET);
        $response = new ResponseHandler($response);

        if ($response->isSuccess()) {
            return $response->getData();
        }
        return [];
    }

}
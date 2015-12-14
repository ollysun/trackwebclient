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
        ]);

        $response = $this->request(ServiceConstant::URL_INVOICE_PARCELS,
            array_filter($filters), self::HTTP_GET);

        $response = new ResponseHandler($response);

        if ($response->isSuccess()) {
            return $response->getData();
        }
        return [];
    }
}
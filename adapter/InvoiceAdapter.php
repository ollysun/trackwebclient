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
}
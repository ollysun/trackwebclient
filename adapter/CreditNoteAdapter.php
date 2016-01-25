<?php

namespace Adapter;

use Adapter\Globals\ServiceConstant;
use Adapter\Util\Calypso;
use yii\helpers\Json;
use yii\helpers\Url;

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

    /**
     * @author Babatunde Otaru <tunde@cottacush.com>
     * @param $credit_note_no
     * @return array
     */
    public function getCreditNoteParcels($credit_note_no)
    {
        $filter['credit_note_no'] = $credit_note_no;
        return $this->request(ServiceConstant::URL_CREDIT_NOTE_PARCELS,$filter, self::HTTP_GET);
    }

    /**
     * @author Babatunde Otaru <tunde@cottacush.com>
     * @param $credit_note_no
     * @return array|mixed|string
     */
    public function getPrintOutDetails($credit_note_no)
    {
        $filter['credit_note_no'] = $credit_note_no;
        return $this->request(ServiceConstant::URL_CREDIT_NOTE_PRINTOUT_DETAILS,$filter,self::HTTP_GET);
    }
}
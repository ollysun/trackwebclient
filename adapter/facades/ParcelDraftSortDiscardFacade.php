<?php

namespace Adapter\facades;

use Adapter\ParcelAdapter;
use Adapter\RequestHelper;
use Adapter\ResponseHandler;
use Yii;

/**
 * Class ParcelDraftSortDiscardFacade
 * @author Adeyemi Olaoye <yemi@cottacush.com>
 * @package Adapter\facades
 */
class ParcelDraftSortDiscardFacade extends BulkOperationFacade
{

    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $data
     * @return ResponseHandler
     */
    public function doRequest($data)
    {
        $parcelsAdapter = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $response = $parcelsAdapter->discardDraftSort($data);
        return $response;
    }

    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @return string
     */
    public function getSuccessfulItemsMessage()
    {
        return parent::getSuccessfulItemsMessage('Discarded draft sortings');
    }

    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @return string
     */
    public function getFailedItemsMessage()
    {
        return parent::getFailedItemsMessage('Failed to discard some draft sortings');
    }

    /**
     * Message to display when bulk operation is fully successful
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @return mixed
     */
    public function getFullySuccessfulMessage()
    {
        return 'Draft sortings successfully discarded';
    }
}
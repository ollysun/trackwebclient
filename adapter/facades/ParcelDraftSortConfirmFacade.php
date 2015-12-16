<?php

namespace Adapter\facades;

use Adapter\ParcelAdapter;
use Adapter\RequestHelper;
use Adapter\ResponseHandler;
use Yii;

/**
 * Class ParcelDraftSortConfirmFacade
 * @author Adeyemi Olaoye <yemi@cottacush.com>
 * @package Adapter\facades
 */
class ParcelDraftSortConfirmFacade extends BulkOperationFacade
{
    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $data
     * @return ResponseHandler
     */
    public function doRequest($data)
    {
        $parcelsAdapter = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $response = $parcelsAdapter->confirmDraftSort($data);
        return $response;
    }

    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @return string
     */
    public function getSuccessfulItemsMessage()
    {
        return parent::getSuccessfulItemsMessage('Draft sortings confirmed');
    }

    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @return string
     */
    public function getFailedItemsMessage()
    {
        return parent::getFailedItemsMessage('Failed to confirm some draft sortings');
    }

    /**
     * Message to display when bulk operation is fully successful
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @return mixed
     */
    public function getFullySuccessfulMessage()
    {
        return 'Draft sortings successfully confirmed';
    }
}
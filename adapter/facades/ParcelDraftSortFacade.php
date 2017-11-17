<?php

namespace Adapter\facades;

use Adapter\ParcelAdapter;
use Adapter\RequestHelper;
use Adapter\ResponseHandler;
use Yii;

/**
 * Class ParcelDraftSortFacade
 * @author Adeyemi Olaoye <yemi@cottacush.com>
 * @package Adapter\facades
 */
class ParcelDraftSortFacade extends BulkOperationFacade
{

    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $data
     * @return ResponseHandler
     */
    public function doRequest($data)
    {
        $parcelsAdapter = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $response = $parcelsAdapter->createDraftSort($data);
        return $response;
    }

    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @return string
     */
    public function getSuccessfulItemsMessage()
    {
        return parent::getSuccessfulItemsMessage('Draft Sorted Parcels');
    }

    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @return string
     */
    public function getFailedItemsMessage()
    {
        return parent::getFailedItemsMessage('Failed to draft sort some parcels');
    }

    /**
     * Message to display when bulk operation is fully successful
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @return mixed
     */
    public function getFullySuccessfulMessage()
    {
        return 'Parcels successfully draft sorted';
    }
}
<?php

namespace Adapter\facades;

use Adapter\ResponseHandler;
use Adapter\Util\Calypso;
use yii\base\Exception;

/**
 * Class BulkOperationFacade
 * @author Adeyemi Olaoye <yemi@cottacush.com>
 * @package Adapter\facades
 */
abstract class BulkOperationFacade
{
    protected $response;
    protected $successful_key = 'successful';
    protected $failed_key = 'failed';

    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $data
     * @throws Exception
     */
    public function process($data)
    {
        $response = $this->doRequest($data);
        if (!($response instanceof ResponseHandler)) {
            throw new Exception('An unexpected error occurred while performing operation');
        }

        $this->response = $response;

        if (!$response->isSuccess()) {
            throw new Exception($response->getError());
        }
    }

    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $data
     * @return ResponseHandler
     */
    public abstract function doRequest($data);

    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @return string
     */
    public function getMessage()
    {
        return $this->getSuccessfulItemsMessage() . '<br/>' . $this->getFailedItemsMessage();
    }

    /**
     * Get successful items message
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param string $prefix
     * @return string
     */
    public function getSuccessfulItemsMessage($prefix = '')
    {
        $successfulItems = $this->getSuccessfulItems();
        if ($this->getFailedItems()) {
            return ($successfulItems) ? ('<strong>' . $prefix . ': ' . implode(', ', $successfulItems) . '</strong><br/></br>') : '';
        } else {
            return $this->getFullySuccessfulMessage();
        }
    }

    /**
     * Get Successful Items
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @return null
     */
    public function getSuccessfulItems()
    {
        return Calypso::getValue($this->response->getData(), $this->successful_key, []);
    }

    /**
     * Get Failed Items
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @return array
     */
    public function getFailedItems()
    {
        $failed_items = Calypso::getValue($this->response->getData(), $this->failed_key, []);
        return $failed_items;
    }

    /**
     * Message to display when bulk operation is fully successful
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @return mixed
     */
    public abstract function getFullySuccessfulMessage();

    /**
     * Get failed items message
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param string $prefix
     * @return string
     */
    public function getFailedItemsMessage($prefix = '')
    {
        $failedItems = $this->getFailedItems();
        $failed_message = '';

        if ($failedItems) {
            $failed_message = '<strong>' . $prefix . ':</strong> <br/>';
            foreach ($failedItems as $id => $message) {
                $failed_message .= '#<strong>' . $id . '</strong> - Reason: ' . $message . '<br/>';
            }
        }
        return $failed_message;
    }

    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @return string
     */
    public function getMessageFlashType()
    {
        $flashType = 'success';
        if ($this->getFailedItems() && $this->getSuccessfulItems()) {
            $flashType = 'warning';
        } else if ($this->getFailedItems() && !$this->getSuccessfulItems()) {
            $flashType = 'danger';
        }
        return $flashType;
    }

    /**
     * Set response successful key
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $key
     */
    public function setSuccessfulKey($key)
    {
        $this->successful_key = $key;
    }

    /**
     * Set response failed key
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $key
     */
    public function setFailedKey($key)
    {
        $this->failed_key = $key;
    }

}
<?php

namespace Adapter;

use Adapter\Globals\ServiceConstant;
use Adapter\Util\Calypso;
use yii\helpers\Json;

/**
 * Class CompanyAdapter
 * @package adapter
 * @author Adegoke Obasa <goke@cottacush.com>
 */
class CompanyAdapter extends BaseAdapter
{
    const TYPE_SHIPMENT = "shipment";
    const TYPE_PICKUP = "pickup";
    const STATUS_PENDING = 'pending';

    public function __construct()
    {
        parent::__construct(RequestHelper::getClientID(), RequestHelper::getAccessToken());
    }

    /**
     * Creates a company
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $data
     * @return bool
     */
    public function createCompany($data)
    {
        $rawResponse = $this->request(ServiceConstant::URL_COMPANY_ADD, Json::encode($data), self::HTTP_POST);
        $response = new ResponseHandler($rawResponse);

        if (!$response->isSuccess()) {
            $this->lastErrorMessage = $response->getError();
        }

        return $response->isSuccess();
    }

    /**
     * Get Companies
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $filters
     * @return array|mixed
     */
    public function getCompanies($filters)
    {

        $filters = array_merge($filters, array(
            'with_total_count' => 'true'));

        $response = $this->request(ServiceConstant::URL_COMPANY_ALL,
            $filters, self::HTTP_GET);

        $response = new ResponseHandler($response);

        if ($response->isSuccess()) {
            return $response->getData();
        }
        return [];
    }

    /**
     * Get Companies
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $filters
     * @return array|mixed
     */
    public function getUsers($filters)
    {

        $filters = array_merge($filters, array(
            'with_total_count' => 'true'));

        $response = $this->request(ServiceConstant::URL_COMPANY_USERS,
            $filters, self::HTTP_GET);

        $response = new ResponseHandler($response);

        if ($response->isSuccess()) {
            return $response->getData();
        }
        return [];
    }


    /**
     * Creates a company user
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $data
     * @return bool
     */
    public function createUser($data)
    {
        $rawResponse = $this->request(ServiceConstant::URL_USER_ADD, Json::encode($data), self::HTTP_POST);
        $response = new ResponseHandler($rawResponse);

        if (!$response->isSuccess()) {
            $this->lastErrorMessage = $response->getError();
        }

        return $response->isSuccess();
    }

    /**
     * Get shipment requests for company
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $filters
     * @return array|mixed
     */
    public function getShipmentRequests($filters)
    {
        $filters = array_merge($filters, array(
            'type' => self::TYPE_SHIPMENT,
            'with_total_count' => 'true'));

        $response = $this->request(ServiceConstant::URL_COMPANY_REQUESTS,
            $filters, self::HTTP_GET);

        $response = new ResponseHandler($response);

        if ($response->isSuccess()) {
            return $response->getData();
        }
        return [];
    }

    /**
     * Get pickup requests for company
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $filters
     * @return array|mixed
     */
    public function getPickupRequests($filters)
    {
        $filters = array_merge($filters, array(
            'type' => self::TYPE_PICKUP,
            'with_total_count' => 'true'));

        $response = $this->request(ServiceConstant::URL_COMPANY_REQUESTS,
            $filters, self::HTTP_GET);

        $response = new ResponseHandler($response);

        if ($response->isSuccess()) {
            return $response->getData();
        }
        return [];
    }

    /**
     * Make shipment request
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $data
     * @return bool
     */
    public function makeShipmentRequest($data)
    {
        $rawResponse = $this->request(ServiceConstant::URL_MAKE_SHIPMENT_REQUEST, Json::encode($data), self::HTTP_POST);
        $response = new ResponseHandler($rawResponse);

        if (!$response->isSuccess()) {
            $this->lastErrorMessage = $response->getError();
        }

        return $response->isSuccess();
    }

    /**
     * Make pickup request
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $data
     * @return bool
     */
    public function makePickupRequest($data)
    {
        $rawResponse = $this->request(ServiceConstant::URL_MAKE_PICKUP_REQUEST, Json::encode($data), self::HTTP_POST);
        $response = new ResponseHandler($rawResponse);

        if (!$response->isSuccess()) {
            $this->lastErrorMessage = $response->getError();
        }

        return $response->isSuccess();
    }
}
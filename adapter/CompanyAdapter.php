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
        if(Calypso::getValue($data, 'company.reg_no') == '') {
            $data['company']['reg_no'] = null;
        }
        $rawResponse = $this->request(ServiceConstant::URL_COMPANY_ADD, Json::encode($data), self::HTTP_POST);
        $response = new ResponseHandler($rawResponse);

        if (!$response->isSuccess()) {
            $this->lastErrorMessage = $response->getError();
        }

        return $response->isSuccess();
    }

    /**
     * Edits a company
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $data
     * @return bool
     */
    public function editCompany($data)
    {
        if(Calypso::getValue($data, 'company.reg_no') == '') {
            $data['company']['reg_no'] = null;
        }
        $rawResponse = $this->request(ServiceConstant::URL_COMPANY_EDIT, Json::encode($data), self::HTTP_POST);
        $response = new ResponseHandler($rawResponse);

        if (!$response->isSuccess()) {
            $this->lastErrorMessage = $response->getError();
        }

        return $response->isSuccess();
    }

    /**
     * Get company detail
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $id
     * @return array|mixed
     */
    public function getCompany($id)
    {
        $filters = [
            'company_id' => $id,
            'with_city' => '1',
            'with_state' => '1',
            'with_primary_contact' => '1',
            'with_relations_officer' => '1',
            'with_relations_officer_auth' => '1',
            'with_primary_contact_auth' => '1',
        ];

        $response = $this->request(ServiceConstant::URL_GET_COMPANY,
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
    public function getCompanies($filters)
    {

        $filters = array_merge($filters, array(
            'with_total_count' => 'true',
            'with_city' => 'true',
            'with_relations_officer' => 'true'
        ));

        $response = $this->request(ServiceConstant::URL_COMPANY_ALL,
            $filters, self::HTTP_GET);

        $response = new ResponseHandler($response);

        if ($response->isSuccess()) {
            return $response->getData();
        }
        return [];
    }

    /**
     * Get Companies with no pagination
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $filters
     * @return array|mixed
     */
    public function getAllCompanies($filters)
    {

        $filters = array_merge($filters, array(
            'no_paginate' => 'true'));

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
     * Edits a company user
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $data
     * @return bool
     */
    public function editUser($data)
    {
        $rawResponse = $this->request(ServiceConstant::URL_USER_EDIT, Json::encode($data), self::HTTP_POST);
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
            'with_receiver_state' => '1',
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
            'with_company' => '1',
            'with_pickup_city' => '1',
            'with_pickup_state' => '1',
            'with_destination_city' => '1',
            'with_destination_state' => '1',
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
     * Get shipment request detail
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $id
     * @return array|mixed
     */
    public function getShipmentRequest($id)
    {
        $filters = [
            'request_id' => $id,
            'with_receiver_city' => '1',
            'with_receiver_state' => '1',
            'with_company' => '1',
            'with_company_city' => '1',
            'with_created_by' => '1'];

        $response = $this->request(ServiceConstant::URL_SHIPMENT_REQUEST,
            $filters, self::HTTP_GET);

        $response = new ResponseHandler($response);

        if ($response->isSuccess()) {
            return $response->getData();
        }
        return [];
    }

    /**
     * Get pickup request detail
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $id
     * @return array|mixed
     */
    public function getPickupRequest($id)
    {
        $filters = [
            'request_id' => $id,
            'with_pickup_city' => '1',
            'with_pickup_state' => '1',
            'with_destination_city' => '1',
            'with_destination_state' => '1',
            'with_company' => '1',
            'with_created_by' => '1'];

        $response = $this->request(ServiceConstant::URL_PICKUP_REQUEST,
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
     * Make shipment request
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $data
     * @return bool
     */
    public function makeBulkShipmentRequest($data)
    {
        $rawResponse = $this->request(ServiceConstant::URL_MAKE_BULK_SHIPMENT_REQUEST, Json::encode($data), self::HTTP_POST);
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

    /**
     * Cancel shipment request
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $requestId
     * @return bool
     */
    public function cancelShipmentRequest($requestId)
    {
        $rawResponse = $this->request(ServiceConstant::URL_CANCEL_SHIPMENT_REQUEST, Json::encode(['request_id' => $requestId]), self::HTTP_POST);
        $response = new ResponseHandler($rawResponse);

        if (!$response->isSuccess()) {
            $this->lastErrorMessage = $response->getError();
        }

        return $response->isSuccess();
    }

    /**
     * Decline shipment request
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $requestId
     * @return bool
     */
    public function declineShipmentRequest($requestId)
    {
        $rawResponse = $this->request(ServiceConstant::URL_DECLINE_SHIPMENT_REQUEST, Json::encode(['request_id' => $requestId]), self::HTTP_POST);
        $response = new ResponseHandler($rawResponse);

        if (!$response->isSuccess()) {
            $this->lastErrorMessage = $response->getError();
        }

        return $response->isSuccess();
    }

    /**
     * Cancel pickup request
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $requestId
     * @return bool
     */
    public function cancelPickupRequest($requestId)
    {
        $rawResponse = $this->request(ServiceConstant::URL_CANCEL_PICKUP_REQUEST, Json::encode(['request_id' => $requestId]), self::HTTP_POST);
        $response = new ResponseHandler($rawResponse);

        if (!$response->isSuccess()) {
            $this->lastErrorMessage = $response->getError();
        }

        return $response->isSuccess();
    }

    /**
     * Decline pickup request
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $requestId
     * @return bool
     */
    public function declinePickupRequest($requestId)
    {
        $rawResponse = $this->request(ServiceConstant::URL_DECLINE_PICKUP_REQUEST, Json::encode(['request_id' => $requestId]), self::HTTP_POST);
        $response = new ResponseHandler($rawResponse);

        if (!$response->isSuccess()) {
            $this->lastErrorMessage = $response->getError();
        }

        return $response->isSuccess();
    }
}
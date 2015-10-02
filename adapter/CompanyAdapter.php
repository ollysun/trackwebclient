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

        if(!$response->isSuccess()) {
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

        if($response->isSuccess()) {
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

        if($response->isSuccess()) {
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

        if(!$response->isSuccess()) {
            $this->lastErrorMessage = $response->getError();
        }

        return $response->isSuccess();
    }

}
<?php

namespace Adapter;

use Adapter\Globals\ServiceConstant;
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

        $filters = array_merge($filters, array());
//            'with_holder' => '',
//            'with_from_branch' => '',
//            'with_sender_admin' => '',
//            'with_total_count' => 'true',
//            'with_to_branch' => ''));

        $response = $this->request(ServiceConstant::URL_COMPANY_ALL,
            $filters, self::HTTP_GET);

        $response = new ResponseHandler($response);

        if($response->isSuccess()) {
            return $response->getData();
        }
        return [];
    }

}
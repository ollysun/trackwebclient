<?php
namespace Adapter;

use Adapter\Globals\ServiceConstant;

/**
 * Class RefAdapter
 * @author Adeyemi Olaoye <yemi@cottacush.com>
 * @package Adapter
 */
class RefAdapter extends BaseAdapter
{


    public function getBanks()
    {
        return $this->request(ServiceConstant::URL_REF_BANK, array(), self::HTTP_GET);

    }

    public function getRoles()
    {
        return $this->request(ServiceConstant::URL_REF_ROLE, array(), self::HTTP_GET);

    }

    public function getBranch($state_id, $branch_type = null)
    {
        return $this->request(ServiceConstant::URL_GET_ALL_BRANCH, ['state_id' => $state_id, 'branch_type' => $branch_type], self::HTTP_GET);
    }

    public function getBranchbyId($id)
    {
        return $this->request(ServiceConstant::URL_BRANCH_GET_ONE, ['branch_id' => $id], self::HTTP_GET);
    }

    public function getShipmentType()
    {
        return $this->request(ServiceConstant::URL_REF_SHIPMENT, array(), self::HTTP_GET);
    }

    public function getdeliveryType()
    {
        return $this->request(ServiceConstant::URL_REF_deliveryType, array(), self::HTTP_GET);
    }

    public function getparcelType()
    {
        return $this->request(ServiceConstant::URL_REF_parcelType, array(), self::HTTP_GET);
    }

    /**
     * This fetches all states from the middleware
     * @param $id
     * @param int $with_regions
     * @return array|mixed|string
     */
    public function getStates($id, $with_regions = 0)
    {
        $response = $this->request(ServiceConstant::URL_REF_STATES, ['country_id' => $id, 'with_region' => $with_regions], self::HTTP_GET);
        return $response;
    }

    /**
     * This function fetches all countries from the middleware
     */
    public function getCountries()
    {
        return $this->request(ServiceConstant::URL_REF_COUNTRIES, [], self::HTTP_GET);
    }

    public function getPaymentMethods()
    {
        return $this->request(ServiceConstant::URL_REF_PAYMENT_METHODS, [], self::HTTP_GET);
    }

    public function getRegions($country_id)
    {
        return $this->request(ServiceConstant::URL_REF_REGIONS, ['country_id' => $country_id], self::HTTP_GET);
    }

    public function getWeightRanges()
    {
        return $this->request(ServiceConstant::URL_WEIGHT_FETCH_ALL, [], self::HTTP_GET);
    }

    public function getOnforwardingCharges($status=null, $offset=null, $count=null, $with_total_count=null, $send_all = null)
    {
        $filter = ['status' => $status, 'send_all' => $send_all, 'offset' => $offset, 'count' => $count,'with_total_count'=>$with_total_count ];
        return $this->request(ServiceConstant::URL_ONFORWARDING_FETCH_ALL, $filter, self::HTTP_GET);
    }

    /**
     * Get all cities
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @return array|mixed|string
     */
    public function getAllCities()
    {
        $response = $this->request(ServiceConstant::URL_REGION_CITY_GET_ALL, [], self::HTTP_GET);

        $response = new ResponseHandler($response);

        if ($response->isSuccess()) {
            return $response->getData();
        }
        return [];
    }
}
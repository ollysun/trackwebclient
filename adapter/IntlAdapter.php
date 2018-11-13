<?php
/**
 * Created by PhpStorm.
 * User: USER
 * Date: 2/10/2017
 * Time: 3:46 PM
 */

namespace Adapter;


use Adapter\Globals\ServiceConstant;

class IntlAdapter extends BaseAdapter
{
    public function getZones($filter){
        return $this->request(ServiceConstant::URL_INTL_ZONES_GET_ALL, $filter, self::HTTP_GET);
    }

    public function addZone($code, $description){
        return $this->request(ServiceConstant::URL_INTL_ADD_ZONE, ['code' => $code, 'description' => $description], self::HTTP_POST);
    }

    public function updateZone($zone,$code, $description,$percent,$sign){
        return $this->request(ServiceConstant::URL_INTL_EDIT_ZONE, ['zone'=>$zone,'code' => $code, 'description' => $description, 'percent'=>$percent, 'sign'=>$sign], self::HTTP_POST);
    }

    public function addCountryToZone($country_id, $zone_id){
        return $this->request(ServiceConstant::URL_INTL_ADD_COUNTRY_TO_ZONE,
            ['zone_id' => $zone_id, 'country_id' => $country_id], self::HTTP_POST);
    }

    public function getCountriesByZoneId($zone_id){
        return $this->request(ServiceConstant::URL_INTL_GET_COUNTRIES_BY_ZONE, ['zone_id' => $zone_id], self::HTTP_GET);
    }

    public function getWeightRange(){
        return $this->request(ServiceConstant::URL_INTL_GET_WEIGHT_RANGE, [], self::HTTP_GET);
    }

    public function addWeightRange(array $data){
        return $this->request(ServiceConstant::URL_INTL_ADD_WEIGHT_RANGE, $data, self::HTTP_POST);
    }

    public function editRange(array $data){
        return $this->request(ServiceConstant::URL_INTL_EDIT_WEIGHT_RANGE, $data, self::HTTP_POST);

    }

    public function saveTariff(array $data){
        return $this->request(ServiceConstant::URL_INTL_SAVE_TARIFF, $data, self::HTTP_POST);
    }

    public function fetchAllBilling(){
        return $this->request(ServiceConstant::URL_INTL_PRICING, ['with_zone' => 1,
            'with_parcel_type' => 1, 'with_weight_range' => 1], self::HTTP_GET);
    }

    public function fetchBilling($id){
        return $this->request(ServiceConstant::URL_INTL_PRICING, ['with_zone' => 1,
            'with_parcel_type' => 1, 'with_weight_range' => 1, 'id' => $id], self::HTTP_GET);
    }

    public function editBilling(array $data){
        return $this->request(ServiceConstant::URL_INTL_EDIT_PRICE, $data, self::HTTP_POST);
    }

    public function deleteTariff(array $data){
        return $this->request(ServiceConstant::URL_INTL_DELETE_TARIFF, $data, self::HTTP_POST);
    }

    /**
     * Delete's a weight range
     * @author Moses OLalere <moses_olalere@superfluxnigeria.com>
     * @param $weightRangeId
     * @return bool
     */
    public function deleteRange($weightRangeId)
    {
        $rawResponse = $this->request(ServiceConstant::URL_INTL_DELETE_WEIGHT,
            ['weight_range_id' => $weightRangeId], self::HTTP_POST);
        $response = new ResponseHandler($rawResponse);

        if (!$response->isSuccess()) {
            $this->lastErrorMessage = $response->getError();
        }

        return $response->isSuccess();
    }

    /**
     * Delete's a weight range
     * @author Moses Olalere <moses_olalere@superfluxnigeria.com>
     * @param $weightRangeIds
     * @param string $force_delete
     * @return bool
     * @internal param $weightRangeId
     */
    public function deleteRanges($weightRangeIds, $force_delete = '0')
    {
        $rawResponse = $this->request(ServiceConstant::URL_INTL_DELETE_WEIGHT,
            ['weight_range_ids' => $weightRangeIds, 'force_delete' => $force_delete], self::HTTP_POST);
        $response = new ResponseHandler($rawResponse);

        if (!$response->isSuccess()) {
            $this->lastErrorMessage = $response->getError();
        }

        return $response->isSuccess();
    }


}
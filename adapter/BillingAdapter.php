<?php
/**
 * Created by PhpStorm.
 * User: Icreatechub
 * Date: 8/1/2015
 * Time: 11:46 AM
 */

namespace Adapter;


use Adapter\Globals\ServiceConstant;

class BillingAdapter extends BaseAdapter
{
    public function addOnforwardingCharge($data){
        return $this->request(ServiceConstant::URL_ONFORWARDING_ADD, $data, self::HTTP_POST);
    }
    public function editOnforwardingCharge($data){
        return $this->request(ServiceConstant::URL_ONFORWARDING_EDIT, $data, self::HTTP_POST);
    }
    public function getOnforwardingCharge($id){
        return $this->request(ServiceConstant::URL_ONFORWARDING_FETCH_ID, $id, self::HTTP_GET);
    }

    /**
     * This fetches all existing billing/pricing
     * @param $billingPlanId
     * @return array|mixed|string
     */
    public function fetchAllBilling($billingPlanId = null) {
        $filters = array_filter(['billing_plan_id' => $billingPlanId]);
        return $this->request(ServiceConstant::URL_BILLING_FETCH_ALL, $filters, self::HTTP_GET);
    }

    /**
     * This adds a new billing/pricing
     * @param $data = [
     *   zone_id => 1
     *   weight_range_id => 2
     *   base_cost => 4000
     *   base_percentage => 0.9
     *   increment_cost => 1000
     *   increment_percentage => 0.85
     * ]
     * @return array|mixed|string
     */
    public function addBilling($data){
        return $this->request(ServiceConstant::URL_BILLING_ADD, $data, self::HTTP_POST);
    }

    public function editBilling($data) {
        return $this->request(ServiceConstant::URL_BILLING_EDIT, $data, self::HTTP_POST);
    }

    public function deleteBilling($data) {
        return $this->request(ServiceConstant::URL_BILLING_DELETE, $data, self::HTTP_POST);
    }

    public function fetchBillingById($data) {
        return $this->request(ServiceConstant::URL_BILLING_FETCH_BY_ID, $data, self::HTTP_GET);
    }

    public function addException(){}

}
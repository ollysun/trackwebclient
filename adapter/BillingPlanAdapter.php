<?php
namespace Adapter;

use Adapter\Globals\ServiceConstant;
use yii\helpers\Json;

/**
 * Class OnForwardingChargeAdapter
 * @package adapter
 * @author Adegoke Obasa <goke@cottacush.com>
 */
class BillingPlanAdapter extends BaseAdapter
{
    const DEFAULT_ON_FORWARDING_PLAN = 2;
    const DEFAULT_WEIGHT_RANGE_PLAN = 2600;// 4; // 2600;// 2565;

    const TYPE_WEIGHT = 1;
    const TYPE_ON_FORWARDING = 2;
    const TYPE_NUMBER = 3;
    const TYPE_WEIGHT_AND_ON_FORWARDING = 4;

    public function __construct()
    {
        parent::__construct(RequestHelper::getClientID(), RequestHelper::getAccessToken());
    }

    /**
     * Gets billing plan types
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public static function getTypes()
    {
        return [
            self::TYPE_WEIGHT => 'Weight',
            self::TYPE_ON_FORWARDING => 'OnForwarding',
            self::TYPE_NUMBER => 'Price',
            self::TYPE_WEIGHT_AND_ON_FORWARDING => 'Weight and Onforwarding',
        ];
    }

    /**
     * Creates a billing plan
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $name
     * @param $type
     * @param $companyId
     * @return bool
     */
    public function createBillingPlan($name, $type, $companyId, $discount)
    {
        $rawResponse = $this->request(ServiceConstant::URL_BILLING_PLAN_ADD, ['name' => $name, 'type' => $type, 'company_id' => $companyId, 'discount' => $discount], self::HTTP_POST);
        $response = new ResponseHandler($rawResponse);

        if (!$response->isSuccess()) {
            $this->lastErrorMessage = $response->getError();
        }

        return $response->isSuccess();
    }

    public function updateBillingDiscount($plan_id, $discount){
        $rawResponse = $this->request(ServiceConstant::URL_BILLING_PLAN_UPDATE_DISCOUNT, ['plan_id' => $plan_id, 'discount' => $discount], self::HTTP_POST);
        $response = new ResponseHandler($rawResponse);

        if (!$response->isSuccess()) {
            $this->lastErrorMessage = $response->getError();
        }

        return $response->isSuccess();
    }

    public function linkCompany($billing_plan_id, $company_id, $is_default){
        return $this->request(ServiceConstant::URL_BILLING_PLAN_LINK_COMPANY,
            ['company_id' => $company_id, 'billing_plan_id' => $billing_plan_id, 'is_default' => $is_default],
            self::HTTP_POST);
    }

    /**
     * Get's all on forwarding charges for a billing plan
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $billingPlanId
     * @return array|mixed
     */
    public function getCitiesWithCharges($billingPlanId)
    {
        $filters = [
            'billing_plan_id' => $billingPlanId,
            'with_state' => '1',
            'with_branch' => '1',
            'with_city' => '1'
        ];

        $response = $this->request(ServiceConstant::URL_BILLING_PLAN_GET_CITIES_WITH_CHARGE,
            $filters, self::HTTP_GET);

        $response = new ResponseHandler($response);

        if ($response->isSuccess()) {
            return $response->getData();
        }
        return [];
    }

    /**
     * Get's all billing plans
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param array $filters
     * @return array|mixed
     */
    public function getBillingPlans($filters = [])
    {
        $filters = array_merge([
            'linked_companies_count' => '1',
            /*'company_only' => '1',
            'with_company' => '1',*/
            'filter_removed' => 1
        ], $filters);

        $response = $this->request(ServiceConstant::URL_BILLING_PLAN_GET_ALL,
            $filters, self::HTTP_GET);

        $response = new ResponseHandler($response);

        if ($response->isSuccess()) {
            return $response->getData();
        }
        return [];
    }

    public function getCompaniesByPlan($plan_id){
        $response = new ResponseHandler($this->request(ServiceConstant::URL_BILLING_PLAN_GET_COMPANIES, ['billing_plan_id' => $plan_id], self::HTTP_GET));
        if($response->isSuccess()){
            return $response->getData();
        }
        return [];
    }

    public function getCompanyBillingPlans($filters = array()){
        $response = $this->request(ServiceConstant::URL_BILLING_PLAN_GET_COMPANY_PLANS,
            $filters, self::HTTP_GET);


        $response = new ResponseHandler($response);

        if ($response->isSuccess()) {
            return $response->getData();
        }
        return [];
    }

    public function removeCompanyFromPlan($plan_id, $company_id){
        return $this->request(ServiceConstant::URL_BILLING_PLAN_REMOVE_COMPANY, ['billing_plan_id' => $plan_id, 'company_id' => $company_id], self::HTTP_POST);
    }

    public function markPlanAsDefault($plan_id, $company_id){
        return $this->request(ServiceConstant::URL_BILLING_PLAN_MAKE_DEFAULT, ['billing_plan_id' => $plan_id, 'company_id' => $company_id], self::HTTP_POST);
    }

    /**
     * Reset onforwarding charges to zero
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $data
     * @return bool
     */
    public function resetOnforwarding($data)
    {
        $rawResponse = $this->request(ServiceConstant::URL_RESET_ONFORWARDING_CHARGES, Json::encode($data), self::HTTP_POST);
        $response = new ResponseHandler($rawResponse);

        if (!$response->isSuccess()) {
            $this->lastErrorMessage = $response->getError();
        }

        return $response->isSuccess();
    }

    /**
     * @author Babatunde Otaru <tunde@cottacush.com>
     * @param $baseBillingPlanId
     * @param $companyId
     * @param $billingPlanName
     * @return bool
     */
    public function cloneBillingPlan($baseBillingPlanId,$companyId,$billingPlanName,$discount)
    {
        $params['base_billing_plan_id'] = $baseBillingPlanId;
        $params['company_id' ] = $companyId;
        $params['billing_plan_name'] = $billingPlanName;
        $params['discount'] = $discount;
        //dd($params);
        $rawResponse = $this->request(ServiceConstant::URL_CLONE_BILLING_PLAN,$params,self::HTTP_POST);
        $response = new ResponseHandler($rawResponse);

        if(!$response->isSuccess()){
            $this->lastErrorMessage = $response->getError();
        }

        return $response->isSuccess();
    }
}
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
    const DEFAULT_WEIGHT_RANGE_PLAN = 1;

    const TYPE_WEIGHT = 1;
    const TYPE_ON_FORWARDING = 2;
    const TYPE_NUMBER = 3;

    public function __construct()
    {
        parent::__construct(RequestHelper::getClientID(), RequestHelper::getAccessToken());
    }

    /**
     * Creates a billing plan
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $name
     * @param $type
     * @param $companyId
     * @return bool
     */
    public function createBillingPlan($name, $type, $companyId)
    {
        $rawResponse = $this->request(ServiceConstant::URL_BILLING_PLAN_ADD, ['name' => $name, 'type' => $type, 'company_id' => $companyId], self::HTTP_POST);
        $response = new ResponseHandler($rawResponse);

        if (!$response->isSuccess()) {
            $this->lastErrorMessage = $response->getError();
        }

        return $response->isSuccess();
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
            'with_company' => '1',
            'with_total_count' => '1'
        ], $filters);

        $response = $this->request(ServiceConstant::URL_BILLING_PLAN_GET_ALL,
            $filters, self::HTTP_GET);

        $response = new ResponseHandler($response);

        if ($response->isSuccess()) {
            return $response->getData();
        }
        return [];
    }

    /**
     * Gets billing plan types
     * @author Adegoke Obasa <goke@cottacush.com>s
     */
    public static function getTypes()
    {
        return [
            self::TYPE_WEIGHT => 'Weight',
            self::TYPE_ON_FORWARDING => 'OnForwarding',
            self::TYPE_NUMBER => 'Price',
        ];
    }
}
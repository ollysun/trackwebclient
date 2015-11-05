<?php
namespace Adapter;
use Adapter\Globals\ServiceConstant;

/**
 * Class OnForwardingChargeAdapter
 * @package adapter
 * @author Adegoke Obasa <goke@cottacush.com>
 */
class BillingPlanAdapter extends BaseAdapter
{
    const DEFAULT_ON_FORWARDING_PLAN = 2;
    const DEFAULT_WEIGHT_RANGE_PLAN = 1;

    public function __construct()
    {
        parent::__construct(RequestHelper::getClientID(), RequestHelper::getAccessToken());
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
}
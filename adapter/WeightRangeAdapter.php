<?php
/**
 * Created by PhpStorm.
 * User: Icreatechub
 * Date: 7/30/2015
 * Time: 8:50 PM
 */

namespace Adapter;

use Adapter\BaseAdapter;
use Adapter\Globals\ServiceConstant;

class WeightRangeAdapter extends BaseAdapter
{
    public function createRange($data)
    {
        return $this->request(ServiceConstant::URL_WEIGHT_ADD, $data, self::HTTP_POST);
    }
    public function editRange($data, $task)
    {
        if($task == 'edit')
            return $this->request(ServiceConstant::URL_WEIGHT_EDIT, $data, self::HTTP_POST);
        elseif($task == 'status')
            return $this->request(ServiceConstant::URL_WEIGHT_CHANGE_STATUS, $data, self::HTTP_POST);
    }
    public function getRange($billingPlan = null)
    {
        $filters = [
            'billing_plan_id' => $billingPlan, 'offset' => 0, 'count' => 50
        ];
        $filters = array_filter($filters);
        return $this->request(ServiceConstant::URL_WEIGHT_FETCH_ALL, $filters, self::HTTP_GET);
    }

    /**
     * Delete's a weight range
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $weightRangeId
     * @return bool
     */
    public function deleteRange($weightRangeId)
    {
        $rawResponse = $this->request(ServiceConstant::URL_WEIGHT_DELETE, ['weight_range_id' => $weightRangeId], self::HTTP_POST);
        $response = new ResponseHandler($rawResponse);

        if (!$response->isSuccess()) {
            $this->lastErrorMessage = $response->getError();
        }

        return $response->isSuccess();
    }

    /**
     * Delete's a weight range
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $weightRangeIds
     * @param string $force_delete
     * @return bool
     * @internal param $weightRangeId
     */
    public function deleteRanges($weightRangeIds, $force_delete = '0')
    {
        $rawResponse = $this->request(ServiceConstant::URL_WEIGHT_DELETE,
            ['weight_range_ids' => $weightRangeIds, 'force_delete' => $force_delete], self::HTTP_POST);
        $response = new ResponseHandler($rawResponse);

        if (!$response->isSuccess()) {
            $this->lastErrorMessage = $response->getError();
        }

        return $response->isSuccess();
    }


}
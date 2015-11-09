<?php
/**
 * Created by PhpStorm.
 * User: RotelandO
 * Date: 8/5/15
 * Time: 7:42 PM
 */

namespace app\services;


use Adapter\Util\Calypso;

/**
 * Class BillingService
 * @package app\services
 * @author Adegoke Obasa <goke@cottacush.com>
 * @author Rotimi Akintewe <akintewe.rotimi@gmail.com>
 */
class BillingService {

    /**
     * Builds post data for creating and updating billing
     * @author Adegoke Obasa <goke@cottacush.com>
     * @author Rotimi Akintewe <akintewe.rotimi@gmail.com>
     * @param $data
     * @return array
     */
    public function buildPostData($data) {

        $response = [];

        if(isset($data['id'])) {
            $response['payload']['weight_billing_id'] = $data['id'];
        }
        $response['payload']['zone_id'] = $data['zone_id'];
        $response['payload']['weight_range_id'] = $data['weight_range_id'];
        $response['payload']['base_cost'] = $data['base_cost'];
        $response['payload']['base_percentage'] = !empty($data['base_percentage']) ? ((float)$data['base_percentage']) / 100 : '';
        $response['payload']['increment_cost'] = $data['increment_cost'];
        $response['payload']['increment_percentage'] = !empty($data['increment_percentage']) ? ((float)$data['increment_percentage']) / 100 : '';

        return $response;
    }
}
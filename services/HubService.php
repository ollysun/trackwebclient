<?php
/**
 * Created by PhpStorm.
 * User: RotelandO
 * Date: 7/25/15
 * Time: 10:12 PM
 */

namespace app\services;


use Adapter\Util\Calypso;

class HubService {

    public function buildPostData($data) {

        $response = [];
        $waybills = [];
        foreach ($data['waybills'] as $wb) {
            $waybills[] = $wb['number'];
        }

        $response['waybill_numbers'] = implode(",", $waybills);
        $response['to_branch_id'] = Calypso::getValue($data, 'to_branch_id', null);
        $response['held_by_id'] = Calypso::getValue($data, 'staff_id', null);
        $response['staff_code'] = Calypso::getValue($data, 'staff_code', null);
        return $response;
    }
}
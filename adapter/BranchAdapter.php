<?php
/**
 * Created by PhpStorm.
 * User: RotelandO
 * Date: 7/20/15
 * Time: 3:40 PM
 */

namespace Adapter;


use Adapter\Globals\ServiceConstant;

class BranchAdapter extends BaseAdapter {

    const BRANCH_TYPE_HUB = 2;
    const BRANCH_TYPE_EC = 1;

    public function listECForHub($hub_id){
        return $this->request(ServiceConstant::URL_GET_ALL_EC_IN_HUB, [ 'hub_id' => $hub_id ],self::HTTP_GET);
    }

    public function getAllHubs($branch_type = self::BRANCH_TYPE_HUB) {
        return $this->request(ServiceConstant::URL_GET_ALL_BRANCH, [ 'branch_type' => $branch_type ],self::HTTP_GET);
    }
}
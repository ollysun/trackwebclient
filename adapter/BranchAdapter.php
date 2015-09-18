<?php
namespace Adapter;

use Adapter\BaseAdapter;
use Adapter\Globals\ServiceConstant;

/**
 * Class BranchAdapter
 * @author Adeyemi Olaoye <yemi@cottacush.com>
 * @author Richard Boyewa <boye@cottacush.com>
 * @author Rotimi Akintewe <akintewe.rotimi@gmail.com>
 * @package Adapter
 */
class BranchAdapter extends BaseAdapter
{
    const BRANCH_TYPE_HUB = 2;
    const BRANCH_TYPE_EC = 1;


    public function createNewHub($postData)
    {
        return $this->request(ServiceConstant::URL_BRANCH_ADD, $postData, self::HTTP_POST);
    }

    public function editOneHub($postData, $operation = 'edit')
    {
        if ($operation == 'status')
            return $this->request(ServiceConstant::URL_BRANCH_CHANGE_STATUS, $postData, self::HTTP_POST);
        else
            return $this->request(ServiceConstant::URL_BRANCH_EDIT, $postData, self::HTTP_POST);
    }

    public function getOneHub($id)
    {
        $filter = 'branch_type=' . ServiceConstant::BRANCH_TYPE_HUB;
        $filter .= '&branch_id=' . $id;
        return $this->request(ServiceConstant::URL_BRANCH_GET_ONE . '?' . $filter, array(), self::HTTP_GET);
    }

    public function getHubs($state = null, $offset = 0, $count = 50)
    {
        $filter = 'branch_type=' . ServiceConstant::BRANCH_TYPE_HUB;
        $filter .= ($state != null ? '&state_id=' . $state : '');
        $filter .= ($offset > -1) ? '&offset=' . $offset . '&count=' . $count : '';
        return $this->request(ServiceConstant::URL_BRANCH_GET_ALL . '?' . $filter, array(), self::HTTP_GET);
    }

    public function createNewCentre($postData)
    {
        return $this->request(ServiceConstant::URL_BRANCH_ADD, $postData, self::HTTP_POST);
    }

    public function editOneCentre($postData, $operation = 'edit')
    {
        if ($operation == 'status')
            return $this->request(ServiceConstant::URL_BRANCH_CHANGE_STATUS, $postData, self::HTTP_POST);
        elseif ($operation == 'relink')
            return $this->request(ServiceConstant::URL_BRANCH_RELINK, $postData, self::HTTP_POST);
        else
            return $this->request(ServiceConstant::URL_BRANCH_EDIT, $postData, self::HTTP_POST);
    }

    public function getOneCentre($id)
    {
        return $this->request(ServiceConstant::URL_BRANCH_GET_ONE, array('id' => $id), self::HTTP_GET);
    }

    public function getCentres($hub_id = null, $offset = 0, $count = 50)
    {
        $url = ServiceConstant::URL_BRANCH_GET_ALL;
        if ($hub_id == null) {
            $filter = "branch_type=" . ServiceConstant::BRANCH_TYPE_EC;
        } else {
            $url = ServiceConstant::URL_BRANCH_GET_ALL_EC;
            $filter = ($hub_id != null ? 'hub_id=' . $hub_id : '');
        }
        $filter .= ($offset > -1) ? '&offset=' . $offset . '&count=' . $count : '';
        return $this->request($url . '?' . $filter . '&with_parent=1', array(), self::HTTP_GET);;
    }

    public function listECForHub($hub_id)
    {
        return $this->request(ServiceConstant::URL_GET_ALL_EC_IN_HUB, ['hub_id' => $hub_id], self::HTTP_GET);
    }

    /**
     * Get all Hubs
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @author Richard Boyewa <boye@cottacush.com>
     * @param int $branch_type
     * @param bool|true $paginate
     * @return array|mixed|string
     */
    public function getAllHubs($branch_type = self::BRANCH_TYPE_HUB, $paginate = true)
    {
        return $this->request(ServiceConstant::URL_GET_ALL_BRANCH, ['branch_type' => $branch_type, 'paginate' => var_export($paginate, true)], self::HTTP_GET);
    }

    public function getMatrix()
    {
        return $this->request(ServiceConstant::URL_ZONES_MATRIX_GET, [], self::HTTP_GET);
    }

    public function getAll()
    {
        return $this->request(ServiceConstant::URL_GET_ALL_BRANCH, [], self::HTTP_GET);
    }
}
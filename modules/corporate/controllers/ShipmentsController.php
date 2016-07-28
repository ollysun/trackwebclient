<?php
/**
 * Created by PhpStorm.
 * User: ELACHI
 * Date: 7/25/2016
 * Time: 12:32 PM
 */

namespace app\modules\corporate\controllers;

use Adapter\BranchAdapter;
use Adapter\CompanyAdapter;
use Adapter\Globals\ServiceConstant;
use Adapter\ParcelAdapter;
use Adapter\RefAdapter;
use Adapter\RequestHelper;
use Adapter\ResponseHandler;
use Adapter\Util\Calypso;
use Adapter\Util\Util;
use Adapter\TellerAdapter;
use app\controllers\BaseController;
use Yii;
use Adapter\RouteAdapter;

class ShipmentsController extends BaseController
{
    public $userData = null;
    public $branch_to_view = null;

    public function beforeAction($action)
    {
        $this->userData = (Calypso::getInstance()->session('user_session'));
        $this->branch_to_view = ($this->userData['role_id'] == ServiceConstant::USER_TYPE_SUPER_ADMIN) ? null :
            ($this->userData['role_id'] == ServiceConstant::USER_TYPE_ADMIN) ? null : Calypso::getValue($this->userData, 'branch_id'); //displays all when null
        //print_r($this->userData);
        if (empty($this->userData)) {
            return false;
        }
        return parent::beforeAction($action);
    }

    public function actionNew()
    {
        return $this->render('new');
    }

    /**
     * Prepares the report based based on filters
     * @param int $page
     * @param null $page_width
     * @return string
     */
    public function actionAll($page = 1, $search = false, $page_width = null)
    {
        $companyId = Calypso::getValue(Calypso::getInstance()->session("user_session"), 'company_id');
        $from_date = date('Y-m-d');
        $to_date = date('Y-m-d');
        $search_action = $search;
        if ($page_width != null) {
            $this->page_width = $page_width;
            Calypso::getInstance()->cookie('page_width', $page_width);
        }

        $page_width = is_null($page_width) ? $this->page_width : $page_width;
        $offset = ($page - 1) * $page_width;
        $parcel = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());


        if (!empty(Calypso::getInstance()->get()->search)) { //check if not empty criteria
            $search = Calypso::getInstance()->get()->search;
            $response = $parcel->getSearchParcels(null, $search, $offset, $this->page_width, 1, $this->branch_to_view, 1, null);
            $search_action = true;
            $filter = null;

        }elseif (isset(Calypso::getInstance()->get()->from, Calypso::getInstance()->get()->to)) {
            $from_date = Calypso::getInstance()->get()->from;
            $to_date = Calypso::getInstance()->get()->to;
            $filter = null;
            $response = $parcel->getCorporateParcels($offset, $this->page_width,
                ['start_created_date' => $from_date. ' 00:00:00', 'end_created_date' => $to_date. ' 23:59:59',
                    'company_id' => $companyId, 'report' => 1, 'with_receiver' => 1, 'with_parcel_comment' => 1]);
            $search_action = true;
        }
        else {
            $filter = null;
            $response = $parcel->getCorporateParcels($offset, $this->page_width,
                ['start_created_date' => $from_date. ' 00:00:00', 'end_created_date' => $to_date. ' 23:59:59',
                'company_id' => $companyId, 'report' => 1, 'with_receiver' => 1, 'with_parcel_comment' => 1]);
            $search_action = true;
        }

        //$response = new ResponseHandler($response);
        $data = $response;

        $total_count = 0;// $data['total_count'];
        if (isset($data['total_count'])) {
            $total_count = $data['total_count'];
        }
        if (isset($data['parcels'])) {
            $data = $data['parcels'];
            $total_count = $total_count <= 0 ? count($data) : $total_count;
        }


        return $this->render('all', array('filter' => $filter, 'parcels' => $data, 'from_date' => $from_date, 'to_date' => $to_date, 'offset' => $offset, 'page_width' => $this->page_width, 'search' => $search_action, 'total_count' => $total_count));
    }

}
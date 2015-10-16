<?php
/**
 * @author Adegoke Obasa <goke@cottacush.com>
 */

namespace app\modules\corporate\controllers;


use Adapter\CompanyAdapter;
use Adapter\RefAdapter;
use Adapter\RegionAdapter;
use Adapter\RequestHelper;
use Adapter\ResponseHandler;
use Adapter\Util\Calypso;
use Adapter\Util\ResponseCodes;
use Adapter\Util\ResponseMessages;
use Adapter\Util\Util;
use app\controllers\BaseController;
use app\modules\corporate\models\BulkShipment;
use app\traits\CorporateRequestFilter;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;

class RequestController extends BaseController
{

    use CorporateRequestFilter;

    /**
     * Company requests action
     * @author Adegoke Obasa <goke@cottacush.com>
     * @author Olajide Oye <jide@cottacush.com>
     * @return string
     */
    public function actionShipments()
    {
        $companyAdapter = new CompanyAdapter();
        $companyId = Calypso::getValue(Calypso::getInstance()->session("user_session"), 'company_id');

        $filters = [];

        $filters = array_merge($filters, $this->getCreatedAtFilters());

        // Add Offset and Count
        $page = \Yii::$app->getRequest()->get('page', 1);

        $query = \Yii::$app->getRequest()->get('search');
        if (!is_null($query)) {
            $filters = [];
            $filters['waybill_number'] = $query;
            $page = 1; // Reset page
        }

        $filters['company_id'] = $companyId;

        $offset = ($page - 1) * $this->page_width;
        $filters['offset'] = $offset;
        $filters['count'] = $this->page_width;

        $requestsData = $companyAdapter->getShipmentRequests($filters);

        $countriesResponse = (new RefAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken()))->getCountries();
        $countries = (new ResponseHandler($countriesResponse))->getData();

        $refAdapter = new RefAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $states = (new ResponseHandler($refAdapter->getStates(1)))->getData();


        $requests = Calypso::getValue($requestsData, 'requests', []);
        $totalCount = Calypso::getValue($requestsData, 'total_count', 0);

        return $this->render('shipment', [
            'requests' => $requests,
            'offset' => $offset,
            'page_width' => $this->page_width,
            'countries' => $countries,
            'states' => $states,
            'total_count' => $totalCount,
            'from_date' => $this->getFromCreatedAtDate($filters),
            'to_date' => $this->getToCreatedAtDate($filters)
        ]);
    }

    /**
     * Company pickup requests action
     * @author Adegoke Obasa <goke@cottacush.com>
     * @author Olajide Oye <jide@cottacush.com>
     * @return string
     */
    public function actionPickups()
    {
        $companyAdapter = new CompanyAdapter();
        $companyId = Calypso::getValue(Calypso::getInstance()->session("user_session"), 'company_id');

        $filters = [];

        $filters = array_merge($filters, $this->getCreatedAtFilters());

        // Add Offset and Count
        $page = \Yii::$app->getRequest()->get('page', 1);

        $query = \Yii::$app->getRequest()->get('search');
        if (!is_null($query)) {
            $filters = [];
            $filters['waybill_number'] = $query;
            $page = 1; // Reset page
        }

        $filters['company_id'] = $companyId;

        $offset = ($page - 1) * $this->page_width;
        $filters['offset'] = $offset;
        $filters['count'] = $this->page_width;

        $requestsData = $companyAdapter->getPickupRequests($filters);

        $countriesResponse = (new RefAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken()))->getCountries();
        $countries = (new ResponseHandler($countriesResponse))->getData();

        $refAdapter = new RefAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $states = (new ResponseHandler($refAdapter->getStates(1)))->getData();


        $requests = Calypso::getValue($requestsData, 'requests', []);
        $totalCount = Calypso::getValue($requestsData, 'total_count', 0);

        return $this->render('pickup', [
            'requests' => $requests,
            'offset' => $offset,
            'page_width' => $this->page_width,
            'countries' => $countries,
            'states' => $states,
            'total_count' => $totalCount,
            'from_date' => $this->getFromCreatedAtDate($filters),
            'to_date' => $this->getToCreatedAtDate($filters)
        ]);
    }

    /**
     * Make shipment request form action
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return string
     */
    public function actionCreateshipment()
    {
        $companyAdapter = new CompanyAdapter();
        $companyId = Calypso::getValue(Calypso::getInstance()->session("user_session"), 'company_id');

        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();
            $data['company_id'] = $companyId;

            $status = $companyAdapter->makeShipmentRequest($data);
            if ($status) {
                $this->flashSuccess("Shipment request created successfully");
            } else {
                $this->flashError($companyAdapter->getLastErrorMessage());
            }
        }
        return $this->redirect(Url::to('/corporate/request/shipments'));
    }

    /**
     * Make pickup request form action
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return string
     */
    public function actionCreatepickup()
    {
        $companyAdapter = new CompanyAdapter();
        $companyId = Calypso::getValue(Calypso::getInstance()->session("user_session"), 'company_id');

        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();
            $data['company_id'] = $companyId;

            $status = $companyAdapter->makePickupRequest($data);
            if ($status) {
                $this->flashSuccess("Pickup request created successfully");
            } else {
                $this->flashError($companyAdapter->getLastErrorMessage());
            }
        }
        return $this->redirect(Url::to('/corporate/request/pickups'));
    }

    /**
     * View Pickup Request Action
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function actionViewpickup()
    {
        $id = Yii::$app->getRequest()->get('id');

        $request = (new CompanyAdapter())->getPickupRequest($id);
        return $this->render('viewpickup', ['request' => $request]);
    }

    /**
     * View Shipment Request Action
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function actionViewshipment()
    {
        $id = Yii::$app->getRequest()->get('id');

        $request = (new CompanyAdapter())->getShipmentRequest($id);
        return $this->render('viewshipment', ['request' => $request]);
    }

    /**
     * Download bulk shipment request template file
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     */
    public function actionDownloadtemplatefile()
    {
        BulkShipment::generateTemplateFile();

    }
}
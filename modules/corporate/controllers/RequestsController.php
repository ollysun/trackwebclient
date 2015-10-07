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
use app\controllers\BaseController;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;

class RequestsController extends BaseController
{

    /**
     * Company requests action
     * @author Adegoke Obasa <goke@cottacush.com>
     * @author Olajide Oye <jide@cottacush.com>
     * @return string
     */
    public function actionIndex()
    {
        $companyAdapter = new CompanyAdapter();
        $companyId = Calypso::getValue(Calypso::getInstance()->session("user_session"), 'company_id');

        if(Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();
            $data['company_id'] = $companyId;

            $status = $companyAdapter->makeShipmentRequest($data);
            if($status) {
                $this->flashSuccess("Shipment request created successfully");
            } else {
                $this->flashSuccess($companyAdapter->getLastErrorMessage());
            }
            return $this->refresh();
        }

        $filters = [
            'company_id' => $companyId
        ];

        // Add Offset and Count
        $page = \Yii::$app->getRequest()->get('page', 1);

        $query = \Yii::$app->getRequest()->get('search');
        if(!is_null($query)) {
            $filters['waybill_number'] = $query;
            $page = 1; // Reset page
        }

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

        return $this->render('index', [
            'requests' => $requests,
            'offset' => $offset,
            'page_width' => $this->page_width,
            'countries' => $countries,
            'states' => $states,
            'total_count' => $totalCount
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

        if(Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();
            $data['company_id'] = $companyId;

            $status = $companyAdapter->makeShipmentRequest($data);
            if($status) {
                $this->flashSuccess("Shipment request created successfully");
            } else {
                $this->flashSuccess($companyAdapter->getLastErrorMessage());
            }
            return $this->refresh();
        }

        $this->redirect(Url::to('/corporate/requests'));
    }

    /**
     * Make shipment request form action
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return string
     */
    public function actionCreatepickup()
    {
        $companyAdapter = new CompanyAdapter();
        $companyId = Calypso::getValue(Calypso::getInstance()->session("user_session"), 'company_id');

        if(Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();
            $data['company_id'] = $companyId;

            $status = $companyAdapter->makePickupRequest($data);
            if($status) {
                $this->flashSuccess("Pickup request created successfully");
            } else {
                $this->flashSuccess($companyAdapter->getLastErrorMessage());
            }
            return $this->refresh();
        }

        $this->redirect(Url::to('/corporate/requests'));
    }

    public function actionTest()
    {
        echo "Hello";
        exit;
    }
}
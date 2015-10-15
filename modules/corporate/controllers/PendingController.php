<?php
/**
 * @author Adegoke Obasa <goke@cottacush.com>
 */

namespace app\modules\corporate\controllers;


use Adapter\CompanyAdapter;
use Adapter\ResponseHandler;
use Adapter\Util\Calypso;
use Adapter\Util\Util;
use app\controllers\BaseController;
use app\traits\CorporateRequestFilter;
use app\traits\CreatedAtFilter;

class PendingController extends BaseController
{
    use CorporateRequestFilter;
    /**
     * Pending Shipment Requests Action
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function actionShipments()
    {
        $companyAdapter = new CompanyAdapter();

        $filters = [
            'status' => CompanyAdapter::STATUS_PENDING,
            'with_company' => '1'
        ];

        $filters = array_merge($filters, $this->getCreatedAtFilters());

        // Add Offset and Count
        $page = \Yii::$app->getRequest()->get('page', 1);

        $companyId = \Yii::$app->getRequest()->get('company_id');
        if(!empty($companyId) ) {
            $filters['company_id'] = $companyId;
            $page = 1; // Reset page
        }

        $offset = ($page - 1) * $this->page_width;
        $filters['offset'] = $offset;
        $filters['count'] = $this->page_width;

        $requestsData = $companyAdapter->getShipmentRequests($filters);

        $companies = (new CompanyAdapter())->getAllCompanies([]);

        $requests = Calypso::getValue($requestsData, 'requests', []);
        $totalCount = Calypso::getValue($requestsData, 'total_count', 0);

        return $this->render('shipment', [
            'requests' => $requests,
            'companies' => $companies,
            'offset' => $offset,
            'page_width' => $this->page_width,
            'companyId' => $companyId,
            'total_count' => $totalCount,
            'from_date' => $this->getFromCreatedAtDate($filters),
            'to_date' => $this->getToCreatedAtDate($filters)
        ]);
    }

    /**
     * Pending Pickup Requests action
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function actionPickups()
    {
        $companyAdapter = new CompanyAdapter();

        $filters = [
            'status' => CompanyAdapter::STATUS_PENDING,
            'with_company' => '1'
        ];

        $filters = array_merge($filters, $this->getCreatedAtFilters());

        // Add Offset and Count
        $page = \Yii::$app->getRequest()->get('page', 1);

        $companyId = \Yii::$app->getRequest()->get('company_id');
        if(!empty($companyId)) {
            $filters['company_id'] = $companyId;
            $page = 1; // Reset page
        }

        $offset = ($page - 1) * $this->page_width;
        $filters['offset'] = $offset;
        $filters['count'] = $this->page_width;

        $requestsData = $companyAdapter->getPickupRequests($filters);

        $companies = (new CompanyAdapter())->getAllCompanies([]);

        $requests = Calypso::getValue($requestsData, 'requests', []);
        $totalCount = Calypso::getValue($requestsData, 'total_count', 0);

        return $this->render('pickup', [
            'requests' => $requests,
            'companies' => $companies,
            'offset' => $offset,
            'page_width' => $this->page_width,
            'total_count' => $totalCount,
            'companyId' => $companyId,
            'from_date' => $this->getFromCreatedAtDate($filters),
            'to_date' => $this->getToCreatedAtDate($filters)
        ]);
    }
}
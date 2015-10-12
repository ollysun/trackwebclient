<?php
/**
 * @author Adegoke Obasa <goke@cottacush.com>
 */

namespace app\modules\corporate\controllers;


use Adapter\CompanyAdapter;
use Adapter\Util\Calypso;
use Adapter\Util\Util;
use app\controllers\BaseController;

class PendingController extends BaseController
{
    /**
     * Pending Shipment Requests Action
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function actionShipments()
    {
        $companyAdapter = new CompanyAdapter();

        $filters = [
            'status' => CompanyAdapter::STATUS_PENDING
        ];
        $defaultDate = Util::today();
        $validFilters = ['from' => 'start_created_date', 'to' => 'end_created_date'];

        foreach ($validFilters as $clientFilter => $serverFilter) {
            $value = \Yii::$app->getRequest()->get($clientFilter, $defaultDate);
            if (preg_match('/\bstart\_\w+\_date\b/', $serverFilter)) {
                $filters[$serverFilter] = $value . " 00:00:00";
            } else if (preg_match('/\bend\_\w+\_date\b/', $serverFilter)) {
                $filters[$serverFilter] = $value . " 23:59:59";
            }
        }

        $fromDate = Calypso::getValue($filters, 'start_created_date', $defaultDate);
        $toDate = Calypso::getValue($filters, 'end_created_date', $defaultDate);

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

        $requests = Calypso::getValue($requestsData, 'requests', []);
        $totalCount = Calypso::getValue($requestsData, 'total_count', 0);

        return $this->render('shipment', [
            'requests' => $requests,
            'offset' => $offset,
            'page_width' => $this->page_width,
            'total_count' => $totalCount,
            'from_date' => $fromDate,
            'to_date' => $toDate
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
            'status' => CompanyAdapter::STATUS_PENDING
        ];

        $defaultDate = Util::today();
        $validFilters = ['from' => 'start_created_date', 'to' => 'end_created_date'];

        foreach ($validFilters as $clientFilter => $serverFilter) {
            $value = \Yii::$app->getRequest()->get($clientFilter, $defaultDate);
            if (preg_match('/\bstart\_\w+\_date\b/', $serverFilter)) {
                $filters[$serverFilter] = $value . " 00:00:00";
            } else if (preg_match('/\bend\_\w+\_date\b/', $serverFilter)) {
                $filters[$serverFilter] = $value . " 23:59:59";
            }
        }

        $fromDate = Calypso::getValue($filters, 'start_created_date', $defaultDate);
        $toDate = Calypso::getValue($filters, 'end_created_date', $defaultDate);

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

        $requestsData = $companyAdapter->getPickupRequests($filters);


        $requests = Calypso::getValue($requestsData, 'requests', []);
        $totalCount = Calypso::getValue($requestsData, 'total_count', 0);

        return $this->render('pickup', [
            'requests' => $requests,
            'offset' => $offset,
            'page_width' => $this->page_width,
            'total_count' => $totalCount,
            'from_date' => $fromDate,
            'to_date' => $toDate
        ]);
    }
}
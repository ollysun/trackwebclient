<?php
/**
 * @author Adegoke Obasa <goke@cottacush.com>
 */

namespace app\modules\corporate\controllers;


use Adapter\CompanyAdapter;
use Adapter\Util\Calypso;
use app\controllers\BaseController;

class PendingController extends BaseController
{
    /**
     *
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function actionShipments()
    {
        $companyAdapter = new CompanyAdapter();

        $filters = [
            'status' => CompanyAdapter::STATUS_PENDING
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

        $requests = Calypso::getValue($requestsData, 'requests', []);
        $totalCount = Calypso::getValue($requestsData, 'total_count', 0);

        return $this->render('shipment', [
            'requests' => $requests,
            'offset' => $offset,
            'page_width' => $this->page_width,
            'total_count' => $totalCount
        ]);
    }

    /**
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function actionPickups()
    {

    }
}
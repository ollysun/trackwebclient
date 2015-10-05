<?php
/**
 * @author Adegoke Obasa <goke@cottacush.com>
 */

namespace app\modules\corporate\controllers;


use Adapter\CompanyAdapter;
use Adapter\Util\Calypso;
use app\controllers\BaseController;
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

        $filters = [
            'company_id' => $companyId
        ];

        // Add Offset and Count
        $page = \Yii::$app->getRequest()->get('page', 1);

        $query = \Yii::$app->getRequest()->get('search');
        if(!is_null($query)) {
//            $filters['email'] = $query;
            $page = 1; // Reset page
        }

        $offset = ($page - 1) * $this->page_width;
        $filters['offset'] = $offset;
        $filters['count'] = $this->page_width;

        $shipments = $companyAdapter->getShipmentRequests($filters);
//        $users = Calypso::getValue($shipmentsData, 'users', []);
//        $totalCount = Calypso::getValue($shipmentsData, 'total_count', 0);

        return $this->render('index', [
            'shipments' => $shipments,
            'offset' => $offset,
            'page_width' => $this->page_width
        ]);
    }
}
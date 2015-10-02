<?php
/**
 * @author Adegoke Obasa <goke@cottacush.com>
 */

namespace app\modules\corporate\controllers;


use Adapter\CompanyAdapter;
use Adapter\Util\Calypso;
use app\controllers\BaseController;
use yii\web\Controller;

class UsersController extends BaseController
{

    /**
     * @author Adegoke Obasa <goke@cottacush.com>
     * @author Olajide Oye <jide@cottacush.com>
     * @return string
     */
    public function actionIndex()
    {
        $companyAdapter = new CompanyAdapter();
        $companyId = Calypso::getValue(Calypso::getInstance()->session("user_session"), 'company_id');

        $filters = [];

        // Add Offset and Count
        $page = \Yii::$app->getRequest()->get('page', 1);
        $offset = ($page - 1) * $this->page_width;
        $filters['offset'] = $offset;
        $filters['count'] = $this->page_width;

        $query = \Yii::$app->getRequest()->get('search');
        if(!is_null($query)) {
//            $filters = ['name' => $query];
            $page = 1; // Reset page
        }

        $usersData = $companyAdapter->getUsers($filters);
        $users = Calypso::getValue($usersData, 'users', []);
        $totalCount = Calypso::getValue($usersData, 'total_count', 0);

        return $this->render('index', [
            'users' => $users,
            'offset' => $offset,
            'total_count' => $totalCount,
            'page_width' => $this->page_width
        ]);
    }
}
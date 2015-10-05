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
     *
     * @author Adegoke Obasa <goke@cottacush.com>
     * @author Olajide Oye <jide@cottacush.com>
     * @return string
     */
    public function actionIndex()
    {
        $companyAdapter = new CompanyAdapter();
        $companyId = Calypso::getValue(Calypso::getInstance()->session("user_session"), 'company_id');


        if(\Yii::$app->request->isPost) {
            $data = \Yii::$app->request->post();

            $data['company_id'] = $companyId;

            // Create User
            $status = $companyAdapter->createUser($data);
            if ($status) {
                $this->flashSuccess("User created successfully");
            } else {
                $this->flashError($companyAdapter->getLastErrorMessage());
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
            $filters['email'] = $query;
            $page = 1; // Reset page
        }

        $offset = ($page - 1) * $this->page_width;
        $filters['offset'] = $offset;
        $filters['count'] = $this->page_width;

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
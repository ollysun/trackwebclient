<?php

namespace app\modules\corporate\controllers;

use app\controllers\BaseController;
use yii\web\Controller;

/**
 * Class DefaultController
 * @package app\modules\admin\controllers
 * @author Adegoke Obasa <goke@cottacush.com>
 */
class DefaultController extends Controller
{

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    /**
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
}

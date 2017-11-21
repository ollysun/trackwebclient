<?php

namespace app\modules\sales\controllers;

use yii\web\Controller;

/**
 * Default controller for the `sales` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
}

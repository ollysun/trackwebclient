<?php
/**
 * @author Adegoke Obasa <goke@cottacush.com>
 */

namespace app\modules\corporate\controllers;


use yii\web\Controller;

class PendingController extends Controller
{
    /**
     * @author Adegoke Obasa <goke@cottacush.com>
     * @author Olajide Oye <jide@cottacush.com>
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
}
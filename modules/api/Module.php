<?php
/**
 * Created by PhpStorm.
 * User: ELACHI
 * Date: 11/3/2016
 * Time: 1:20 PM
 */

namespace app\modules\api;


class Module extends \yii\base\Module
{

    public $controllerNamespace = 'app\modules\api\controllers';

    public function init()
    {
        parent::init();

        $this->modules = [
            'v1' => [
                'class' => 'app\modules\api\modules\v1\Module',
            ],
        ];
    }

}
<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Olajide Oye
 */
class TrackingAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/libs/font-awesome.min.css',
        'css/compiled/tracking.css',
    ];
    public $js = [
        //globals
        'js/libs/pace.min.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}

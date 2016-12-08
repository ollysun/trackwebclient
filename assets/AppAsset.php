<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        //bootstrap
        //'css/bootstrap/bootstrap.min.css',
        //libraries
        'css/libs/font-awesome.min.css',
        'css/libs/nanoscroller.css',
        //theme
        'css/compiled/courier-plus.css'
    ];
    public $js = [
        //globals
        //'js/libs/jquery.js',
        'js/libs/bootstrap.min.js',
        'js/libs/jquery.nanoscroller.min.js',
        //theme scripts
        'js/libs/scripts.js',
        'js/libs/pace.min.js',
        'js/libs/underscore-min.js',
        'js/libs/knockout-3.4.0.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}

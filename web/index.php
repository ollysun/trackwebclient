<?php
session_start();
// comment out the following two lines when deployed to production

if (!in_array(getenv("APPLICATION_ENV"), ["production", "staging"])) {
    defined('YII_DEBUG') or define('YII_DEBUG', true);
    defined('YII_ENV') or define('YII_ENV', 'dev');
}

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

$config = require(__DIR__ . '/../config/web.php');
Yii::setAlias("@Adapter", __DIR__ . "/../adapter");
(new yii\web\Application($config))->run();

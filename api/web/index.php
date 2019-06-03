<?php
ini_set('display_errors', 'on');
//error_reporting(E_ERROR | E_NOTICE | E_WARNING );

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require(__DIR__ . '/../../vendor/autoload.php');
require(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/../../common/config/bootstrap.php');
require(__DIR__ . '/../../common/components/CHelper.php');
require(__DIR__ . '/../../api/modules/v1/models/RestHelper.php');

//CHelper::debug(get_included_files());

$config = yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../../common/config/main.php'),
    require(__DIR__ . '/../../common/config/main-local.php'),
    require(__DIR__ . '/../config/main.php'),
    require(__DIR__ . '/../config/main-local.php')
);
$application = new yii\web\Application($config);
$application->run();

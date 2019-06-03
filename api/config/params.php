<?php
return [
    'adminEmail' => 'admin@example.com',
    'SUCCESS_CODE'=>200,
    'ERROR_CODE'=>403,
    'app_version'=>'0.86',
    'app_version_android'=>'1.0',
    'app_version_ios'=>'1.0',    
    'user.apiAccessTokenExpire' => 3600*24*1,
];

Yii::$app->params['user.apiAccessTokenExpire'];

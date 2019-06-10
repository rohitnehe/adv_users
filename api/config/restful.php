<?php

return [
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => 'v1/user',
        'extraPatterns' => [
            'OPTIONS,HEAD,POST login' => 'login',
            'POST forgot-password' => 'forgot-password',
            'POST reset-password' => 'reset-password',
            'POST change-password' => 'change-password',            
            'POST logout' => 'logout',
       ],
        
    ],
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => 'v1/profile',
//        'extraPatterns' => [
//            'POST set-profile' => 'set-profile',
//       ],
        
    ]
];



<?php
return [
   'components' => [
       'db' => [
           'class' => 'yii\db\Connection',
           'dsn' => 'mysql:host=hostname;dbname=databasename',
           'username' => 'username',
           'password' => 'password',
           'charset' => 'utf8',
           'tablePrefix' => 'tbl_',
       ],
    'mailer' => [
          'class' => 'yii\swiftmailer\Mailer',
          'viewPath' => '@common/mail',
          'transport' => [
              'class' => 'Swift_SmtpTransport',
              'host' => 'smtp.gmail.com',
              'username' => 'username',
              'password' => 'password',
              'port' => '587',
              'encryption' => 'tls',
          ],
          'useFileTransport' => false,
      ],
     
   ],
];

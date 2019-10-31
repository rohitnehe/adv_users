<?php
return [
   'components' => [
       'db' => [
           'class' => 'yii\db\Connection',
           'dsn' => 'mysql:host=hostname;dbname=databasename',
           'username' => 'mysqlusername',
           'password' => 'mysqlpassword',
           'charset' => 'utf8',
           'tablePrefix' => 'tbl_',
       ],
    'mailer' => [
          'class' => 'yii\swiftmailer\Mailer',
          'viewPath' => '@common/mail',
          'transport' => [
              'class' => 'Swift_SmtpTransport',
              'host' => 'smtp.gmail.com',
              'username' => 'smtpusername',
              'password' => 'smtppassword',
              'port' => '587',
              'encryption' => 'tls',
          ],
          'useFileTransport' => false,
      ],
     
   ],
];

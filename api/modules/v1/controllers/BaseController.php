<?php

namespace api\modules\v1\controllers;


use \yii\rest\ActiveController as baseActiveController;

class BaseController extends \yii\rest\ActiveController
{
   public function init()
   {
       parent::init();       
   }
   
   public function actionOptions()
   {
        Yii::$app->getResponse()->getHeaders()->set('Allow', implode(', ', ['OPTIONS', 'POST', 'GET']));
   } 
}

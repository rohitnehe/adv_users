<?php

namespace api\modules\v1\controllers;


use \yii\rest\ActiveController as baseActiveController;

//\yii\base\Event::on(baseActiveController::className(), baseActiveController::EVENT_BEFORE_ACTION, ['api\modules\v1\models\LogHandler', 'saveRequest'], ['request' => \Yii::$app->request->getBodyParams(), 'response' => \Yii::$app->response->content]);
class BaseController extends \yii\rest\ActiveController
{
   public function init()
   {
       parent::init();       
   }
//   use \api\components\traits\ControllersCommonTrait;
   
   public function actionOptions()
   {
        Yii::$app->getResponse()->getHeaders()->set('Allow', implode(', ', ['OPTIONS', 'POST', 'GET']));
   } 
}

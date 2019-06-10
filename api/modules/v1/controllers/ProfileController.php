<?php

namespace api\modules\v1\controllers;

use api\modules\v1\models\LoginForm;
use RestHelper;
use CHelper;
use Yii;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBasicAuth;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;

class ProfileController extends ActiveController {

    public $modelClass = 'api\modules\v1\models\User';
    
    public function behaviors() {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBasicAuth::className(),
            'except' => [],
        ];
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'except' => ['index'],
            'rules' => [
                [
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ];
        
        return $behaviors;
    }
    
    public function actions() {
        $actions = parent::actions();
        unset($actions['index'], $actions['update'], $actions['create'], $actions['delete'], $actions['view']);
        return $actions;
    }

    
    public function actionIndex() {
        $growers_id = \CHelper::getUserIdentityData('id');

        $modelClass = $this->modelClass;
        $data = $modelClass::find()->andWhere(['=', 'ref_user_id', $growers_id])->all();
        if(!empty($data)){
            $response_fields=array('id','full_name','email','status','user_type','ref_user_id','is_verified','is_block'); 
            $data=  \RestHelper::apiResponseFeildsArr($data,$response_fields);
            return \RestHelper::formatResponseSuccess('2d513df5-16a7-4442-a1b4-2373d5c12967', $data);
        }else{ 
            return \RestHelper::formatResponseError('afd55005-6305-402d-a1b2-049b1c71ae2c', $data);
        }
        
    }
    
    public function actionView($id=NULL) {
        $growers_id = \CHelper::getUserIdentityData('id');

        $modelClass = $this->modelClass;
        $data = $modelClass::find()->Where(['=', 'id', $growers_id])->all();
        if(!empty($data)){
            $response_fields=array('id','full_name','email','status','user_type','ref_user_id','is_verified','is_block'); 
            $data=  \RestHelper::apiResponseFeildsArr($data,$response_fields);
            return \RestHelper::formatResponseSuccess('2d513df5-16a7-4442-a1b4-2373d5c12967', $data);
        }else{ 
            return \RestHelper::formatResponseError('afd55005-6305-402d-a1b2-049b1c71ae2c', $data);
        }
    }
    
    public function actionCreate() {
        $growers_id = \CHelper::getUserIdentityData('id');

        $modelClass = $this->modelClass;
        $data = $modelClass::find()->Where(['=', 'id', $growers_id])->all();

        if(!empty(Yii::$app->request->bodyParams)){
            $data[0]['full_name'] = Yii::$app->request->getBodyParam("full_name");
        }

        if(!empty($data[0]->save())){
            $response_fields=array('id','full_name','email','status','user_type','ref_user_id','is_verified','is_block'); 
            $data=  \RestHelper::apiResponseFeildsArr($data,$response_fields);
            return \RestHelper::formatResponseSuccess('2d513df5-16a7-4442-a1b4-2373d5c12967', $data);
        }else{ 
            return \RestHelper::formatResponseError('afd55005-6305-402d-a1b2-049b1c71ae2c', $data);
        }
     }
    
}
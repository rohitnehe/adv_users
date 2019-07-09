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

class UserController extends ActiveController {

    public $modelClass = 'api\modules\v1\models\User';
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

    public function behaviors() {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBasicAuth::className(),
            'except' => ['login']
        ];
        
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'except' => ['login'],
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

    /**
     * @author Kumar Waghmode <kumarwaghmode@benchmarkitsolutions.com>
     * @date : 24 Nov 2018
     * This Action is Used To Login The user.
     * @return array of data
     */
    public function actionLogin() {       
        $model = new LoginForm();
        $model->scenario = LoginForm::SCENARIO_API_LOGIN;
        
        $email = \Yii::$app->request->getBodyParam('email');

        $password = \Yii::$app->request->getBodyParam('password');

        $data=array();
        if ($model->load(Yii::$app->request->bodyParams,'') && $model->login()) {
            
            $user_id = CHelper::getUserIdentityData('id');                       //get user_id which is Loggged in 
            $user_data = $this->findModel($user_id);  
            $token=Yii::$app->user->identity->access_token;
            $timestamp = (int) substr($token, strrpos($token, '_') + 1);
            $expire = Yii::$app->params['user.apiAccessTokenExpire'];       
                $data = [
                    'access_token' => Yii::$app->user->identity->access_token,
                    'token_valid' => CHelper::getFormatedDatetime( $timestamp + $expire)
                        
                ];
                return RestHelper::formatResponseSuccess('51bb0c65-e3fa-4a60-bb03-239497189b78', $data); //$model;
        } else {  
            return RestHelper::formatResponseError("6fadbfc1-e90f-429f-8095-0d543dff5d7e", $data);//$model->getErrors()['password'][0]
        }
    }

   

   

    /**
     * @author Kumar Waghmode <kumarwaghmode@benchmarkitsolutions.com>
     * @date : 24 Nov 2018
     * @purpose This Function used To Logout The User
     * @return 
     */
    public function actionLogout() {
//        CHelper::addApiRequestLog("Logout");
        $id = \CHelper::getUserIdentityData('id');
        $params = \Yii::$app->request->bodyParams;        
        $data=array();  
        $model = $this->findModel($id);
        if ($model->id) {
            $flag = Yii::$app->user->logout();
            $model->access_token= '';
            $model->save(false);
                        
            return RestHelper::formatResponseSuccess('8587cff0-049a-4ba1-8931-10548050d5c0', $data);
        } else {
            return RestHelper::formatResponseError('a9b0f548-3d63-4c0e-959a-25431b5197f6', $data);
        }
    }

    /**
     * 
     * @param type $id
     * @return type
     * @throws NotFoundHttpException
     */
    protected function findModel($id) {
        $modelClass = $this->modelClass;
        if (($model = $modelClass::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}

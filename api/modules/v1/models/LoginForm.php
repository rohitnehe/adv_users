<?php

namespace api\modules\v1\models;

use Yii;
use api\modules\v1\models\User;
use yii\base\NotSupportedException;
use yii\helpers\Security;
use yii\helpers\BaseArrayHelper;
use common\components\CUtils;

/**
 * Login form
 */
class LoginForm extends User {

    private $_user;
    public $verifyCode;
    public $rememberPassword;
   
    const SCENARIO_API_LOGIN = 'apiLogin';
    
    /**
     * 
     * @return type
     */
    public function rules() {
        return [
            [['email', 'password'], 'required', 'on' => self::SCENARIO_API_LOGIN],
            [['email', 'password'], 'safe'],
            [['email', 'password'], 'string', 'max' => 55],            
        ];
    }
    
    /**
     * Logs in a user using the provided email and password.
     * @return boolean whether the user is logged in successfully
     */
    public function login() {                        
        $user = $this->getUser();
        if (isset($user) && $this->validatePassword($this->password)) {
            if(!empty($user->id) ){
                $user->access_token= User::generateAccessToken();
                $user->save(false);
            }
            \Yii::$app->user->login($user, 3600 * 24);
            $this->setLoginSession($user->getAttributes());                
            return true;
        } else {
            return false;
        }        
        return false;
    }
 
    /**
     * 
     * @return type
     */
    protected function getUser() {
        if ($this->_user === null) {
            $this->_user = User::find()
                    ->andWhere('email = :email', [':email' => $this->email])
                    ->andWhere('status = :status', [':status' => self::STATUS_ACTIVE])
                    ->andWhere('is_block = :is_block', [':is_block' => self::IS_UNBLOCK])
                    ->one();
        }
        return $this->_user;
    }

    /**
     * 
     * @param type $attribute
     * @param type $params
     * @return boolean
     */
    public function validatePassword($password) {
        $user = $this->_user;
        if (!$user || !$user->validatePassword($password)) {
            return false;
        } else {
            return true;
        }
    }
    
    /**
     * @inheritdoc
     */
    public function getId() {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey() {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey) {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * @author Nikhil B. <nihkilbhagunde@benchmarkitsolutions.com>.
     * @purpose : To set user attribute values in login session array.
     * @param type $user
     * @return type
     */
    public static function setLoginSession($user) {
        
        if (isset($user)) {
            Yii::$app->session->open();
            $type = '';
            $user_session[$type]['user_id'] = $user['id'];
            $user_session[$type]['email'] = $user['email'];
            $user_session[$type]['full_name'] = $user['full_name'];
            $user_session[$type]['user_type'] = $user['user_type'];
            $user_session[$type]['ref_user_id'] = $user['ref_user_id'];
            $user_session[$type]['last_login'] = $user['last_logged_in'];
            $user_session[$type]['created_at'] = $user['created_at'];
            if ($type != '')
                Yii::$app->session->set($type, $user_session);
        }
    }

    /**
     * 
     */
    public static function logout() {
        Yii::$app->session->remove($type);
    }

}
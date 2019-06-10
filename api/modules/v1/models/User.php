<?php

namespace api\modules\v1\models;

use api\modules\v1\models\LoginForm;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\web\NotFoundHttpException;
use CHelper;
/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */
class User extends \common\models\User {

    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    const DEFAULT_ROUND_PAGE_NM='screen_terms_conditions';
    const DEFAULT_USER_MENU_NM='Get Started With Round 1';
    
    public $pwd,$old_pwd,$ad_token;
    public $user_beverages,$user_fav_sports;
    public $external_id;
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%users}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE]],
            [['user_name','id','authkey', 'password_hash', 'password_reset_token', 'email', 'status', 'user_type', 'phone_no', 'created_at', 'updated_at','country_code'], 'safe'],
            [['pwd'],'safe'],
            [['user_name','first_name','last_name','email','home_country','user_beverages','user_fav_sports','dob'],'safe'],
            ['email','unique'],
            [['access_token','pwd','old_pwd','nick_name'],'safe'], 
        ];
    }
    
  

   
    /**
     * @author Kumar Waghmode <kumarwaghmode@benchmarkitsolutions.com>
     * @date : 24 Nov 2018
     * @purpose : Used to validate Api user using access token
     * @param type $token
     * @param type $type
     * @return type
     * @throws NotFoundHttpException
     */
    public static function findIdentityByAccessToken($token, $type = null) {
        $data=array();
        $user = static::find()
                        ->where([
                            'access_token' => $token,
                            'status' => self::STATUS_ACTIVE,
                        ])->one();
        
        if (!empty($user)) {
            if(self::isAccessTokenValid($user->access_token)){
                return $user;
            }else{
                throw new NotFoundHttpException('Access token is expired',403);    
            }
        } else {
            throw new NotFoundHttpException('Invalid access token.',403);
        }
    }


  /**
     * @author Kumar Waghmode <kumarwaghmode@benchmarkitsolutions.com>
     * @date : 03 Dec.2018
     * @Description -: Finds out if access token is valid . 
     * @param string $token access token
     * @return boolean
     */
    public static function isAccessTokenValid($token) {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.apiAccessTokenExpire'];
        
        return $timestamp + $expire >= time();
    }
 
    
     /**
     * @author Kumar Waghmode <kumarwaghmode@benchmarkitsolutions.com>
     * @date : 03 Dec 2018
     * <@Description -: generate access token. >     
     * @return boolean
     */
     public static function generateAccessToken() {
        return Yii::$app->security->generateRandomString() . '_' . time();
    }

}

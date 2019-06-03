<?php

namespace common\models;

use Yii;
use common\models\BaseModel;
use yii\db\Query;
/**
 * This is the model class for table "tbl_users".
 *
 * @property int $id
 * @property string $full_name
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $status
 * @property string $user_type SA = Super Admin, A = Admin, GA = Grower Admin, SAL = Sales Rep, CA = Customer Admin,CU= Customer User
 * @property int $last_logged_in Last login date
 * @property string $access_controls It contain serialized array for site access
 * @property int $created_by current logged user id
 * @property int $created_at
 * @property int $updated_by
 * @property int $updated_at
 * @property int $is_deleted
 */
class User extends BaseModel implements \yii\web\IdentityInterface {  
    public $oldemail;
    public $password;
    public $newPassword;
    public $confirmPassword;
    
    public $rememberMe =false;
    
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
        return parent::behaviors();
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['status', 'user_type', 'access_controls'], 'string'],
            [['last_logged_in', 'created_at', 'updated_at', 'is_deleted', 'is_block'], 'integer'],
            [['full_name','growpoint_external_id'], 'string', 'max' => 50],
            [['email'], 'string', 'max' => 60],
            [['auth_key'], 'string', 'max' => 32],
            [['password_hash', 'password_reset_token'], 'string', 'max' => 255],
            [['password_reset_token'], 'unique'],
            [['email'], 'email'],
            [['email'], 'users_type'],
            [['email'], 'unique', 'message' => 'Entered email already exists in the system.'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_NOT_DELETED]],
            [['full_name', 'email'], 'required', 'on' => 'userProfile'],
            [['email'], 'user_exists'],
            [['password', 'newPassword', 'confirmPassword', 'oldemail'], 'safe', 'on' => 'userProfile'],
            [['password'], 'validatePassword', 'on' => 'userProfile'],
            ['full_name', 'match', 'pattern' => "/^['A-Za-z 0-9_ &.-]+$/i"],
            /* Change Password Validations */
            ['newPassword', 'customCheckRequiredPassword', 'on' => 'userProfile'],
            ['password', 'isoldpasswordCorrect', 'on' => 'userProfile'],
            [['password', 'newPassword', 'confirmPassword'], 'string', 'min' => 6, 'max' => 25],
            [['confirmPassword'], 'compare', 'compareAttribute' => 'newPassword', 'message' => "New Password and Confirm Password does not match"],
            [['newPassword','confirmPassword'], 'match', 'pattern' => "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!#^~`()<>,.+-\/\%*?&])[A-Za-z\d@$!#^~`()<>,.+-\/\%*?&]{6,25}$/", 'message' => "Password must contain atleast 6 characters, including both Upper and Lower case, numbers and symbols.", 'on' => 'userProfile'],
            [['newPassword','confirmPassword'], 'match', 'pattern' => "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!#^~`()<>,.+-\/\%*?&])[A-Za-z\d@$!#^~`()<>,.+-\/\%*?&]{6,25}$/", 'message' => "Password must contain atleast 6 characters, including both Upper and Lower case, numbers and symbols.", 'on' => 'resetUserPassword'],
            [['newPassword','confirmPassword'], 'required', 'on' => 'resetUserPassword'],
        ];
    }

    /**
     * <@author Nikhil Bhagunde <nikhilbhagunde@benchmarkitsolutions.com>
     * <@Date -: 24-Sep-2018> 
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'full_name' => 'Name',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'email' => 'Email',
            'status' => 'Status',
            'user_type' => 'User Type',
            'last_logged_in' => 'Last Logged In',
            'access_controls' => 'Access Controls',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
            'is_deleted' => 'Is Deleted',
            'growpoint_external_id' => 'Growpoint External Key',
            'is_block' => 'Is Locked'
        ];
    }

    /**
     * {@inheritdoc}
     * @return UserQuery the active query used by this AR class.
     */
    public static function find() {
        return new UserQuery(get_called_class());
    }

    /**
     * <@author Nikhil Bhagunde <nikhilbhagunde@benchmarkitsolutions.com>
     * <@Date -: 04-Oct-2018> 
     * @param type $username
     * @return type
     */
    public static function findByUsername($username) {
        return static::findOne(['full_name' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * <@author Nikhil Bhagunde <nikhilbhagunde@benchmarkitsolutions.com>
     * <@Date -: 04-Oct-2018>
     * <@Description -: Finds user by password reset token.>
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token) {
//       // commented by Kumar on 20Dec2018 
//       // Pupose : Feedback 73: Link in the email will be active for forever
//        if (!static::isPasswordResetTokenValid($token)) {
//            return null;
//        }

        return static::findOne([
                    'password_reset_token' => $token,
                    'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * <@Date -: 24-Sep-2018>
     * <@Description -: Finds out if password reset token is valid.>
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token) {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * <@Date -: 24-Sep-2018>
     * <@Description -: Validates password.>
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password) {
        
        if(isset($this->password_hash))
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * <@Date -: 24-Sep-2018>
     * <@Description -: Generates password hash from password and sets it to the model.>
     * @param string $password
     */
    public function setPassword($password) {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * <@Date -: 24-Sep-2018> 
     * <@Description -:Generates "remember me" authentication key.>
     */
    public function generateAuthKey() {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * <@Date -: 24 Sep 2018>
     * <@Description -:Generates new password reset token.>
     */
    
    public function generatePasswordResetToken() {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * <@Date -: 24-Sep-2018>
     * <@Description -:Removes password reset token.>
     */
    public function removePasswordResetToken() {
        $this->password_reset_token = null;
    }

    /**
     * <@Date -: 24-Sep-2018> 
     * @return type
     */
    // public function getStates() {
    //     return $this->hasOne(States::className(), ['id' => 'state_id']);
    // }

    /**
     * <@Date -: 24-Sep-2018> 
     * @return type
     */
    public function getStateName() {
        return $this->states->name;
    }

    /**
     * <@Date -: 24-Sep-2018> 
     */
    public function getAuthKey() {
        return $this->auth_key;
    }

    /**
     * <@Date -: 24-Sep-2018> 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * <@Date -: 24-Sep-2018> 
     * @param type $authKey
     */
    public function validateAuthKey($authKey) {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * <@Date -: 24-Sep-2018> 
     * @param type $id
     */
    // public static function findIdentity($id) {
    // }
    /**
     * @inheritdoc
     * loginform method take in user as identity class is now User
     */
    public static function findIdentity($id) {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }
    
    /**
     * <@Date -: 24-Sep-2018>
     * @param type $token
     * @param type $type
     */
    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null) {
        if (!ProxyLogin::isAccessTokenValid($token)) {
            return null;
        }

        $user = static::find()
                ->where([
                    'proxy_access_token' => $token
                ])
                ->one();
        if (!empty($user)) {
            return $user;
        } else {
            throw new NotFoundHttpException('Invalid access token.');
        }
    }

    /**
     * <@author Nikhil B.<nikhilbhagunde@benchmarkitsolutions.com>
     * Date : 24 Sep 2018
     * @param type $email
     * @return type
     */
    public static function findByEmail($email) {
        if (Yii::$app->id == 'app-backend')
            $user_type = \Yii::$app->params['admin.user_types'];

        elseif (Yii::$app->id == 'app-frontend')
            $user_type = \Yii::$app->params['regular.user_types'];

        return static::find()
                        ->andWhere('email = :email', [':email' => $email])
                        ->andWhere('status = :status', [':status' => self::STATUS_ACTIVE])
                        ->andFilterWhere(['in', 'user_type', $user_type])
                        ->one();
    }

    /**
     * <@author Ravina Surve. <ravinasurve@benchmarkitsolutions.com>
     * <@Date -: 24-Sep-2018>
     * @purpose Function to getting states to show dropdown List.     
     * @return type $states
     */
    public static function getStateList() {
        //Getting States to show dropdown list.
        return $states = \yii\helpers\ArrayHelper::map(States::find()->where(['is_deleted' => '0'])->all(), 'id', 'name');
    }

    /**
     * <@author Ravina Surve <ravinasurve@benchmarkitsolutions.com>
     * <@Date -: 24-Sep-2018>
     * @purpose : Validating entered email is exists or not. 
     * @param type $attribute
     * @param type $params    
     */
    public function user_exists($attribute, $params) {
        if (isset($this->id)) {
            //for  updating record
            $user = User::find()
                    ->andWhere(['<>', 'id', $this->id])
                    ->andWhere(['email' => $this->email])
                    ->one();
        } else {
            $user = User::findOne([
                        'email' => $this->email,
            ]);
        }
        if ($user) {
            $this->addError($attribute, 'Entered email already exists in the system.');
        }
    }

    /**
     * <@author Ravina Surve <ravinasurve@benchmarkitsolutions.com>
     * <@Date -: 15-Oct-2018>
     * <@Description -: get UserInfo relation.>
     * @return type
     */
    public function getUserInfo() {
        return $this->hasOne(\common\models\UsersInfo::className(), ['users_id' => 'id']);
    }

    /**
     * <@author Ravina Surve <ravinasurve@benchmarkitsolutions.com>
     * <@Date -: 15-Oct-2018>
     * <@Description -: check old password.>
     * @return type
     */
    public function isoldpasswordCorrect($attribute, $params) {
        if (!password_verify($this->$attribute, \Yii::$app->user->getIdentity()['password_hash'])) {
            $this->addError($attribute, 'You have entered incorrect old Password');
        }
    }

    /**
     * <@author Ravina Surve <ravinasurve@benchmarkitsolutions.com>
     * <@Date -: 16-Oct-2018>
     * <@Description -: if new password entered then old password,confirm password required.>
     * @return type
     */
    public function customCheckRequiredPassword($attribute) {
        if (!empty($this->newPassword) && $this->newPassword != '' && (empty($this->confirmPassword) || empty($this->password))) {
            if (empty($this->password) && empty($this->confirmPassword)){
                $msgOldPassword = 'Old password is required for setting new password';
                $msgConfirmPassword = 'Confirm password is required for setting new password';
                $this->addError('confirmPassword', $msgConfirmPassword);
                $this->addError('password', $msgOldPassword);
              
            }
            elseif (empty($this->confirmPassword)){
                $msg = 'Confirm password is required for setting new password';
                $this->addError('confirmPassword', $msg);
               
            }   
            else{
                $msg = 'Old password is required for setting new password';
                $this->addError('password', $msg);
              
            }
        }
    }

    /**
     * <@author Ravina Surve <ravinasurve@benchmarkitsolutions.com>
     * <@Date -: 16-Oct-2018>
     * <@Description -: get user list of name and id depends on type.>
     * @return type
     */
    public static function getUserList($type) {
        $userModel = User::find()->userType($type)->nondeleted()->active()->orderBy(['full_name' => SORT_ASC])->all();
        return \yii\helpers\ArrayHelper::map($userModel, 'id', 'full_name');
    }
    
    /**
     * <@author Nikhil Bhagunde <nikhilbhagunde@benchmarkitsolutions.com>
     * <@Date -: 22-Oct-2018>
     * <@Description -: fetch, Activity Log Relation.>
     * @return type
     */
    public function getActivityLogs() {
        return $this->hasMany(ActivityLog::className(), ['users_id' => 'id'])->nondeleted();
    }

    /**
     * <@author Bhushan Amane <bhushanamane@benchmarkitsolutions.com>
     * <@Date -: 22-Oct-2018>
     * @purpose : to deactivate child records after deactivate parent 
     * @param type $id
     * @param type $type    
     */
    public static function setDeactivateChilds($id, $type) {
      //for updating record      
        if ($type == "GA" || $type == "CA") {
            $user = User::find()
                    ->where(['=', 'ref_user_id', $id])
                    ->andWhere(['=', 'inactive_after_parent', User::INACTIVE_AFTER_PARENT])
                    ->andWhere(['=', 'status', User::STATUS_ACTIVE])
                    ->all();

            foreach ($user as $models) {
                $user_data = User::findOne(['id' => $models->id]);
                $user_data->inactive_after_parent = User::ACTIVE_AFTER_PARENT;
                $user_data->status = User::STATUS_INACTIVATE;
                $user_data->save(false);
            }
        }
       // \CHelper::debug($model->growerRel);
        if($type == "SAL"){ 
           return true; 
        }
    }

    /**
     * <@author Bhushan Amane <bhushanamane@benchmarkitsolutions.com>
     * <@Date -: 22-Oct-2018>
     * @purpose : to activate child records after activate parent 
     * @param type $id
     * @param type $type    
     */
    public static function setActivateChilds($id, $type) {
        //for  updating record
        if ($type == "GA" || $type == "CA") {
            $user = User::find()
                    ->where(['=', 'ref_user_id', $id])
                    ->andWhere(['=', 'inactive_after_parent', User::ACTIVE_AFTER_PARENT])
                    ->andWhere(['=', 'status', User::STATUS_INACTIVATE])
                    ->all();

            foreach ($user as $models) {
                $user_data = User::findOne(['id' => $models->id]);
                $user_data->inactive_after_parent = User::INACTIVE_AFTER_PARENT;
                $user_data->status = User::STATUS_ACTIVE;
                $user_data->save(false);
            }
        }
        // \CHelper::debug($model->growerRel);
        if($type == "SAL"){ 
                return true; 
        }
    }
    
    /**
     * @author Niranjan Patil <niranjanpatil@benchmarkitsolutions.com>
     * @Date -: 29 Nov 2018
     * @purpose : find user by grow point external id 
     * @param type $id
     * @param type $type    
     */
    public static function findByGrowpoitExternalId($id){
        return self::find()->where(['growpoint_external_id' => $id])->active()->nondeleted()->one();
//       echo $query->createCommand()->rawSql;die;
    }

    public static function findIdentityOfUser($id) {
        return static::findOne($id);
    }
    /**
     * @author Ravina surve <ravinasurve@benchmarkitsolutions.com>
     * @Date -: 9 jan 2019
     * @purpose : get user's user_type,ref_user_id,status   
     */
    public static function getUserStatusAndParent($id) {
        return static::find()->select(['user_type','status','ref_user_id'])->where(['id' => $id,'is_deleted' => self::STATUS_NOT_DELETED])->one();
       
    }
     /**
     * <@author Kumar Waghmode <kumarwaghmode@benchmarkitsolutions.com>
     * <@Date -: 14Jan2019>
     * @purpose : Validating entered growpoint external id  is exists or not. 
     * @param type $attribute
     * @param type $params    
     */
   public function grower_exid_exists($attribute, $params) {
        if (isset($this->id)) {
            //for  updating record
            $user = User::find()
                    ->andWhere(['<>', 'id', $this->id])
                    ->andWhere(['growpoint_external_id' => $this->growpoint_external_id])
                    ->one();
        } else {
            $user = User::findOne([
                        'status' => User::STATUS_ACTIVE,
                        'growpoint_external_id' => $this->growpoint_external_id,
            ]);
        }
        if ($user) {
            $this->addError($attribute, 'Entered Growpoint External Key already exists in the system.');
        }
    }
    /*
     * End
     */
    /**
     * <@author Kumar Waghmode <kumarwaghmode@benchmarkitsolutions.com>
     * <@Date -: 14Jan2019>
     * @purpose : Validating entered license_key  is exists or not. 
     * @param type $attribute
     * @param type $params    
     */
     public function grower_license_exists($attribute, $params) {
        if (isset($this->id)) {
            //for  updating record
            $user = User::find()
                    ->andWhere(['<>', 'id', $this->id])
                    ->andWhere(['license_key' => $this->license_key])
                    ->one();
        } else {
            $user = User::findOne([
                        'status' => User::STATUS_ACTIVE,
                        'license_key' => $this->license_key,
            ]);
        }
        if ($user) {
            $this->addError($attribute, 'Entered License Key already exists in the system.');
        }
    } 
    /*
     * End
     */
    
    
    /**
     * <@author Bhushan Amane <bhushanamane@benchmarkitsolutions.com>
     * <@Date -: 14Jan2019>
     * @purpose : Validating entered license_key  is exists or not. 
     * @param type $attribute
     * @param type $params    
     */
     public function getUserCounts() {
        $query = (new Query())->select(
                        'SUM(case when user_type = "GA" AND status="A" then 1 else 0 end) grower_count,
                SUM(case when user_type = "CA" AND status="A" then 1 else 0 end) customer_count,
                SUM(case when user_type = "SAL" AND status="A" then 1 else 0 end) sales_count'
                )->from('tbl_users');
        $query->join('INNER JOIN','tbl_users_info','tbl_users_info.users_id=tbl_users.id');
        $openOrderData = $query->one();
        return $openOrderData;
    }
    
    
    public static function findSuperadminEmail() 
    {
        $query = User::find()->select('email , full_name')->asArray()->where(['in', 'user_type', 'SA'])->nondeleted()->active()->verified()->all();
        return $query;
    }
    
    /**
     * @author Niranjan Patil <niranjanpatil@benchmarkitsolutions.com>
     * @date : 1 APR 2019
     * @purpose check email exist in user table and return error with user type
     * @return type
     */
    public function users_type($attribute, $params) {
        if (isset($this->id)) {
            //for  updating record
            $user = User::find()
                    ->andWhere(['<>', 'id', $this->id])
                    ->andWhere(['email' => $this->email])
                    ->one();
        } else {
            $user = User::findOne([
                        'status' => User::STATUS_ACTIVE,
                        'email' => $this->email,
            ]);
        }
        if ($user) {
            switch ($user->user_type) {
                case User::USER_TYPE_SUPER_ADMIN:
                    $Entity_Name = "Super Admin";
                    break;
                case User::USER_TYPE_ADMIN:
                    $Entity_Name = "Admin";
                    break;
                case User::USER_SALES_REP:
                    $Entity_Name = "Sales Representative";
                    break;
                case User::USER_TYPE_GROWER_ADMIN:
                    $Entity_Name = "Grower";
                    break;
                case User::USER_TYPE_GROWER_USER:
                    $Entity_Name = "Grower User";
                    break;
                case User::USER_TYPE_CUSTOMER_ADMIN:
                    $Entity_Name = "Customer";
                    break;
                case User::USER_TYPE_CUSTOMER_USER:
                    $Entity_Name = "Customer User";
                    break;
                default:
                    $Entity_Name = "user";
            }
            $this->addError($attribute, 'This email address already used for other ' . $Entity_Name . '.'); //in the system
        }
    }

}

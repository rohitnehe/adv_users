<?php

namespace common\models;

use Yii;
/**
 * This is the model class for table "{{%users_info}}".
 *
 * @property int $id
 * @property int $users_id
 * @property string $business_name
 * @property string $address
 * @property string $city
 * @property int $state_id
 * @property string $postal_code
 * @property int $country_id
 * @property string $phone
 * @property string $fax
 * @property string $website
 * @property string $mobile
 * @property string $logo
 * @property string $logo_text
 * @property string $social_media_page1
 * @property string $social_media_page2
 * @property string $banner1
 * @property string $banner2
 * @property string $comment1
 * @property string $comment2
 * @property int $group1
 * @property int $group2
 * @property int $group3
 * @property int $group4
 * @property int $ags_representative_id
 * @property int $is _show_stock
 * @property int $created_by
 * @property int $created_at
 * @property int $updated_by
 * @property int $updated_at
 * @property int $is_deleted
 */
class UsersInfo extends \common\models\BaseModel
{
    public $file;
    public $file_icon;
    
    const SCENARIO_ADMIN_CREATE = 'create';
    const IS_SHOW_ITEM_CONDITION = 1;
    const IS_SHOW_ITEM_PRICE = 1;
    const IS_SHOW_ITEM_PHOTO = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%users_info}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['address', 'city', 'phone'], 'required','except' => 'noRequired'],
            [['state_id', 'country_id', 'is _show_stock', 'created_at', 'updated_at', 'is_deleted'], 'integer'],
            [['banner1', 'banner2', 'comment1', 'comment2'], 'string','max' => 120],
            [['social_media_page1', 'social_media_page2'], 'string', 'max' => 100],
            [['logo_text'], 'string', 'max' => 50],
            [['business_name'], 'string', 'max' => 50],
            [['city', 'postal_code'], 'string', 'max' => 25],
            [['mobile'], 'string', 'max' => 15],
            [['website', 'logo'], 'string', 'max' => 100],
            [['address','tc_text'], 'string', 'max' => 255],
            [['address'], 'string', 'max' => 100,'on'=>'mxAddress'],
            [['city'], 'string', 'max' => 60],
            [['special_item_caption'], 'string', 'max' => 20],
            [['users_id','business_name','fax', 'website','is_show_item_quantity','is_show_multiple_group','special_item_icon','special_item_caption','special_item_bgcolor',
            'mobile','logo', 'logo_text', 'social_media_page1', 'social_media_page2', 'banner1', 'banner2', 'comment1', 'comment2', 'group1', 'group2',
            'group3', 'group4', 'ags_representative_id','file','$file_icon' ,'is_show_stock', 'created_by', 'created_at', 'updated_by', 'updated_at', 'is_deleted','special_item_bgcolor','ic_text','tc_text','state_id', 'country_id','growpoint_external_id'],'safe'],
            [['logo','file'],'file', 'skipOnEmpty' => true, 'extensions' => 'png,jpeg ,jpg , bmp', 'maxSize' => 1024 * 1024 * 5.1, 'tooBig' => 'Image you tried to upload is too large, please try to upload image upto 5 MB.'],
            [['file_icon'],'file', 'skipOnEmpty' => true, 'extensions' => 'png,jpeg ,jpg , bmp', 'maxSize' => 1024 * 1024 * 2.1, 'tooBig' => 'You can upload image upto 2mb'],
            [['phone'], 'string', 'max' => 25],      
            [['mobile','fax'], 'string', 'max' => 25],      
            [['address', 'city', 'postal_code', 'phone'], 'safe', 'on' => 'noRequired'],
            [['business_name'], 'required', 'on' => 'noRequired'],
            [['business_name'], 'string', 'max' => 50],
            [['state_id','country_id','postal_code'], 'required', 'on' => self::SCENARIO_ADMIN_CREATE],
            [['business_name'], 'filter', 'filter' => 'trim'],
            [['state_id','country_id'], 'required'],
        ];
    }
    

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'users_id' => 'Users ID',
            'business_name' => 'Business Name',
            'address' => 'Address',
            'city' => 'City',
            'state_id' => 'State',
            'postal_code' => 'Postal Code',
            'country_id' => 'Country',
            'phone' => 'Phone #',
            'fax' => 'Fax',
            'website' => 'Website',
            'mobile' => 'Mobile',
            'logo' => 'Logo',
            'ic_text' => 'Item Conditions',
            'tc_text' => 'Order Submission Terms & Conditions',
            'logo_text' => 'Logo Slogan',
            'special_item_bgcolor' => 'Special Item Background Color',
            'social_media_page1' => 'Social Media Details',
            'social_media_page2' => 'Social Media Page2',
            'banner1' => 'Banner1',
            'banner2' => 'Banner2',
            'comment1' => 'Comment1',
            'comment2' => 'Comment2',
            'group1' => 'Group1',
            'group2' => 'Group2',
            'group3' => 'Group3',
            'group4' => 'Group4',
            'ags_representative_id' => 'Ags Representative ID',
            'is _show_stock' => 'Is  Show Stock',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
            'is_deleted' => 'Is Deleted',
        ];
    }

    /**
     * <@Date -: 24-Sep-2018> 
     * @return type
     */
    public function getStates() {
        return $this->hasOne(States::className(), ['id' => 'state_id']);
    }
    /**
     * <@Date -: 16-oct-2018> 
     * @return type
     */
    public function getCountry() {
        return $this->hasOne(Country::className(), ['id' => 'country_id']);
    }
    public function getUser() {
        return $this->hasOne(\common\models\User::className(), ['id' => 'users_id']);
    }
     public function getStateName() {
        return !empty($this->states)?$this->states->name:"";
    }
    
}

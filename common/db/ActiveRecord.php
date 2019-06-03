<?php

namespace common\db;

use yii\db\ActiveRecord as mainActiveRecord;
use yii\behaviors\TimestampBehavior;
use common\behaviors\CreateBehavior;

/**
 * <@author Nikhil Bhagunde <nihkilbhagunde@benchmarkitsolutions.com>
 * <@Date -: 23-Oct-2018>
 */
class ActiveRecord extends mainActiveRecord {

    const EVENT_BEFORE_ACTIVE = 'beforeActive';
    const EVENT_AFTER_ACTIVE = 'afterActive';
    const EVENT_BEFORE_DELETE = 'beforeDelete';
    const EVENT_AFTER_DELETE = 'afterDelete';
    const EVENT_BEFORE_INACTIVE = 'beforeInActive';
    const EVENT_AFTER_INACTIVE = 'afterInActive';
    const STATUS_ACTIVE = 'A';
    const STATUS_INACTIVATE = 'I';
    const STATUS_DELETED = 1;
    
    /**
     * <@Description To automatically fills the specified attributes with current timestamp & current user ID.>
     * @return type
     */
    public function behaviors() {
        parent::behaviors();
        return [
            TimestampBehavior::className(),
            CreateBehavior::className()
        ];
    }

    /**
     * <@Description -: This, function is use to trigger mark status as activated.> 
     * @return type
     */
    public function activate() {
        $this->trigger(self::EVENT_BEFORE_ACTIVE);
        $this->setAttribute('status', self::STATUS_ACTIVE);
        $result = $this->save(false);
        if ($result) {
            $this->trigger(self::EVENT_AFTER_ACTIVE);
        }
        return $result;
    }

    /**
     * <@Description -: This, function is use to trigger mark status as deactivated.> 
     * @return type
     */
    public function deactivate() {
        $this->trigger(self::EVENT_BEFORE_INACTIVE);
        $this->setAttribute('status', self::STATUS_INACTIVATE);
        $result = $this->save(false);
        if ($result) {
            $this->trigger(self::EVENT_AFTER_INACTIVE);
        }
        return $result;
    }
    
    /**
     * <@Description -: This, function is use to trigger mark status as deactivated.> 
     * @return type
     */
    public function isDeleted() {
        $this->trigger(self::EVENT_BEFORE_INACTIVE);
        $this->setAttribute('is_deleted', self::STATUS_DELETED);
        $result = $this->save(false,['is_deleted', 'updated_by', 'updated_at']);
        if ($result) {
            $this->trigger(self::EVENT_AFTER_DELETE);
        }
        return $result;
    }

    /**
     * <@Description -: Genarate unique identity key.>
     * @return type
     */
    public static function generate_uuid() {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0C2f) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0x2Aff), mt_rand(0, 0xffD3), mt_rand(0, 0xff4B)
        );
    }

    /**
     * <@Description -: Create primary key before save record.>
     * @param type $insert
     * @return boolean
     */
    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $class = $this->className();
                $pk = $class::primaryKey();
                $primaryKey = $pk[0];
                $this->{$primaryKey} = self::generate_uuid();
            }
            return true;
        } else {
            return false;
        }
    }

}

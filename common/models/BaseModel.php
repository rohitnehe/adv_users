<?php

namespace common\models;

use Yii;
use common\db\ActiveRecord;

/**
 * <@author Nikhil B. <nikhilbhagunde@benchmarkitsolutions.com>
 * @purpose : To manage common function related to Modal.
 */
class BaseModel extends ActiveRecord {

    const STATUS_NOT_DELETED = 0;
    const STATUS_IS_DELETED = 1;
    const STATUS_ACTIVE = 'A';
    const STATUS_INACTIVATE = 'I';
    const IS_BLOCK = 1;
    const IS_UNBLOCK = 0;
    const IS_VERIFIED = 1;
    const ALL = 'all';
    const INACTIVE_AFTER_PARENT =0;
    const ACTIVE_AFTER_PARENT =1;
    const USER_ROLES = ['SA' => 'Super Admin', 'A' => 'Admin'];
    const USER_TYPE_SUPER_ADMIN = 'SA';
    const USER_TYPE_ADMIN = 'A';
    const USER_SALES_REP = 'SAL';
    const USER_STATUS_BLOCK = '1';
    const USER_STATUS_UNBLOCK = '0';
    const USER_IS_VERIFIED = '1';
    const USER_STATUS_ACTIVE = 'A';
    const USER_STATUS_INACTIVE = 'I';
    const USER_TYPE_GROWER_ADMIN = 'GA';
    const USER_TYPE_CUSTOMER_ADMIN = 'CA';
    const USER_TYPE_GROWER_USER = 'GU';
    const USER_TYPE_CUSTOMER_USER = 'CU';
    const USER_TYPE_SAL_USER = 'SAL';
    const IS_DEFAULT = '0';
    const ORDER_STATUS_OPEN= 'Open';
    const ORDER_STATUS_SUBMITTED= 'Submitted';
    const ORDER_STATUS_CANCELED='Canceled';
    const ORDER_STATUS_REOPEN= 'Reopen';
    const QUOTE_STATUS_OPEN= 'Open';
    const SEARCH_STATUS = ['A' => 'Active', 'I' => 'Inactive'];
    const ORDER_TYPE_ORDER = 'Order';
    const ORDER_TYPE_QUOTE = 'Quote';
    
    /**
     * @date : 24 Sep 2018
     * @purpose : To get status label by there value. 
     * @return string
     */
    public function getStatusLabel() {
        if ($this->status == 'I') {
            return 'Inactive';
        } else {
            return 'Active';
        }
    }
    
    /**
     * @author Niranjan Patil <niranjanpatil@benchmarkitsolutions.com>
     * @purpose get Verification Status by its value.
     * @return string
     * @Date 26 APR 2019
     */
    public function findVerificationStatus(){
        if ($this->is_verified == 0) {
            return 'Pending';
        } else {
            return 'Verified';
        }
    }

}

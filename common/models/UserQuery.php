<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[User]].
 * @see User
 */
class UserQuery extends BaseActiveQuery
{
    /**
     * <@author Ravina Surve <ravinasurve@benchmarkitsolutions.com>
     * 24 Sep 2018
     * @param type $alias
     * @return type
     */
    public function nondeleted($alias = '{{%users}}') {
        return $this->andWhere([$alias . '.is_deleted' => !self::DELETED]);
    }
    /**
     * <@author Ravina Surve <ravinasurve@benchmarkitsolutions.com>
     * 24 Sep 2018
     * @param type $alias
     * @return type
     */
    public function userType($type,$alias = '{{%users}}') {
        return $this->andWhere([$alias . '.user_type' => $type]);
    }
    /**
     * <@author Ravina Surve <ravinasurve@benchmarkitsolutions.com>
     * 24 Sep 2018
     * @param type $alias
     * @return type
     */
    public function active($alias = '{{%users}}') {
        return $this->andWhere([$alias . '.status' => \common\models\User::STATUS_ACTIVE]);
    }
    /**
     * @author Niranjan Patil <niranjanpatil@benchmarkitsolutions.com>
     * @date 03 Jan 2019
     * @param type $alias
     * @return type
     */
    public function blocked($alias = '{{%users}}') {
        return $this->andWhere([$alias . '.is_block' => \common\models\User::USER_STATUS_BLOCK]);
    }
    
    /**
     * @author Chirag Rajpurohit
     * @date 23 Jan 2019
     * @param type $alias
     * @return type
     */
    public function verified($alias = '{{%users}}') {
        return $this->andWhere([$alias . '.is_verified' => \common\models\User::USER_IS_VERIFIED]);
    }
}


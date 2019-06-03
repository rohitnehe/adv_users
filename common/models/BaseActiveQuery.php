<?php

namespace common\models;

/**
 * <@author Nikhil B <nikhilbhagunde@benchmarkitsolutions.com>
 * Date : 24 Sep 2018
 * This is the BaseActiveQuery class.
 *
 */
class BaseActiveQuery extends \yii\db\ActiveQuery {

    const DELETED = 1;
    const NOT_DELETED = 0;

    /**
     * @inheritdoc
     * @return models[]|array
     */
    public function all($db = null) {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return model|array|null
     */
    public function one($db = null) {
        return parent::one($db);
    }
    
    /**
     * <@author Nikhil Bhagunde>
     * @return type
     */
    public function notdeleted()
    {
        //$this->activeStatus();
        return $this->andWhere('[[is_deleted]]='.self::NOT_DELETED);
    }
    
     /**
     * <@author Nikhil Bhagunde>
     * @return type
     */
    public function activeStatus()
    {
        return $this->andWhere('[[status]]='.BaseModel::STATUS_ACTIVE);
    }

}

<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace common\behaviors;

use yii\base\InvalidCallException;
use yii\db\BaseActiveRecord;
use yii\behaviors\AttributeBehavior;
/**
 * Custome Behavior for Automatically assign created by user id and updated by user id
 * CreateBehavior automatically fills the specified attributes with the current Users.
 *
 * To use CreateBehavior, insert the following code to your ActiveRecord class:
 *
 * ```php
 * use common\behaviors\CreateBehavior;
 *
 * public function behaviors()
 * {
 *     return [
 *         CreateBehavior::className(),
 *     ];
 * }
 * ```
 *
 * By default, CreateBehavior will fill the `created_by` and `updated_by` attributes with the current timestamp
 * when the associated AR object is being inserted; it will fill the `updated_at` attribute
 * with the timestamp when the AR object is being updated. The timestamp value is obtained by `time()`.
 *
 * Because attribute values will be set automatically by this behavior, they are usually not user input and should therefore
 * not be validated, i.e. `created_at` and `updated_at` should not appear in the [[\yii\base\Model::rules()|rules()]] method of the model.
 *
 * For the above implementation to work with MySQL database, please declare the columns(`created_at`, `updated_at`) as int(11) for being UNIX timestamp.
 *
 * If your attribute names are different or you want to use a different way of calculating the timestamp,
 * you may configure the [[createdAtAttribute]], [[updatedAtAttribute]] and [[value]] properties like the following:
 *
 * public function behaviors()
 * {
 *     return [
 *         [
 *             'class' => TimestampBehavior::className(),
 *             'createdAtAttribute' => 'created_by',
 *             'updatedAtAttribute' => 'updatedby',
 *             'value' => \Yii->$app->user->id,
 *         ],
 *     ];
 * }
 *
 * @author Anupam Ojha
 * @since 2.0
 */
class CreateBehavior extends AttributeBehavior
{
    /**
     * @var string the attribute that will receive timestamp value
     * Set this property to false if you do not want to record the creation time.
     */
    public $createdAtAttribute = 'created_by';
    /**
     * @var string the attribute that will receive timestamp value.
     * Set this property to false if you do not want to record the update time.
     */
    public $updatedAtAttribute = 'updated_by';
    /**
     * @inheritdoc
     *
     * In case, when the value is `null`, the result of the PHP function [time()](http://php.net/manual/en/function.time.php)
     * will be used as value.
     */
    public $value;


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (empty($this->attributes)) {
            $this->attributes = [
                BaseActiveRecord::EVENT_BEFORE_INSERT => [$this->createdAtAttribute, $this->updatedAtAttribute],
                BaseActiveRecord::EVENT_BEFORE_UPDATE => $this->updatedAtAttribute,
            ];
        }
    }

    /**
     * @inheritdoc
     *
     * In case, when the [[value]] is `null`, the result of the PHP function [time()](http://php.net/manual/en/function.time.php)
     * will be used as value.
     */
    protected function getValue($event)
    {
         // \common\components\CHelper::debug(11);
        if(!\Yii::$app->user->isGuest){
          
            if( !isset(\Yii::$app->user->identity->id) ){
                return 1;
            }
            if ($this->value === null) {
                return \Yii::$app->user->identity->id;
            }
            return parent::getValue($event);
        }
        return 0;
    }

    /**
     * Updates a timestamp attribute to the current timestamp.
     *
     * ```php
     * $model->touch('lastVisit');
     * ```
     * @param string $attribute the name of the attribute to update.
     * @throws InvalidCallException if owner is a new record (since version 2.0.6).
     */
    public function touch($attribute)
    {
        /* @var $owner BaseActiveRecord */
        $owner = $this->owner;
        if ($owner->getIsNewRecord()) {
            throw new InvalidCallException('Updating the timestamp is not possible on a new record.');
        }
        $owner->updateAttributes(array_fill_keys((array) $attribute, $this->getValue(null)));
    }
}

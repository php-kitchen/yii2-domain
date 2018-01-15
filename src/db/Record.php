<?php

namespace PHPKitchen\Domain\db;

use PHPKitchen\DI\Contracts\ContainerAware;
use PHPKitchen\DI\Contracts\ServiceLocatorAware;
use PHPKitchen\DI\Mixins\ContainerAccess;
use PHPKitchen\DI\Mixins\ServiceLocatorAccess;
use PHPKitchen\Domain\contracts;
use PHPKitchen\Domain\contracts\EntityDataSource;
use PHPKitchen\Domain\contracts\LoggerAware;
use PHPKitchen\Domain\mixins\LoggerAccess;
use PHPKitchen\Domain\mixins\StaticSelfAccess;
use yii\db\ActiveRecord;

/**
 * Represents
 *
 * @package PHPKitchen\Domain\base
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class Record extends ActiveRecord implements contracts\Record, ContainerAware, ServiceLocatorAware, LoggerAware, EntityDataSource {
    use LoggerAccess;
    use ServiceLocatorAccess;
    use ContainerAccess;
    use StaticSelfAccess;

    /**
     * @override
     * @inheritdoc
     */
    public static function instantiate($row) {
        return \Yii::$container->create(static::class);
    }

    /**
     * @override
     * @inheritdoc
     * @return RecordQuery the newly created query instance.
     */
    public static function find() {
        return static::getInstance()->createQuery(RecordQuery::class);
    }

    /**
     * @param $class
     *
     * @return RecordQuery
     */
    public function createQuery($class) {
        /**
         * @var RecordQuery $finder
         */
        $finder = $this->getContainer()->create($class, [static::class]);
        $finder->setMainTableName(static::tableName());

        return $finder;
    }

    public function isNew() {
        return $this->isNewRecord;
    }

    public function isNotNew() {
        return !$this->isNewRecord;
    }

    public function canGetProperty($name, $checkVars = true, $checkBehaviors = true) {
        return $this->hasAttribute($name) || parent::canGetProperty($name, $checkVars, $checkBehaviors);
    }

    public function canSetProperty($name, $checkVars = true, $checkBehaviors = true) {
        return $this->hasAttribute($name) || parent::canSetProperty($name, $checkVars, $checkBehaviors);
    }

    /**
     * Saves the current record.
     *
     * This method will call [[insert()]] when [[isNewRecord]] is true, or [[update()]]
     * when [[isNewRecord]] is false.
     *
     * For example, to save a customer record:
     *
     * ```php
     * $customer = new Customer; // or $customer = Customer::findOne($id);
     * $customer->name = $name;
     * $customer->email = $email;
     * $customer->save();
     * ```
     *
     * @param boolean $runValidation whether to perform validation (calling [[validate()]])
     * before saving the record. Defaults to `true`. If the validation fails, the record
     * will not be saved to the database and this method will return `false`.
     * @param array $attributeNames list of attribute names that need to be saved. Defaults to null,
     * meaning all attributes that are loaded from DB will be saved.
     *
     * @return boolean whether the saving succeeded (i.e. no validation errors occurred).
     */
    public function validateAndSave($attributeNames = null) {
        if ($this->getIsNewRecord()) {
            return $this->insert($runValidation = true, $attributeNames);
        } else {
            return $this->update($runValidation = true, $attributeNames) !== false;
        }
    }

    /**
     * Saves the current record.
     *
     * This method will call [[insert()]] when [[isNewRecord]] is true, or [[update()]]
     * when [[isNewRecord]] is false.
     *
     * For example, to save a customer record:
     *
     * ```php
     * $customer = new Customer; // or $customer = Customer::findOne($id);
     * $customer->name = $name;
     * $customer->email = $email;
     * $customer->save();
     * ```
     *
     * @param boolean $runValidation whether to perform validation (calling [[validate()]])
     * before saving the record. Defaults to `true`. If the validation fails, the record
     * will not be saved to the database and this method will return `false`.
     * @param array $attributeNames list of attribute names that need to be saved. Defaults to null,
     * meaning all attributes that are loaded from DB will be saved.
     *
     * @return boolean whether the saving succeeded (i.e. no validation errors occurred).
     */
    public function saveWithoutValidation($attributeNames = null) {
        if ($this->getIsNewRecord()) {
            return $this->insert($runValidation = false, $attributeNames);
        } else {
            return $this->update($runValidation = false, $attributeNames) !== false;
        }
    }

    /**
     * Deletes the table row corresponding to this active record.
     *
     * This method performs the following steps in order:
     *
     * 1. call [[beforeDelete()]]. If the method returns false, it will skip the
     *    rest of the steps;
     * 2. delete the record from the database;
     * 3. call [[afterDelete()]].
     *
     * In the above step 1 and 3, events named [[EVENT_BEFORE_DELETE]] and [[EVENT_AFTER_DELETE]]
     * will be raised by the corresponding methods.
     *
     * @return integer|false the number of rows deleted, or false if the deletion is unsuccessful for some reason.
     * Note that it is possible the number of rows deleted is 0, even though the deletion execution is successful.
     * @throws StaleObjectException if [[optimisticLock|optimistic locking]] is enabled and the data
     * being deleted is outdated.
     * @throws Exception in case delete failed.
     */
    public function deleteRecord() {
        return parent::delete();
    }
}
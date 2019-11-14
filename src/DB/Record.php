<?php

namespace PHPKitchen\Domain\DB;

use PHPKitchen\DI\Contracts\ContainerAware;
use PHPKitchen\DI\Contracts\ServiceLocatorAware;
use PHPKitchen\DI\Mixins\ContainerAccess;
use PHPKitchen\DI\Mixins\ServiceLocatorAccess;
use PHPKitchen\Domain\Contracts;
use PHPKitchen\Domain\Contracts\EntityDataSource;
use PHPKitchen\Domain\Contracts\LoggerAware;
use PHPKitchen\Domain\Mixins\LoggerAccess;
use PHPKitchen\Domain\Mixins\StaticSelfAccess;
use yii\db\ActiveQueryInterface;
use yii\db\ActiveRecord;
use yii\db\AfterSaveEvent;

/**
 * Represents
 *
 * @package PHPKitchen\Domain\DB
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class Record extends ActiveRecord implements Contracts\Record, ContainerAware, ServiceLocatorAware, LoggerAware, EntityDataSource {
    use LoggerAccess;
    use ServiceLocatorAccess;
    use ContainerAccess;
    use StaticSelfAccess;
    /**
     * @var bool flag that record was just inserted
     */
    private $justAdded = false;
    /**
     * @var array attribute values that were changed after inser or update
     */
    private $_changedAttributes = [];
    /**
     * @var EntitiesRepository[] repositories of related entities
     */
    private $relatedRepositories = [];

    public function init() {
        parent::init();

        $this->on(static::EVENT_BEFORE_INSERT, [$this, 'markAsJustAdded']);
        $this->on(static::EVENT_BEFORE_UPDATE, [$this, 'markAsJustUpdated']);
        $this->on(static::EVENT_AFTER_INSERT, [$this, 'initChangedAttributes']);
        $this->on(static::EVENT_AFTER_UPDATE, [$this, 'initChangedAttributes']);
    }

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

    public function setChangedAttributes(array $changedAttributes) {
        $this->_changedAttributes = $changedAttributes;
    }

    public function getChangedAttributes() {
        return $this->_changedAttributes;
    }

    public function getChangedAttribute($name) {
        if ($this->wasAttributeChanged($name)) {
            return $this->_changedAttributes[$name];
        }

        return false;
    }

    public function wasAttributeChanged($name) {
        return (array_key_exists($name, $this->_changedAttributes));
    }

    public function isJustAdded() {
        return $this->justAdded;
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
     * before saving the record. Defaults to `true`. If the validation fails, the record
     * will not be saved to the database and this method will return `false`.
     *
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
     * before saving the record. Defaults to `true`. If the validation fails, the record
     * will not be saved to the database and this method will return `false`.
     *
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

    protected function markAsJustAdded() {
        $this->justAdded = true;
    }

    protected function markAsJustUpdated() {
        $this->justAdded = false;
    }

    protected function initChangedAttributes(AfterSaveEvent $event) {
        $this->setChangedAttributes($event->changedAttributes);
    }

    /**
     * Creates a query instance for `has-one` or `has-many` relation.
     *
     * @param string $class the class name of the related record.
     * @param array $link the primary-foreign key constraint.
     * @param bool $multiple whether this query represents a relation to more than one record.
     *
     * @return ActiveQueryInterface the relational query object.
     */
    protected function createRelationQuery($class, $link, $multiple) {
        $repository = $this->getRelatedRepository($byRecordClass = $class);
        if (!$repository) {
            return parent::createRelationQuery($class, $link, $multiple);
        }

        $query = $this->getQueryFromRepository($repository);

        $query->primaryModel = $this;
        $query->link = $link;
        $query->multiple = $multiple;

        return $query;
    }

    /**
     * @param EntitiesRepository $repository
     *
     * @return ActiveQueryInterface
     */
    protected function getQueryFromRepository($repository) {
        return $repository->find()->getQuery();
    }

    /**
     * @param string $byRecordClass
     *
     * @return false|EntitiesRepository
     */
    protected function getRelatedRepository(string $byRecordClass) {
        return $this->relatedRepositories[$byRecordClass] ?? $this->initRelatedRepository($byRecordClass);
    }

    protected function initRelatedRepository(string $byRecordClass) {
        $repositoryClass = false !== strpos($byRecordClass, 'Record') ? str_replace('Record', 'Repository', $byRecordClass) : null;
        $container = $this->container;
        try {
            $repository = $repositoryClass ? $container->create($repositoryClass) : false;
        } catch (\Exception $e) {
            $repository = false;
        }
        $this->relatedRepositories[$byRecordClass] = $repository;

        return $this->relatedRepositories[$byRecordClass];
    }
}
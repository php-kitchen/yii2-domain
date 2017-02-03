<?php

namespace dekey\domain\db;

use dekey\domain\base\Component;
use dekey\domain\base\ModelEvent;
use dekey\domain\contracts;
use dekey\domain\contracts\DomainEntity;
use dekey\domain\exceptions\UnableToSaveEntityException;
use dekey\domain\mixins\TransactionAccess;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;

/**
 * Represents
 *
 * @package dekey\domain\base
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class Repository extends Component implements contracts\Repository {
    use TransactionAccess;
    public $recordsProviderClassName = ActiveDataProvider::class;
    public $useTransactions = true;
    private $_defaultFinderClass = Finder::class;
    private $_defaultQueryClass = RecordQuery::class;


    public function validateAndSave(DomainEntity $entity, $attributes = null) {
        return $this->saveEntityInternal($entity, $runValidation = true, $attributes);
    }

    public function saveWithoutValidation(DomainEntity $entity, $attributes = null) {
        return $this->saveEntityInternal($entity, $runValidation = false, $attributes);
    }

    protected function saveEntityInternal(DomainEntity $entity, $runValidation, $attributes) {
        if ($this->triggerModelEvent(self::EVENT_BEFORE_SAVE, $entity)) {
            $dataSource = $entity->getDataSource();
            $result = $runValidation ? $dataSource->validateAndSave($attributes) : $dataSource->saveWithoutValidation($attributes);
        } else {
            $result = false;
        }
        if ($result) {
            $this->triggerModelEvent(self::EVENT_AFTER_SAVE, $entity);
        } else {
            $exception = new UnableToSaveEntityException('Failed to save entity ' . get_class($entity));
            $exception->errorsList = $dataSource->getErrors();
            throw $exception;
        }

        return $result;
    }

    public function delete(DomainEntity $entity) {
        if ($this->triggerModelEvent(self::EVENT_BEFORE_DELETE, $entity)) {
            $result = $entity->getDataSource()->deleteRecord();
        } else {
            $result = false;
        }
        if ($result) {
            $this->triggerModelEvent(self::EVENT_AFTER_DELETE, $entity);
        }
        return $result;
    }

    /**
     * This method is called at the beginning of inserting or updating a record.
     * The default implementation will trigger an [[EVENT_BEFORE_INSERT]] event when `$insert` is `true`,
     * or an [[EVENT_BEFORE_UPDATE]] event if `$insert` is `false`.
     * When overriding this method, make sure you call the parent implementation like the following:
     *
     * ```php
     * public function beforeSave($insert)
     * {
     *     if (parent::beforeSave($insert)) {
     *         // ...custom code here...
     *         return true;
     *     } else {
     *         return false;
     *     }
     * }
     * ```
     *
     * @param boolean $insert whether this method called while inserting a record.
     * If `false`, it means the method is called while updating a record.
     * @return boolean whether the insertion or updating should continue.
     * If `false`, the insertion or updating will be cancelled.
     */
    protected function triggerModelEvent($eventName, $entity) {
        /**
         * @var ModelEvent $event
         */
        $event = $this->container->create(ModelEvent::class, [$entity]);
        $this->trigger($eventName, $event);

        return $event->isValid();
    }

    public function validate(DomainEntity $entity) {
        $dataSource = $entity->getDataSource();
        return $dataSource->validate();
    }

    public function createNewEntity() {
        return $this->container->create([
            'class' => $this->getEntityClass(),
            'dataSource' => $this->createRecord(),
        ]);
    }

    private function createRecord() {
        return $this->container->create($this->getRecordClass());
    }

    public function createEntityFromRecord(contracts\Record $record) {
        return $this->container->create([
            'class' => $this->getEntityClass(),
            'dataSource' => $record,
        ]);
    }

    /**
     * @return Finder|RecordQuery
     */
    public function find() {
        return $this->createFinder();
    }

    protected function createFinder() {
        return $this->container->create($this->getFinderClass(), [$query = $this->createQuery(), $repository = $this]);
    }

    public function createQuery() {
        return $this->container->create($this->getQueryClass(), [$recordClass = $this->getRecordClass()]);
    }

    /**
     * @return ActiveDataProvider
     */
    public function createRecordsDataProvider() {
        return $this->container->create([
            'class' => $this->recordsProviderClassName,
            'query' => $this->createQuery(),
        ]);
    }

    //----------------------- GETTERS FOR DYNAMIC PROPERTIES -----------------------//

    protected function getEntityClass() {
        return $this->buildModelElementClassName('Entity');
    }

    protected function getRecordClass() {
        return $this->buildModelElementClassName('Record');
    }

    protected function getFinderClass() {
        return $this->buildModelElementClassName('Finder', $this->getDefaultFinderClass());
    }

    protected function getQueryClass() {
        return $this->buildModelElementClassName('Query', $this->getDefaultQueryClass());
    }

    protected function buildModelElementClassName($modelElement, $defaultClass = null) {
        $selfClassName = static::class;
        $elementClassName = str_replace('Repository', $modelElement, $selfClassName);
        if (!class_exists($elementClassName) && !interface_exists($elementClassName)) {
            if ($defaultClass) {
                $elementClassName = $defaultClass;
            } else {
                throw new InvalidConfigException("{$modelElement} class should be an existing class or interface!");
            }
        }
        return $elementClassName;
    }

    //----------------------- GETTERS/SETTERS -----------------------//

    protected function getDefaultFinderClass() {
        return $this->_defaultFinderClass;
    }

    protected function setDefaultFinderClass($defaultFinderClass) {
        if (!class_exists($defaultFinderClass) && !interface_exists($defaultFinderClass)) {
            throw new InvalidConfigException('Default finder class should be an existing class or interface!');
        }
        $this->_defaultFinderClass = $defaultFinderClass;
    }

    public function getDefaultQueryClass() {
        return $this->_defaultQueryClass;
    }

    public function setDefaultQueryClass($defaultQueryClass) {
        if (!class_exists($defaultQueryClass) && !interface_exists($defaultQueryClass)) {
            throw new InvalidConfigException('Default query class should be an existing class or interface!');
        }
        $this->_defaultQueryClass = $defaultQueryClass;
    }
}
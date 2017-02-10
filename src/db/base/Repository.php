<?php

namespace dekey\domain\db\base;

use dekey\domain;
use dekey\domain\base\Component;
use dekey\domain\contracts;
use dekey\domain\data\EntitiesProvider;
use dekey\domain\mixins\TransactionAccess;
use yii\base\InvalidConfigException;

/**
 * Represents base DB repository.
 *
 * @package dekey\domain\db\base
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
abstract class Repository extends Component implements contracts\Repository {
    use TransactionAccess;
    /**
     * @var bool indicates whether to use DB transaction or not.
     */
    public $useTransactions = true;
    /**
     * @var string entities provider class name. Change it in {@link init()} method if you need
     * custom provider.
     */
    public $entitiesProviderClassName;
    /**
     * @var string class name of an event that being triggered on each important action. Change it in {@link init()} method
     * if you need custom event.
     */
    public $modelEventClassName = domain\base\ModelEvent::class;
    /**
     * @var string records query class name. This class being used if no query specified in morel directory. Change it
     * in {@link init()} method if you need custom default query.
     */
    private $_defaultQueryClass = domain\db\RecordQuery::class;

    /**
     * @return domain\db\Finder|domain\db\RecordQuery
     */
    abstract public function find();

    abstract protected function saveEntityInternal(contracts\DomainEntity $entity, $runValidation, $attributes);

    //----------------------- ENTITY MANIPULATION METHODS -----------------------//

    public function validateAndSave(contracts\DomainEntity $entity, $attributes = null) {
        return $this->useTransactions ? $this->saveEntityUsingTransaction($entity, $runValidation = true, $attributes) : $this->saveEntityInternal($entity, $runValidation = true, $attributes);
    }

    public function saveWithoutValidation(contracts\DomainEntity $entity, $attributes = null) {
        return $this->useTransactions ? $this->saveEntityUsingTransaction($entity, $runValidation = false, $attributes) : $this->saveEntityInternal($entity, $runValidation = false, $attributes);
    }

    protected function saveEntityUsingTransaction(contracts\DomainEntity $entity, $runValidation, $attributes) {
        $this->beginTransaction();
        try {
            $result = $this->saveEntityInternal($entity, $runValidation, $attributes);
            $result ? $this->commitTransaction() : null;
        } catch (\Exception $e) {
            $result = false;
        }
        if (!$result) {
            $this->rollbackTransaction();
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
        $event = $this->container->create($this->modelEventClassName, [$entity]);
        $this->trigger($eventName, $event);

        return $event->isValid();
    }

    /**
     * @return EntitiesProvider an instance of data provider.
     */
    public function getEntitiesProvider() {
        return $this->container->create([
            'class' => $this->entitiesProviderClassName,
            'query' => $this->createQuery(),
            'repository' => $this,
        ]);
    }

    //----------------------- SEARCH METHODS -----------------------//

    /**
     * @param mixed $pk primary key of the entity
     * @return Entity[]
     */
    public function findOneWithPk($pk) {
        return $this->find()->oneWithPk($pk);
    }

    /**
     * @return Entity[]
     */
    public function findAll() {
        return $this->find()->all();
    }

    /**
     * @return Entity[]
     */
    public function each() {
        return $this->find()->each();
    }

    public function createQuery() {
        return $this->container->create($this->getQueryClass(), [$recordClass = $this->getRecordClass()]);
    }

    //----------------------- GETTERS FOR DYNAMIC PROPERTIES -----------------------//

    protected function getEntityClass() {
        return $this->buildModelElementClassName('Entity');
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
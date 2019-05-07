<?php

namespace PHPKitchen\Domain\DB\Base;

use PHPKitchen\Domain;
use PHPKitchen\Domain\Base\Component;
use PHPKitchen\Domain\Contracts;
use PHPKitchen\Domain\Data\EntitiesProvider;
use PHPKitchen\Domain\Mixins\TransactionAccess;
use yii\base\InvalidConfigException;
use yii\db\Exception;

/**
 * Represents base DB repository.
 *
 * GETTERS/SETTERS:
 *
 * @property string $className public alias of the {@link _className}
 * @property string $entityClassName public alias of the {@link _entityClassName}
 * @property string $queryClassName public alias of the {@link _queryClassName}
 * @property string $defaultQueryClassName public alias of the {@link _defaultQueryClassName}
 * @property string $recordClassName public alias of the {@link _recordClassName}
 *
 * @package PHPKitchen\Domain\DB\Base
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
abstract class Repository extends Component implements Contracts\Repository {
    use TransactionAccess;
    /**
     * @var array Stores errors which could occur during save process
     */
    public $errors = [];
    /**
     * @var int indicates whether to throw exception or handle it
     */
    public $throwExceptions = false;
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
    public $modelEventClassName = Domain\Base\ModelEvent::class;
    /**
     * @var string records query class name. This class being used if no query specified in morel directory. Change it
     * in {@link init()} method if you need custom default query.
     */
    private $_defaultQueryClassName = Domain\DB\RecordQuery::class;
    private $_className;
    /**
     * @var string indicates what entity to use. By default equal following template "{model name}Entity" where model name is equal to
     * the repository class name without "Repository" suffix.
     */
    private $_entityClassName;
    /**
     * @var string indicates what records query to use. By default equal following template "{model name}Query" where model name is equal to
     * the repository class name without "Repository" suffix.
     */
    private $_queryClassName;
    /**
     * @var string indicates what record to use. By default equal following template "{model name}Record" where model name is equal to
     * the repository class name without "Repository" suffix.
     */
    private $_recordClassName;

    /**
     * @return Domain\DB\Finder|Domain\DB\RecordQuery
     */
    abstract public function find();

    abstract protected function saveEntityInternal(Contracts\DomainEntity $entity, bool $runValidation, ?array $attributes): bool;

    //region ----------------------- ENTITY MANIPULATION METHODS ------------------------

    public function validateAndSave(Contracts\DomainEntity $entity, ?array $attributes = null) {
        $this->clearErrors();

        return $this->useTransactions ? $this->saveEntityUsingTransaction($entity, $runValidation = true, $attributes) : $this->saveEntityInternal($entity, $runValidation = true, $attributes);
    }

    public function saveWithoutValidation(Contracts\DomainEntity $entity, ?array $attributes = null) {
        $this->clearErrors();

        return $this->useTransactions ? $this->saveEntityUsingTransaction($entity, $runValidation = false, $attributes) : $this->saveEntityInternal($entity, $runValidation = false, $attributes);
    }

    protected function saveEntityUsingTransaction(Contracts\DomainEntity $entity, bool $runValidation, ?array $attributes) {
        $this->beginTransaction();
        $exception = null;
        try {
            $result = $this->saveEntityInternal($entity, $runValidation, $attributes);
            throw new Exception('test');
            $result ? $this->commitTransaction() : null;
        } catch (\Exception $e) {
            $result = false;
            $exception = $e;
            $this->addError($e->getMessage());
        }
        if (!$result) {
            $this->rollbackTransaction();
        }
        if ($exception && $this->throwExceptions) {
            throw $e;
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
     *
     * @return boolean whether the insertion or updating should continue.
     * If `false`, the insertion or updating will be cancelled.
     */
    protected function triggerModelEvent($eventName, $entity) {
        /**
         * @var domain\Base\ModelEvent $event
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
    //endregion-

    //region ----------------------- SEARCH METHODS -------------------------------------

    /**
     * @param mixed $pk primary key of the entity
     *
     * @return Domain\Base\Entity
     */
    public function findOneWithPk($pk) {
        return $this->find()->oneWithPk($pk);
    }

    /**
     * @return Domain\Base\Entity[]
     */
    public function findAll() {
        return $this->find()->all();
    }

    /**
     * @param int $batchSize
     *
     * @return Domain\Base\Entity[]
     */
    public function each($batchSize = 100) {
        return $this->find()->each($batchSize);
    }

    /**
     * @param int $batchSize
     *
     * @return Domain\Base\Entity[][]
     */
    public function getBatchIterator($batchSize = 100) {
        return $this->find()->each($batchSize);
    }

    public function createQuery() {
        return $this->container->create($this->queryClassName, [$recordClass = $this->recordClassName]);
    }
    //endregion

    //region ----------------------- GETTERS/SETTERS ------------------------------------

    /**
     * @return array
     */
    public function getErrors(): array {
        return $this->errors;
    }

    /**
     * @param array $errors
     */
    public function setErrors(array $errors): void {
        $this->errors = $errors;
    }

    /**
     * Adds error to the errors array
     *
     * @param $error
     */
    public function addError($error): void {
        $this->errors[] = $error;
    }

    /**
     * Clears errors
     */
    public function clearErrors(): void {
        $this->setErrors([]);
    }

    public function getDefaultQueryClassName() {
        return $this->_defaultQueryClassName;
    }

    public function setDefaultQueryClassName($defaultQueryClass) {
        if (!class_exists($defaultQueryClass) && !interface_exists($defaultQueryClass)) {
            throw new InvalidConfigException('Default query class should be an existing class or interface!');
        }
        $this->_defaultQueryClassName = $defaultQueryClass;
    }

    public function getClassName() {
        if (null === $this->_className) {
            $this->_className = static::class;
        }

        return $this->_className;
    }

    public function setClassName($className) {
        $this->_className = $className;
    }

    public function getEntityClassName() {
        if (null === $this->_entityClassName) {
            $this->_entityClassName = $this->buildModelElementClassName('Entity');
        }

        return $this->_entityClassName;
    }

    public function setEntityClassName($entityClassName) {
        $this->_entityClassName = $entityClassName;
    }

    public function getQueryClassName() {
        if (null === $this->_queryClassName) {
            $this->_queryClassName = $this->buildModelElementClassName('Query', $this->defaultQueryClassName);
        }

        return $this->_queryClassName;
    }

    public function setQueryClassName($queryClassName) {
        $this->_queryClassName = $queryClassName;
    }

    public function getRecordClassName() {
        if (null === $this->_recordClassName) {
            $this->_recordClassName = $this->buildModelElementClassName('Record');
        }

        return $this->_recordClassName;
    }

    public function setRecordClassName($recordClassName) {
        $this->_recordClassName = $recordClassName;
    }

    protected function buildModelElementClassName($modelElement, $defaultClass = null) {
        $selfClassName = $this->className;
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
    //endregion
}
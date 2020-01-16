<?php

namespace PHPKitchen\Domain\DB;

use PHPKitchen\Domain;
use PHPKitchen\Domain\Contracts;
use PHPKitchen\Domain\Data\EntitiesProvider;
use PHPKitchen\Domain\Exceptions\UnableToSaveEntityException;
use yii\base\InvalidConfigException;

/**
 * Represents entities DB repository.
 *
 * @property string $finderClassName public alias of the {@link _finderClass}
 * @property string $defaultFinderClassName public alias of the {@link _defaultFinderClass}
 *
 * @package PHPKitchen\Domain\base
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class EntitiesRepository extends Base\Repository {
    /**
     * @var string data mapper class name. Required to map data from record to entity. Change it in {@link init()} method
     * if you need custom mapper. But be aware - data mapper is internal class and it is strongly advised to not
     * touch this property.
     */
    public $dataMapperClassName = Domain\Base\DataMapper::class;
    /**
     * @var string indicates what finder to use. By default equal following template "{model name}Finder" where model name is equal to
     * the repository class name without "Repository" suffix.
     */
    protected $_finderClassName;
    /**
     * @var string entities finder class name. This class being used if no finder specified in morel directory. Change it
     * in {@link init()} method if you need custom default finder.
     */
    private $_defaultFinderClassName = Finder::class;

    public function __construct($config = []) {
        $this->entitiesProviderClassName = EntitiesProvider::class;
        parent::__construct($config);
    }

    //region ---------------------- ENTITY MANIPULATION METHODS -------------------

    /**
     * @param Contracts\DomainEntity $entity
     * @param bool $runValidation
     * @param array $attributes
     *
     * @return bool
     * @throws UnableToSaveEntityException
     */
    protected function saveEntityInternal(Contracts\DomainEntity $entity, bool $runValidation, ?array $attributes): bool {
        $isEntityNew = $entity->isNew();
        $dataSource = $entity->getDataMapper()->getDataSource();

        if ($this->triggerModelEvent($isEntityNew ? self::EVENT_BEFORE_ADD : self::EVENT_BEFORE_UPDATE, $entity) && $this->triggerModelEvent(self::EVENT_BEFORE_SAVE, $entity)) {
            $result = $runValidation ? $dataSource->validateAndSave($attributes) : $dataSource->saveWithoutValidation($attributes);
        } else {
            $result = false;
        }
        if ($result) {
            $this->triggerModelEvent($isEntityNew ? self::EVENT_AFTER_ADD : self::EVENT_AFTER_UPDATE, $entity);
            $this->triggerModelEvent(self::EVENT_AFTER_SAVE, $entity);
        } else {
            $exception = new UnableToSaveEntityException('Failed to save entity ' . get_class($entity));
            $exception->errorsList = $dataSource->getErrors();
            throw $exception;
        }

        return $result;
    }

    public function delete(Contracts\DomainEntity $entity): bool {
        if ($this->triggerModelEvent(self::EVENT_BEFORE_DELETE, $entity)) {
            $result = $entity->getDataMapper()->getDataSource()->deleteRecord();
        } else {
            $result = false;
        }
        if ($result) {
            $this->triggerModelEvent(self::EVENT_AFTER_DELETE, $entity);
        }

        return $result;
    }

    public function validate(Contracts\DomainEntity $entity): bool {
        $dataSource = $entity->getDataMapper()->getDataSource();

        return $dataSource->validate();
    }

    public function refresh(Contracts\DomainEntity $entity): bool {
        return $entity->getDataMapper()->refresh();
    }
    //endregion

    //region ----------------------- ENTITY DATA METHODS --------------------------
    public function isNewOrJustAdded(Contracts\DomainEntity $entity): bool {
        return $entity->isNew() || $this->isJustAdded($entity);
    }

    public function isJustUpdated(Contracts\DomainEntity $entity): bool {
        return !$this->isJustAdded($entity);
    }

    public function isJustAdded(Contracts\DomainEntity $entity): bool {
        $dataSource = $entity->getDataMapper()->getDataSource();

        return $dataSource->isJustAdded();
    }

    public function getDirtyAttributes(Contracts\DomainEntity $entity, array $names = null): array {
        $dataSource = $entity->getDataMapper()->getDataSource();

        return $dataSource->getDirtyAttributes($names);
    }

    public function getOldAttributes(Contracts\DomainEntity $entity): array {
        $dataSource = $entity->getDataMapper()->getDataSource();

        return $dataSource->getOldAttributes();
    }

    public function getOldAttribute(Contracts\DomainEntity $entity, string $name) {
        $dataSource = $entity->getDataMapper()->getDataSource();

        return $dataSource->getOldAttribute($name);
    }

    public function isAttributeChanged(Contracts\DomainEntity $entity, string $name, bool $identical = true): bool {
        $dataSource = $entity->getDataMapper()->getDataSource();

        return $dataSource->isAttributeChanged($name, $identical);
    }

    public function setChangedAttributes(Contracts\DomainEntity $entity, array $changedAttributes): void {
        $dataSource = $entity->getDataMapper()->getDataSource();

        $dataSource->setChangedAttributes($changedAttributes);
    }

    public function getChangedAttributes(Contracts\DomainEntity $entity): array {
        $dataSource = $entity->getDataMapper()->getDataSource();

        return $dataSource->getChangedAttributes();
    }

    public function getChangedAttribute(Contracts\DomainEntity $entity, string $name) {
        $dataSource = $entity->getDataMapper()->getDataSource();

        return $dataSource->getChangedAttribute($name);
    }

    /**
     * Method returns the result of checking whether the attribute was changed during
     * the saving of the entity.
     * Be aware! False positive possible because of Yii BaseActiveRecord::getDirtyAttributes()
     * method compares values with type matching
     *
     * @param Contracts\DomainEntity $entity
     * @param string $name
     *
     * @return bool
     */
    public function wasAttributeChanged(Contracts\DomainEntity $entity, string $name): bool {
        $dataSource = $entity->getDataMapper()->getDataSource();

        return $dataSource->wasAttributeChanged($name);
    }

    /**
     * Method returns the result of checking whether the attribute value was changed during
     * the saving of the entity.
     * Be aware! This method compare old value with new without type comparison.
     *
     * @param Contracts\DomainEntity $entity
     * @param string $name
     *
     * @return bool
     */
    public function wasAttributeValueChanged(Contracts\DomainEntity $entity, string $name): bool {
        return $this->getChangedAttribute($entity, $name) != $entity->{$name};
    }
    //endregion

    //region ----------------------- INSTANTIATION METHODS ------------------------
    public function createNewEntity() {
        $container = $this->container;

        return $container->create([
            'class' => $this->entityClassName,
            'dataMapper' => $container->create($this->dataMapperClassName, [$this->createRecord()]),
        ]);
    }

    private function createRecord() {
        return $this->container->create($this->recordClassName);
    }

    public function createEntityFromSource(Contracts\EntityDataSource $record) {
        $container = $this->container;

        return $container->create([
            'class' => $this->entityClassName,
            'dataMapper' => $container->create($this->dataMapperClassName, [$record]),
        ]);
    }
    //endregion

    //region ----------------------- SEARCH METHODS -------------------------------
    /**
     * @return Finder|RecordQuery
     */
    public function find() {
        return $this->createFinder();
    }

    protected function createFinder() {
        return $this->container->create($this->finderClassName, [
            $query = $this->createQuery(),
            $repository = $this,
        ]);
    }
    //endregion

    //region ----------------------- GETTERS/SETTERS ------------------------------
    protected function getFinderClassName() {
        if (null === $this->_finderClassName) {
            $this->_finderClassName = $this->buildModelElementClassName('Finder', $this->defaultFinderClassName);
        }

        return $this->_finderClassName;
    }

    public function setFinderClassName($finderClassName): void {
        $this->_finderClassName = $finderClassName;
    }

    public function getDefaultFinderClassName(): string {
        return $this->_defaultFinderClassName;
    }

    public function setDefaultFinderClassName($defaultFinderClass): void {
        if (!class_exists($defaultFinderClass) && !interface_exists($defaultFinderClass)) {
            throw new InvalidConfigException('Default finder class should be an existing class or interface!');
        }
        $this->_defaultFinderClassName = $defaultFinderClass;
    }
    //endregion
}
<?php

namespace PHPKitchen\Domain\Web\Base;

use PHPKitchen\DI\Contracts\ContainerAware;
use PHPKitchen\DI\Contracts\ServiceLocatorAware;
use PHPKitchen\DI\Mixins\ContainerAccess;
use PHPKitchen\DI\Mixins\ServiceLocatorAccess;
use PHPKitchen\Domain\Contracts\DomainEntity;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Represents base view model.
 *
 * @property mixed $id
 * @property \PHPKitchen\Domain\Contracts\EntityController|\yii\web\Controller $controller
 * @property \PHPKitchen\Domain\DB\EntitiesRepository $repository
 * @property \PHPKitchen\Domain\Data\EntitiesProvider $dataProvider
 * @property \PHPKitchen\Domain\Base\Entity $entity
 *
 * @package PHPKitchen\Domain\Web\Base
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class ViewModel extends Model implements ContainerAware, ServiceLocatorAware {
    use ContainerAccess;
    use ServiceLocatorAccess;
    /**
     * @var \PHPKitchen\Domain\Base\Entity
     */
    private $_entity;
    /**
     * @var array Defines map of entity attributes required in {@link convertAttributesToEntityAttributes()}
     * Format of map:
     * <pre>
     * [
     *      'entityAttribute' => 'formAttributeName',
     *      'entityAttribute' => any value (for example digits, objects, array) except string equals to name of the form attributes,
     *      'entityAttribute' => callable // callable will be executed and result will be set as entity attribute
     * ]
     * </pre>
     */
    private $_entityAttributesMap;
    /**
     * @var \PHPKitchen\Domain\Contracts\EntityController|\yii\web\Controller
     */
    private $_controller;

    public function convertToEntity() {
        $defaultAttributes = $this->prepareDefaultEntityAttributes();
        $newAttributes = $this->convertToEntityAttributes();
        $entity = $this->getEntity();
        $entity->load(ArrayHelper::merge($defaultAttributes, $newAttributes));

        return $entity;
    }

    /**
     * Override to set default entity attributes.
     *
     * @return array default entity attributes
     */
    protected function prepareDefaultEntityAttributes() {
        return [];
    }

    /**
     * Converts form to entity attributes.
     *
     * @return array entity attributes.
     */
    public function convertToEntityAttributes() {
        $entityAttributesMap = $this->getEntityAttributesMap();
        if (empty($entityAttributesMap)) {
            return $this->getAttributes();
        }
        $attributes = [];
        foreach ($entityAttributesMap as $entityAttribute => $formValue) {
            if (is_string($formValue) && $this->canGetProperty($formValue)) {
                $attributeValue = $this->$formValue;
            } elseif (is_callable($formValue)) {
                $attributeValue = call_user_func($formValue);
            } else {
                $attributeValue = $formValue;
            }
            $attributes[$entityAttribute] = $attributeValue;
        }

        return $attributes;
    }

    /**
     * Populates the form by entity data.
     *
     * @return bool
     */
    public function loadAttributesFromEntity() {
        $attributes = $this->convertEntityToSelfAttributes();

        return $this->load($attributes, '');
    }

    /**
     * Converts AR attributes to form attributes.
     *
     * @return array
     */
    protected function convertEntityToSelfAttributes() {
        $entity = $this->getEntity();
        $attributes = [];
        foreach ($this->getEntityAttributesMap() as $modelAttribute => $formValue) {
            if (is_string($formValue) && $this->canGetProperty($formValue) && $entity->canGetProperty($modelAttribute)) {
                $attributes[$formValue] = $entity->$modelAttribute;
            }
        }

        return $attributes;
    }

    protected function getEntityAttributesMap() {
        if (null === $this->_entityAttributesMap) {
            $selfAttributeNames = $this->attributes();
            $this->_entityAttributesMap = array_combine($selfAttributeNames, $selfAttributeNames);
        }

        return $this->_entityAttributesMap;
    }

    public function setEntityAttributesMap(array $entityAttributesMap) {
        $this->_entityAttributesMap = $entityAttributesMap;
    }

    //region -------------------- GETTERS/SETTERS --------------------

    public function getEntity() {
        return $this->_entity;
    }

    public function setEntity(DomainEntity $entity) {
        $this->_entity = $entity;
    }

    public function getId() {
        return $this->getEntity()->id;
    }

    public function getController() {
        return $this->_controller;
    }

    public function setController($controller) {
        $this->_controller = $controller;
    }

    public function getRepository() {
        return $this->controller->repository;
    }
    //endregion
}
<?php

namespace PHPKitchen\Domain\Web\Mixins;

use yii\base\InvalidConfigException;
use yii\web\NotFoundHttpException;

/**
 * Represents
 *
 * @property \yii\web\Controller $controller
 * @property string $id
 * @property \PHPKitchen\Domain\DB\EntitiesRepository $repository
 * @property callable $searchBy
 *
 * @mixin \PHPKitchen\DI\Mixins\ServiceLocatorAccess
 * @mixin \PHPKitchen\DI\Mixins\ContainerAccess
 * @mixin ViewModelManagement
 *
 * @package PHPKitchen\Domain\Web\Mixins
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
trait ModelSearching {
    /**
     * @var callable a PHP callable that will be called to return the model corresponding
     * to the specified primary key value. If not set, [[findModel()]] will be used instead.
     * The signature of the callable should be:
     *
     * ```php
     * function ($id, $action) {
     *     // $id is the primary key value. If composite primary key, the key values
     *     // will be separated by comma.
     *     // $action is the action object currently running
     * }
     * ```
     *
     * The callable should return the model found, or throw an exception if not found.
     */
    protected $_searchBy;

    /**
     * Returns the data model based on the primary key given.
     * If the data model is not found, a 404 HTTP exception will be raised.
     *
     * @param string $entityPrimaryKey the ID of the model to be loaded. If the model has a composite primary key,
     * the ID must be a string of the primary key values separated by commas.
     * The order of the primary key values should follow that returned by the `primaryKey()` method
     * of the model.
     *
     * @return mixed
     * @throws InvalidConfigException on invalid configuration
     *
     * @throws NotFoundHttpException if the model cannot be found
     * @deprecated use {@link findEntityByIdentifierOrFail} instead
     */
    protected function findModelByPk($entityPrimaryKey) {
        if ($this->searchBy !== null) {
            $model = call_user_func($this->searchBy, $entityPrimaryKey, $this);
        } elseif ($this->controller->hasMethod('findEntityByPk')) {
            //@deprecated use repositories or callback
            $entity = $this->controller->findEntityByPk($entityPrimaryKey);
            $model = $this->createViewModel($entity);
        } elseif ($this->repository) {
            $model = $this->findEntityByPK($entityPrimaryKey);
        } else {
            throw new InvalidConfigException('Either "' . static::class . '::modelSearchCallback" must be set or controller must declare method "findEntityByPk()".');
        }

        return $model;
    }

    /**
     * Returns the data model based on the primary key given.
     * If the data model is not found, a 404 HTTP exception will be raised.
     *
     * @param string $identifier the ID of the model to be loaded. If the model has a composite primary key,
     * the ID must be a string of the primary key values separated by commas.
     * The order of the primary key values should follow that returned by the `primaryKey()` method
     * of the model.
     *
     * @return mixed
     * @throws InvalidConfigException on invalid configuration
     * @throws NotFoundHttpException on invalid configuration
     */
    protected function findEntityByIdentifierOrFail($identifier) {
        if ($this->searchBy !== null) {
            $entity = call_user_func($this->searchBy, $identifier, $this);
        } elseif ($this->repository) {
            $entity = $this->findEntityByPK($identifier);
        } else {
            throw new InvalidConfigException('Either "' . static::class . '::searchBy" or "' . static::class . '::repository" must be set.');
        }

        if (!$entity) {
            throw new NotFoundHttpException('Entity doest not exist');
        }

        return $entity;
    }

    protected function findEntityByPK($primaryKey) {
        return $this->repository->find()->oneWithPk($primaryKey);
    }

    public function getSearchBy() {
        return $this->_searchBy;
    }

    public function setSearchBy(callable $filter) {
        $this->_searchBy = $filter;
    }

    /**
     * @deprecated use {@link getSearchBy}
     */
    public function getModelSearchCallback() {
        return $this->getSearchBy();
    }

    /**
     * @deprecated use {@link setSearchBy}
     */
    public function setModelSearchCallback(callable $findModelByPk) {
        $this->setSearchBy($findModelByPk);
    }
}
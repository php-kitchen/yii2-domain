<?php

namespace dekey\domain\web\mixins;

use yii\base\InvalidConfigException;
use yii\web\NotFoundHttpException;

/**
 * Represents
 *
 * @property \yii\web\Controller $controller
 * @property string $id
 *
 * @mixin \dekey\di\mixins\ServiceLocatorAccess
 * @mixin \dekey\di\mixins\ContainerAccess
 * @mixin ViewModelManagement
 * @package dekey\domain\web\mixins
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
    protected $_modelSearchCallback;

    /**
     * Returns the data model based on the primary key given.
     * If the data model is not found, a 404 HTTP exception will be raised.
     *
     * @param string $id the ID of the model to be loaded. If the model has a composite primary key,
     * the ID must be a string of the primary key values separated by commas.
     * The order of the primary key values should follow that returned by the `primaryKey()` method
     * of the model.
     *
     * @param string $entityPrimaryKey the ID of the model to be loaded. If the model has a composite primary key,
     * the ID must be a string of the primary key values separated by commas.
     * The order of the primary key values should follow that returned by the `primaryKey()` method
     * of the model.
     *
     * @throws NotFoundHttpException if the model cannot be found
     * @throws InvalidConfigException on invalid configuration
     *
     * @return mixed
     */
    protected function findModelByPk($entityPrimaryKey) {
        if ($this->getModelSearchCallback() !== null) {
            $model = call_user_func($this->getModelSearchCallback(), $entityPrimaryKey, $this);
        } elseif ($this->controller->hasMethod('findEntityByPk')) {
            $entity = $this->controller->findEntityByPk($entityPrimaryKey);
            $model = $this->createViewModel($entity);
        } else {
            throw new InvalidConfigException('Either "' . static::class . '::modelSearchCallback" must be set or controller must declare method "findEntityByPk()".');
        }

        return $model;
    }

    public function getModelSearchCallback() {
        return $this->_modelSearchCallback;
    }

    public function setModelSearchCallback(callable $findModelByPk) {
        $this->_modelSearchCallback = $findModelByPk;
    }
}
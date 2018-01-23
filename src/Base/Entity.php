<?php

namespace PHPKitchen\Domain\Base;

use PHPKitchen\Domain\Contracts\DomainEntity;
use yii\base\Arrayable;
use yii\base\ArrayableTrait;
use yii\base\ArrayAccessTrait;

/**
 * Implements domain entity.
 *
 * @property DataMapper $dataMapper
 *
 * @package PHPKitchen\Domain
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class Entity extends Component implements DomainEntity, \IteratorAggregate, \ArrayAccess, Arrayable {
    use ArrayableTrait;
    use ArrayAccessTrait;
    /**
     * @var \PHPKitchen\Domain\Base\DataMapper
     */
    private $_dataMapper;

    public function getId() {
        return $this->dataMapper->primaryKey;
    }

    /**
     * Populates the {@lint _dataSource} with input data.
     *
     * This method provides a convenient shortcut for:
     *
     * ```php
     * if (isset($_POST['FormName'])) {
     *     $model->attributes = $_POST['FormName'];
     *     if ($model->save()) {
     *         // handle success
     *     }
     * }
     * ```
     *
     * which, with `load()` can be written as:
     *
     * ```php
     * if ($model->load($_POST) && $model->save()) {
     *     // handle success
     * }
     * ```
     *
     * `load()` gets the `'FormName'` from the model's [[formName()]] method (which you may override), unless the
     * `$formName` parameter is given. If the form name is empty, `load()` populates the model with the whole of `$data`,
     * instead of `$data['FormName']`.
     *
     * Note, that the data being populated is subject to the safety check by [[setAttributes()]].
     *
     * @param array $data the data array to load, typically `$_POST` or `$_GET`.
     * @param string $formName the form name to use to load the data into the model.
     * If not set, [[formName()]] is used.
     *
     * @return boolean whether `load()` found the expected form in `$data`.
     */
    public function load($data) {
        return $this->dataMapper->load($this->convertDataToSourceAttributes($data));
    }

    /**
     * Converts data passed to {@link load()} into {@link _dataSource} attributes.
     * Override this method to implement specific logic for your entity.
     *
     * @param mixed $data traversable data of {@link _dataSource}.
     *
     * @return mixed converted data. By default returns the same data as passed.
     */
    protected function convertDataToSourceAttributes(&$data) {
        return $data;
    }

    public function isNew() {
        return $this->dataMapper->isRecordNew();
    }

    public function isNotNew() {
        return !$this->dataMapper->isRecordNew();
    }

    public function hasAttribute($name) {
        return $this->dataMapper->canGet($name);
    }

    public function getAttribute($name) {
        return $this->dataMapper->get($name);
    }

    // --------------  MAGIC ACCESS TO DATA SOURCE ATTRIBUTES --------------

    public function __get($name) {
        try {
            $result = parent::__get($name);
        } catch (\Exception $e) {
            $dataMapper = $this->getDataMapper();
            if ($dataMapper && $dataMapper->canGet($name)) {
                $result = $dataMapper->get($name);
            } else {
                throw $e;
            }
        }

        return $result;
    }

    public function __set($name, $value) {
        try {
            parent::__set($name, $value);
        } catch (\Exception $e) {
            $dataMapper = $this->getDataMapper();
            if ($dataMapper && $dataMapper->canSet($name)) {
                $dataMapper->set($name, $value);
            } else {
                throw $e;
            }
        }
    }

    public function __isset($name) {
        $dataMapper = $this->getDataMapper();

        return parent::__isset($name) || $dataMapper && $dataMapper->isPropertySet($name);
    }

    public function __unset($name) {
        try {
            parent::__unset($name);
        } catch (\Exception $e) {
            $dataMapper = $this->getDataMapper();
            if ($dataMapper && $dataMapper->isPropertySet($name)) {
                $dataMapper->unSetProperty($name);
            } else {
                throw $e;
            }
        }
    }

    public function hasProperty($name, $checkVars = true, $checkBehaviors = true) {
        $result = parent::hasProperty($name, $checkVars, $checkBehaviors);
        if (!$result) {
            $dataMapper = $this->getDataMapper();
            $result = $dataMapper && ($dataMapper->canGet($name) || $dataMapper->canSet($name));
        }

        return $result;
    }

    public function canGetProperty($name, $checkVars = true, $checkBehaviors = true) {
        $result = parent::canGetProperty($name, $checkVars, $checkBehaviors);
        if (!$result) {
            $dataMapper = $this->getDataMapper();
            $result = $dataMapper && $dataMapper->canGet($name);
        }

        return $result;
    }

    public function canSetProperty($name, $checkVars = true, $checkBehaviors = true) {
        $result = parent::canSetProperty($name, $checkVars, $checkBehaviors);
        if (!$result) {
            $dataMapper = $this->getDataMapper();
            $result = $dataMapper && $dataMapper->canSet($name);
        }

        return $result;
    }

    // -------------- GETTERS/SETTERS --------------

    public function getDataMapper() {
        return $this->_dataMapper;
    }

    public function setDataMapper(DataMapper $source) {
        $this->_dataMapper = $source;
    }

    protected function getData() {
        return $this->dataMapper->getAttributes();
    }
}
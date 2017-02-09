<?php

namespace dekey\domain\base;

/**
 * Represents
 *
 * @package dekey\domain\base
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class DataMapper extends Component {
    /**
     * @var \dekey\domain\db\Record
     */
    protected $dataSource;

    /**
     * DataMapper constructor.
     */
    public function __construct($dataSource, $config = []) {
        $this->dataSource = $dataSource;
        parent::__construct($config);
    }

    public function canGet($name) {
        $dataSource = $this->dataSource;
        return $dataSource->canGetProperty($name);
    }

    public function canSet($name) {
        $dataSource = $this->dataSource;
        return $dataSource->canSetProperty($name);
    }

    public function isPropertySet($name) {
        return $this->canGetProperty($name) && isset($this->dataSource->$name);
    }

    public function getDataSource() {
        return $this->dataSource;
    }

    public function get($name) {
        return $this->canGet($name) ? $this->dataSource->$name : null;
    }

    public function set($name, $value) {
        return $this->canSet($name) ? $this->dataSource->$name = $value : null;
    }

    public function unSetProperty($name) {
        if ($this->isPropertySet($name)) {
            unset($this->dataSource->$name);
        }
    }

    public function isRecordNew() {
        return $this->dataSource->isNew();
    }

    public function getPrimaryKey() {
        return $this->dataSource->primaryKey;
    }

    public function load($data) {
        return $this->dataSource->load($data, '');
    }

    public function getAttributes() {
        return $this->dataSource->attributes;
    }
}
<?php

namespace PHPKitchen\Domain\Base;

use PHPKitchen\Domain\Contracts\Record;

/**
 * Represents
 *
 * @property mixed $primaryKey
 *
 * @package PHPKitchen\Domain\base
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class DataMapper extends Component {
    /**
     * @var \PHPKitchen\Domain\DB\Record
     */
    protected $dataSource;
    protected $relatedEntities;

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
        if (isset($this->relatedEntities[$name])) {
            $property = $this->relatedEntities[$name];
        } else {
            $property = $this->getPropertyFromDataSource($name);
        }

        return $property;
    }

    protected function getPropertyFromDataSource($propertyName) {
        $property = $this->canGet($propertyName) ? $this->dataSource->$propertyName : null;

        if ($property instanceof Record && ($repository = $this->findRepositoryForRecord($property))) {
            $property = $repository->createEntityFromSource($property);
            $this->relatedEntities[$propertyName] = $property;
        } elseif ($this->propertyIsAnArrayOfRecords($property)) {
            $repository = $this->findRepositoryForRecord($property[0]);
            if ($repository) {
                $entities = [];
                foreach ($property as $key => $item) {
                    $entities[$key] = $repository->createEntityFromSource($item);
                }
                $property = &$entities;
                $this->relatedEntities[$propertyName] = &$entities;
            }
        }
        return $property;
    }

    protected function propertyIsAnArrayOfRecords($property) {
        return is_array($property) && isset($property[0]) && ($property[0] instanceof Record) && $this->arrayHasOnlyRecords($property);
    }

    protected function arrayHasOnlyRecords(&$array) {
        return array_reduce(
            $array,
            function($result, $element) {
                return ($element instanceof Record);
            }
        );
    }

    /**
     * @param $record
     * @return null|\PHPKitchen\Domain\DB\EntitiesRepository
     */
    protected function findRepositoryForRecord($record) {
        $recordClass = get_class($record);
        $repositoryClass = strstr($recordClass, 'Record') ? str_replace('Record', 'Repository', $recordClass) : null;
        $container = $this->container;
        try {
            $repository = $repositoryClass ? $container->create($repositoryClass) : null;
        } catch (\Exception $e) {
            $repository = null;
        }
        return $repository;
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
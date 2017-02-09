<?php

namespace dekey\domain\db;

use dekey\domain\base\MagicObject;
use dekey\domain\contracts;

/**
 * Represents
 *
 * @package dekey\domain\db
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class Finder extends MagicObject {
    /**
     * @var RecordQuery
     */
    private $_query;
    /**
     * @var EntitiesRepository
     */
    private $_repository;

    public function __construct(contracts\Specification $query, contracts\Repository $repository, $config = []) {
        $this->_query = $query;
        $this->_repository = $repository;
        parent::__construct($config);
    }

    /**
     * @return RecordQuery
     */
    public function asArray() {
        return $this->getQuery()->asArray();
    }

    public function all() {
        $queryResult = $this->getQuery()->all();
        $entities = [];
        foreach ($queryResult as $record) {
            $entities[] = $this->createEntityFromRecord($record);
        }
        return $entities;
    }

    public function one() {
        $queryResult = $this->getQuery()->one();

        return $this->createEntityFromRecord($queryResult);
    }

    public function oneWithPk($pk) {
        $queryResult = $this->getQuery()->oneWithPk($pk);

        return $this->createEntityFromRecord($queryResult);
    }

    public function batch($batchSize = 100) {
        $iterator = $this->getQuery()->batch($batchSize);
        return $this->container->create(SearchResult::class, [$iterator, $this->getRepository()]);
    }

    public function each() {
        $iterator = $this->getQuery()->each();
        return $this->container->create(SearchResult::class, [$iterator, $this->getRepository()]);
    }

    protected function createEntityFromRecord($record) {
        if ($record instanceof contracts\Record) {
            $entity = $this->getRepository()->createEntityFromRecord($record);
        } else {
            $entity = $record;
        }
        return $entity;
    }

    public function __call($name, $params) {
        $query = $this->getQuery();
        if ($query->hasMethod($name)) {
            call_user_func_array([$query, $name], $params);
            $result = $this;
        } else {
            $result = parent::__call($name, $params);
        }
        return $result;
    }

    public function getQuery() {
        return $this->_query;
    }

    protected function getRepository() {
        return $this->_repository;
    }
}
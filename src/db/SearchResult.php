<?php

namespace dekey\domain\db;

use dekey\domain\base\MagicObject;
use dekey\domain\contracts;
use yii\db\BatchQueryResult;

/**
 * Represents
 *
 * @package dekey\domain\db
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class SearchResult extends MagicObject implements \Iterator {
    private $_queryResultIterator;
    /**
     * @var contracts\Repository
     */
    private $_repository;

    public function __construct(BatchQueryResult $queryResult, contracts\Repository $repository, $config = []) {
        $this->_queryResultIterator = $queryResult;
        $this->_repository = $queryResult;
        parent::__construct($config);
    }

    public function current() {
        $iterator = $this->getQueryResultIterator();
        $value = $iterator->current();
        if ($iterator->each && $value instanceof contracts\Record) {
            $entity = $this->getRepository()->createEntityFromSource($value);
        } elseif (!$iterator->each) {
            foreach ($value as $record) {
                $entity[] = $this->getRepository()->createEntityFromSource($record);
            }
        } else {
            $entity = null;
        }
        return $entity;
    }

    public function next() {
        $this->getQueryResultIterator()->next();
    }

    public function key() {
        return $this->getQueryResultIterator()->key();
    }

    public function valid() {
        return $this->getQueryResultIterator()->valid();
    }

    public function rewind() {
        $this->getQueryResultIterator()->rewind();
    }

    protected function getQueryResultIterator() {
        return $this->_queryResultIterator;
    }

    public function getRepository() {
        return $this->_repository;
    }

    public function setRepository($repository) {
        $this->_repository = $repository;
    }
}
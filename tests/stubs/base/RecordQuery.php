<?php

namespace tests\stubs\base;

/**
 * Represents
 *
 * @package tests\stubs
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class RecordQuery extends \dekey\domain\db\RecordQuery {

    public $records = [];
    public $singleRecord = [];
    public function all($db = null) {
        return $this->records;
    }

    public function one($db = null) {
        return $this->singleRecord;
    }

    public function batch($batchSize = 100, $db = null) {
        return array_chunk($this->records, $batchSize);
    }

    public function each($batchSize = 100, $db = null) {
        return $this->records;
    }

    // @todo implement
    /*public function scalar($db = null) {
        return parent::scalar($db);
    }*/

    public function count($q = '*', $db = null) {
        return count($this->records);
    }
}
<?php

namespace tests\stubs\models\dummy;

use tests\stubs\base\Record;

/**
 * Represents
 *
 * @package tests\stubs\models\dummy
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class DummyRecord extends Record {
    public $id;

    public function getPrimaryKey($asBool = false) {
        return $this->id;
    }

}
<?php

namespace PHPKitchen\Domain\Specs\Unit\Stubs\Models\Dummy;

use PHPKitchen\Domain\Specs\Unit\Stubs\Base\Record;

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
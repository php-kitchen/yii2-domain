<?php

namespace PHPKitchen\Domain\db;

use PHPKitchen\Domain\base\MagicObject;

/**
 * Represents
 *
 * @package PHPKitchen\Domain\db
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class Condition extends MagicObject {
    public function __call($name, $params) {
        if ($name === 'and') {
            $result = $this->callAnd();
        } elseif ($name === 'or') {
            $result = $this->callAnd();
        } else {
            $result = parent::__call($name, $params);
        }

        return $result;
    }
}
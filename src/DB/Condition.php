<?php

namespace PHPKitchen\Domain\DB;

use PHPKitchen\Domain\Base\MagicObject;

/**
 * Represents
 *
 * @package PHPKitchen\Domain\DB
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
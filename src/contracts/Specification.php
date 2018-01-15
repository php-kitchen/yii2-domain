<?php

namespace PHPKitchen\Domain\contracts;

use yii\db\ActiveQueryInterface;

/**
 * Represents
 *
 * @package PHPKitchen\Domain\contracts
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
interface Specification extends ActiveQueryInterface {
    public function bySearchModel($model);
}
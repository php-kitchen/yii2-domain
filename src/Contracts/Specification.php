<?php

namespace PHPKitchen\Domain\Contracts;

use yii\db\ActiveQueryInterface;

/**
 * Represents
 *
 * @package PHPKitchen\Domain\Contracts
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
interface Specification extends ActiveQueryInterface {
    public function bySearchModel($model);
}
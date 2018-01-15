<?php

namespace PHPKitchen\Domain\db;

use PHPKitchen\DI\Contracts\ContainerAware;
use PHPKitchen\DI\Contracts\ServiceLocatorAware;
use PHPKitchen\DI\Mixins\ContainerAccess;
use PHPKitchen\DI\Mixins\ServiceLocatorAccess;
use PHPKitchen\Domain\contracts;
use PHPKitchen\Domain\db\mixins\QueryConditionBuilderAccess;
use PHPKitchen\Domain\db\mixins\RecordQueryFunctions;
use yii\db\ActiveQuery;

/**
 * Represents
 *
 * @package PHPKitchen\Domain\base
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class RecordQuery extends ActiveQuery implements contracts\Specification, contracts\RecordQuery, ContainerAware, ServiceLocatorAware {
    use QueryConditionBuilderAccess;
    use RecordQueryFunctions;
    use ContainerAccess;
    use ServiceLocatorAccess;
}
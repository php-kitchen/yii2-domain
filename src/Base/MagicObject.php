<?php

namespace PHPKitchen\Domain\Base;

use PHPKitchen\DI\Contracts\ContainerAware;
use PHPKitchen\DI\Contracts\ServiceLocatorAware;
use PHPKitchen\DI\Mixins\ContainerAccess;
use PHPKitchen\DI\Mixins\ServiceLocatorAccess;
use PHPKitchen\Domain\Mixins\LoggerAccess;
use yii\base\Object;

/**
 * Represents
 *
 * @package PHPKitchen\Domain\base
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class MagicObject extends Object implements ContainerAware, ServiceLocatorAware {
    use ServiceLocatorAccess;
    use ContainerAccess;
    use LoggerAccess;
}
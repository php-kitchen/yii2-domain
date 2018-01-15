<?php

namespace PHPKitchen\Domain\base;

use PHPKitchen\DI\Contracts\ContainerAware;
use PHPKitchen\DI\Contracts\ServiceLocatorAware;
use PHPKitchen\DI\Mixins\ContainerAccess;
use PHPKitchen\DI\Mixins\ServiceLocatorAccess;
use PHPKitchen\Domain\mixins\LoggerAccess;
use yii\base\BaseObject;

/**
 * Represents
 *
 * @package PHPKitchen\Domain\base
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class MagicObject extends BaseObject implements ContainerAware, ServiceLocatorAware {
    use ServiceLocatorAccess;
    use ContainerAccess;
    use LoggerAccess;
}
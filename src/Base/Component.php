<?php

namespace PHPKitchen\Domain\Base;

use PHPKitchen\DI\Contracts\ContainerAware;
use PHPKitchen\DI\Contracts\ServiceLocatorAware;
use PHPKitchen\DI\Mixins\ContainerAccess;
use PHPKitchen\DI\Mixins\ServiceLocatorAccess;
use PHPKitchen\Domain\Contracts\LoggerAware;
use PHPKitchen\Domain\Mixins\LoggerAccess;

/**
 * Represents
 *
 * @package PHPKitchen\Domain\base
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class Component extends \yii\base\Component implements ContainerAware, ServiceLocatorAware, LoggerAware {
    use ServiceLocatorAccess;
    use ContainerAccess;
    use LoggerAccess;
}
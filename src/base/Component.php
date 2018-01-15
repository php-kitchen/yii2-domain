<?php

namespace PHPKitchen\Domain\base;

use PHPKitchen\DI\Contracts\ContainerAware;
use PHPKitchen\DI\Contracts\ServiceLocatorAware;
use PHPKitchen\DI\Mixins\ContainerAccess;
use PHPKitchen\DI\Mixins\ServiceLocatorAccess;
use PHPKitchen\Domain\contracts\LoggerAware;
use PHPKitchen\Domain\mixins\LoggerAccess;

/**
 * Extends default Yii {@link \yii\base\Component} to provide container and
 * service provider access features.
 *
 * @package PHPKitchen\Domain\base
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class Component extends \yii\base\Component implements ContainerAware, ServiceLocatorAware, LoggerAware {
    use ServiceLocatorAccess;
    use ContainerAccess;
    use LoggerAccess;
}
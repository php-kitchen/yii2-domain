<?php

namespace dekey\domain\base;

use dekey\di\contracts\ContainerAware;
use dekey\di\contracts\ServiceLocatorAware;
use dekey\di\mixins\ContainerAccess;
use dekey\di\mixins\ServiceLocatorAccess;
use dekey\domain\contracts\LoggerAware;
use dekey\domain\mixins\LoggerAccess;
/**
 * Represents
 *
 * @package dekey\domain\base
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class Component extends \yii\base\Component implements ContainerAware, ServiceLocatorAware, LoggerAware {
    use ServiceLocatorAccess;
    use ContainerAccess;
    use LoggerAccess;
}
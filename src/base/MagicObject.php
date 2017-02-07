<?php

namespace dekey\domain\base;

use dekey\di\contracts\ContainerAware;
use dekey\di\contracts\ServiceLocatorAware;
use dekey\di\mixins\ContainerAccess;
use dekey\di\mixins\ServiceLocatorAccess;
use dekey\domain\mixins\LoggerAccess;
use yii\base\Object;

/**
 * Represents
 *
 * @package dekey\domain\base
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class MagicObject extends Object implements ContainerAware, ServiceLocatorAware {
    use ServiceLocatorAccess;
    use ContainerAccess;
    use LoggerAccess;
}
<?php

namespace PHPKitchen\Domain\Base;

use PHPKitchen\Domain\Mixins\StrategiesComposingAlgorithm;

/**
 * Represents
 *
 * @deprecated use php-kitchen/flow
 *
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class CompositeStrategy extends Strategy {
    use StrategiesComposingAlgorithm;
}
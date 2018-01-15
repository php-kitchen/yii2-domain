<?php

namespace PHPKitchen\Domain\base;

use PHPKitchen\Domain\mixins\StrategiesComposingAlgorithm;

/**
 * Represents
 *
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class CompositeStrategy extends Strategy {
    use StrategiesComposingAlgorithm;
}
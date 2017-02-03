<?php
namespace dekey\domain\base;

use dekey\domain\mixins\StrategiesComposingAlgorithm;

/**
 * Represents
 *
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class CompositeStrategy extends Strategy {
    use StrategiesComposingAlgorithm;
}
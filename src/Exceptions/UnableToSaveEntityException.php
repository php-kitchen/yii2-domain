<?php

namespace PHPKitchen\Domain\Exceptions;

/**
 * Represents
 *
 * @package PHPKitchen\Domain\Exceptions
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class UnableToSaveEntityException extends \Exception {
    public $errorsList = [];
}

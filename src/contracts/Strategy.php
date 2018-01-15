<?php

namespace PHPKitchen\Domain\contracts;

/**
 * Represents
 *
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
interface Strategy {
    const EVENT_BEFORE_CALL = 'beforeCall';
    const EVENT_AFTER_CALL = 'afterCall';

    public function call();
}
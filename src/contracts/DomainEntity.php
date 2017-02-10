<?php

namespace dekey\domain\contracts;

/**
 * Represents domain entity.
 *
 * @package dekey\domain
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
interface DomainEntity {
    public function isNew();

    public function isNotNew();
}
<?php

namespace PHPKitchen\Domain\Contracts;

use PHPKitchen\Domain\Base\DataMapper;

/**
 * Represents domain entity.
 *
 * @method DataMapper getDataMapper()
 *
 * @package PHPKitchen\Domain
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
interface DomainEntity {
    public function isNew();

    public function isNotNew();
}
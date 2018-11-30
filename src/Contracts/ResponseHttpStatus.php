<?php

namespace PHPKitchen\Domain\Contracts;

/**
 * Defines interface of Response Http Status codes
 *
 * @package PHPKitchen\Domain\Contracts
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
interface ResponseHttpStatus {
    public const OK = 200;
    public const FOUND = 302;
}
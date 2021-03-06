<?php

namespace PHPKitchen\Domain\Contracts;

/**
 * Defines interfaces for recovered entities functionality of DB repository.
 *
 * @package PHPKitchen\Domain\Contracts
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
interface RecoverableRepository {
    public function recover(DomainEntity $entity);
}
<?php

namespace PHPKitchen\Domain\Contracts;

use PHPKitchen\Domain\DB\EntitiesRepository;

/**
 * Represents
 *
 * @property EntitiesRepository $repository
 *
 * @deprecated
 *
 * @package PHPKitchen\Domain\Contracts
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
interface EntityCrudController {
    /**
     * @return EntitiesRepository
     */
    public function getRepository();

    public function setRepository($repository);

    public function findEntityByPk($pk);

    public function createListingDataProvider();
}
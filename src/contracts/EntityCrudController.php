<?php

namespace PHPKitchen\Domain\contracts;

use PHPKitchen\Domain\db\EntitiesRepository;

/**
 * Represents
 *
 * @property EntitiesRepository $repository
 *
 * @deprecated
 *
 * @package PHPKitchen\Domain\contracts
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
<?php

namespace dekey\domain\contracts;

use dekey\domain\db\EntitiesRepository;

/**
 * Represents
 *
 * @property EntitiesRepository $repository
 *
 * @deprecated
 *
 * @package dekey\domain\contracts
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
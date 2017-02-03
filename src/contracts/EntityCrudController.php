<?php

namespace dekey\domain\contracts;

use dekey\domain\db\Repository;

/**
 * Represents
 *
 * @package dekey\domain\contracts
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
interface EntityCrudController {
    /**
     * @return Repository
     */
    public function getRepository();

    public function setRepository($repository);

    public function findEntityByPk($pk);

    public function createListingRecordsProvider();
}
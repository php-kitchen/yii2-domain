<?php

namespace dekey\domain\contracts;

/**
 * Represents entities controller.
 *
 * @property \dekey\domain\db\EntitiesRepository $repository
 *
 * @package dekey\domain\contracts
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
interface EntityController {
    /**
     * @return \dekey\domain\db\EntitiesRepository
     */
    public function getRepository();

    public function setRepository(Repository $repository);

    public function findEntityByPk($pk);
}
<?php

namespace PHPKitchen\Domain\contracts;

/**
 * Represents entities controller.
 *
 * @property \PHPKitchen\Domain\db\EntitiesRepository $repository
 *
 * @package PHPKitchen\Domain\contracts
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
interface EntityController {
    /**
     * @return \PHPKitchen\Domain\db\EntitiesRepository
     */
    public function getRepository();

    public function setRepository(Repository $repository);

    public function findEntityByPk($pk);
}
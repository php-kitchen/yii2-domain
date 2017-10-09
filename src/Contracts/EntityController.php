<?php

namespace PHPKitchen\Domain\Contracts;

/**
 * Represents entities controller.
 *
 * @property \PHPKitchen\Domain\DB\EntitiesRepository $repository
 *
 * @package PHPKitchen\Domain\Contracts
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
interface EntityController {
    /**
     * @return \PHPKitchen\Domain\DB\EntitiesRepository
     */
    public function getRepository();

    public function setRepository(Repository $repository);

    public function findEntityByPk($pk);
}
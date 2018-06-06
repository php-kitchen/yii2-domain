<?php

namespace PHPKitchen\Domain\Web\Strategy;

/**
 * Represents
 *
 * @author Dmitry Kolodko <prowwid@gmail.com>
 * @since 1.0
 */
class FilterAllEntities extends Base\CriteriaFilterStrategy {
    /**
     * @param \PHPKitchen\Domain\Contracts\Specification $finder
     *
     * @return mixed
     *
     * @since 1.0
     */
    protected function searchBy($finder) {
        return $finder->all();
    }
}
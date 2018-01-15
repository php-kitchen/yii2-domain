<?php

namespace PHPKitchen\Domain\web\base;

use PHPKitchen\Domain\contracts\Specification;

/**
 * Represents a view model designed to be used in listing actions.
 *
 * @package PHPKitchen\Domain\web\base
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class ListingModel extends ViewModel {
    public $fetchDataAsArray = true;

    /**
     * Override this method
     *
     * @return \PHPKitchen\Domain\data\EntitiesProvider
     */
    public function getDataProvider() {
        $provider = $this->repository->getEntitiesProvider();
        if ($this->fetchDataAsArray) {
            $provider->query->asArray();
        }
        if ($provider->query instanceof Specification) {
            $provider->query->bySearchModel($this);
        }

        return $provider;
    }
}
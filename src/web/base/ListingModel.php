<?php

namespace dekey\domain\web\base;

use dekey\domain\contracts\Specification;

/**
 * Represents a view model designed to be used in listing actions.
 *
 * @package dekey\domain\web\base
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class ListingModel extends ViewModel {
    public $fetchDataAsArray = true;

    /**
     * Override this method
     *
     * @return \dekey\domain\data\EntitiesProvider
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
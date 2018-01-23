<?php

namespace PHPKitchen\Domain\Data;

use PHPKitchen\Domain\Contracts\Record;

/**
 * Represents data provider of an Entity.
 *
 * Extends {@link RecordsProvider} to fetch data using query object and then convert
 * records to entities.
 *
 * @package PHPKitchen\Domain\Data
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class EntitiesProvider extends RecordsProvider {
    protected function prepareModels() {
        $result = parent::prepareModels();
        if (isset($result[0]) && is_object($result[0]) && $result[0] instanceof Record) {
            $repository = $this->repository;
            foreach ($result as $key => $record) {
                $newResult[$key] = $repository->createEntityFromSource($record);
            }
        } else {
            $newResult = &$result;
        }

        return $newResult;
    }
}
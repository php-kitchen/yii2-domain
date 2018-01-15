<?php

namespace PHPKitchen\Domain\data;

use PHPKitchen\Domain\contracts\Record;

/**
 * Represents data provider of an Entity.
 *
 * Extends {@link RecordsProvider} to fetch data using query object and then convert
 * records to entities.
 *
 * @package PHPKitchen\Domain\data
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
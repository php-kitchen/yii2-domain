<?php

namespace dekey\domain\data;

use dekey\domain\contracts\Record;
use dekey\domain\contracts\Repository;
use yii\data\ActiveDataProvider;

/**
 * Represents data provider of an Entity.
 *
 * Extends {@link ActiveDataProvider} to fetch data using query object and then convert
 * records to entities.
 *
 * @property \dekey\domain\db\EntitiesRepository $repository
 *
 * @package dekey\domain\data
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class EntitiesProvider extends ActiveDataProvider {
    /**
     * @var \dekey\domain\db\EntitiesRepository
     */
    protected $_repository;

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

    public function getRepository() {
        return $this->_repository;
    }

    public function setRepository(Repository $repository) {
        $this->_repository = $repository;
    }
}
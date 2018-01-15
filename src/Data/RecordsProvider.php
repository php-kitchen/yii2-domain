<?php

namespace PHPKitchen\Domain\Data;

use PHPKitchen\Domain\Contracts;
use yii\data\ActiveDataProvider;

/**
 * Represents DB records provider.
 *
 * @property \PHPKitchen\Domain\DB\EntitiesRepository|\PHPKitchen\Domain\DB\RecordsRepository $repository
 * @property \PHPKitchen\Domain\DB\RecordQuery $query
 *
 * @package PHPKitchen\Domain\Data
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class RecordsProvider extends ActiveDataProvider {
    /**
     * @var \PHPKitchen\Domain\DB\EntitiesRepository|\PHPKitchen\Domain\DB\RecordsRepository
     */
    protected $_repository;

    public function getRepository() {
        return $this->_repository;
    }

    public function setRepository(contracts\Repository $repository) {
        $this->_repository = $repository;
    }
}
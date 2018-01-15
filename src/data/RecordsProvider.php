<?php

namespace PHPKitchen\Domain\data;

use PHPKitchen\Domain\contracts;
use yii\data\ActiveDataProvider;

/**
 * Represents DB records provider.
 *
 * @property \PHPKitchen\Domain\db\EntitiesRepository|\PHPKitchen\Domain\db\RecordsRepository $repository
 * @property \PHPKitchen\Domain\db\RecordQuery $query
 *
 * @package PHPKitchen\Domain\data
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class RecordsProvider extends ActiveDataProvider {
    /**
     * @var \PHPKitchen\Domain\db\EntitiesRepository|\PHPKitchen\Domain\db\RecordsRepository
     */
    protected $_repository;

    public function getRepository() {
        return $this->_repository;
    }

    public function setRepository(contracts\Repository $repository) {
        $this->_repository = $repository;
    }
}
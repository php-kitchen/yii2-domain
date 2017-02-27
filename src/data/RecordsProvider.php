<?php

namespace dekey\domain\data;

use dekey\domain\contracts;
use yii\data\ActiveDataProvider;

/**
 * Represents DB records provider.
 *
 * @property \dekey\domain\db\EntitiesRepository|\dekey\domain\db\RecordsRepository $repository
 * @property \dekey\domain\db\RecordQuery $query
 *
 * @package dekey\domain\data
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class RecordsProvider extends ActiveDataProvider {
    /**
     * @var \dekey\domain\db\EntitiesRepository|\dekey\domain\db\RecordsRepository
     */
    protected $_repository;

    public function getRepository() {
        return $this->_repository;
    }

    public function setRepository(contracts\Repository $repository) {
        $this->_repository = $repository;
    }
}
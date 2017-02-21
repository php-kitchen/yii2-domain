<?php

namespace dekey\domain\contracts;

use yii\db\ActiveQueryInterface;

/**
 * Represents
 *
 * @property string $alias public alias of the {@link _alias}
 * @property string $mainTableName public alias of the {@link _mainTableName}
 *
 * @package dekey\domain\contracts
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
interface RecordQuery extends ActiveQueryInterface {
    public function find();

    /**
     * @param $pk
     * @return ActiveRecord|array|null
     */
    public function oneWithPk($pk);
}
<?php

namespace PHPKitchen\Domain\contracts;

use yii\db\ActiveQueryInterface;

/**
 * Represents
 *
 * @property string $alias public alias of the {@link _alias}
 * @property string $mainTableName public alias of the {@link _mainTableName}
 *
 * @package PHPKitchen\Domain\contracts
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
interface RecordQuery extends ActiveQueryInterface {
    public function find();

    /**
     * @param $pk
     *
     * @return ActiveRecord|array|null
     */
    public function oneWithPk($pk);
}
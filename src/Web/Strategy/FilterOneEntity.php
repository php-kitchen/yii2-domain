<?php

namespace PHPKitchen\Domain\Web\Strategy;

use PHPKitchen\DI\Contracts\ContainerAware;
use PHPKitchen\DI\Mixins\ContainerAccess;
use PHPKitchen\Domain\DB\EntitiesRepository;
use yii\base\InvalidConfigException;

/**
 * Represents
 *
 * @author Dmitry Kolodko <prowwid@gmail.com>
 * @since 1.0
 */
class FilterOneEntity extends Base\CriteriaFilterStrategy {
    protected $identifier;


    /**
     * @param \PHPKitchen\Domain\Contracts\Specification $finder
     *
     * @return mixed
     *
     * @since 1.0
     */
    protected function searchBy($finder) {
        return $finder->oneWithPk($this->identifier);
    }
}
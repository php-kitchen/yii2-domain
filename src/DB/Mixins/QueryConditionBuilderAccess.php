<?php

namespace PHPKitchen\Domain\DB\Mixins;

use PHPKitchen\Domain\DB\QueryConditionBuilder;

/**
 * Represents
 *
 * @property QueryConditionBuilder $conditionBuilder protected alias of the {@link _conditionBuilder}
 *
 * @mixin \PHPKitchen\DI\Mixins\ContainerAccess
 *
 * @package PHPKitchen\Domain\DB\Mixins
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
trait QueryConditionBuilderAccess {
    protected $conditionBuilderClassName = QueryConditionBuilder::class;
    private $_conditionBuilder;

    /**
     * Alias of {@link QueryConditionBuilder::buildAliasedNameOfField}
     *
     * @param string $field field name.
     * @param null $alias optional alias. If not used query alias will be used.
     *
     * @return string
     */
    public function buildAliasedNameOfField($field, $alias = null) {
        return $this->conditionBuilder->buildAliasedNameOfField($field, $alias);
    }

    public function buildAliasedNameOfParam($param, $alias = null) {
        return $this->conditionBuilder->buildAliasedNameOfParam($param, $alias);
    }

    protected function getConditionBuilder() {
        if (null === $this->_conditionBuilder) {
            $this->_conditionBuilder = $this->container->create($this->conditionBuilderClassName, [$query = $this]);
        }

        return $this->_conditionBuilder;
    }
}
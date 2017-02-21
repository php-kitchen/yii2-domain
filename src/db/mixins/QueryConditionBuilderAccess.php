<?php

namespace dekey\domain\db\mixins;

use dekey\domain\db\QueryConditionBuilder;

/**
 * Represents
 *
 * @property QueryConditionBuilder $conditionBuilder protected alias of the {@link _conditionBuilder}
 *
 * @mixin \dekey\di\mixins\ContainerAccess
 *
 * @package dekey\domain\db\mixins
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
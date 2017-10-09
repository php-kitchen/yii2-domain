<?php

namespace PHPKitchen\Domain\DB;

use PHPKitchen\Domain\Base\MagicObject;
use PHPKitchen\Domain\Contracts;

/**
 * Represents
 *
 * @package PHPKitchen\Domain\db
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class QueryConditionBuilder extends MagicObject {
    /**
     * @var RecordQuery
     */
    protected $query;
    private $_paramNamesCounters = [];

    public function __construct(contracts\RecordQuery $query, $config = []) {
        $this->query = $query;
        parent::__construct($config);
    }

    public function buildAliasedNameOfField($field, $alias = null) {
        $alias = $alias ? $alias : $this->query->alias;
        return "[[$alias]].[[$field]]";
    }

    public function buildAliasedNameOfParam($param, $alias = null) {
        $alias = $alias ? $alias : $this->query->alias;
        $paramName = ":{$alias}_{$param}";
        if ($this->isParamNameUsed($paramName)) {
            $index = $this->getParamNameNextIndexAndIncreaseCurrent($paramName);
            $paramName = "{$paramName}_{$index}";
        } else {
            $this->addParamNameToUsed($paramName);
        }
        return $paramName;
    }

    protected function isParamNameUsed($paramName) {
        return isset($this->_paramNamesCounters[$paramName]);
    }

    protected function addParamNameToUsed($paramName) {
        $this->_paramNamesCounters[$paramName] = 0;
    }

    protected function getParamNameNextIndexAndIncreaseCurrent($paramName) {
        $this->_paramNamesCounters[$paramName]++;
        return $this->_paramNamesCounters[$paramName];
    }
}
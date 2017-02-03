<?php

namespace dekey\domain\db;

use yii\db\ActiveQuery;

/**
 * Represents
 *
 * @package dekey\domain\base
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class RecordQuery extends ActiveQuery {
    public $primaryKeyName = 'id';
    private $_alias;
    private $_mainTableName;
    private $_paramNamesCounters = [];

    public function alias($alias) {
        $this->_alias = $alias;
        return parent::alias($alias);
    }

    /**
     * Method designed to make chain of query methods more accurate if query used as a stored object and not as a part
     * of active record.
     * Example:
     * <pre>
     * $finder = new ActiveQuery();
     *     $resultSet = $finder->find()
     *       ->active()
     *       ->withSomeRelation()
     *       ->all();
     *     $record = $finder->find()->one();
     * </pre>
     *
     * @return $this
     */
    public function find() {
        $clone = clone $this;
        foreach ($this->getBehaviors() as $name => $behavior) {
            $clone->attachBehavior($name, clone $behavior);
        }
        return $clone;
    }

    /**
     * @param $pk
     * @return ActiveRecord|array|null
     */
    public function oneWithPk($pk) {
        $pkParam = $this->buildAliasedParamName('pk');
        $primaryKey = $this->buildAliasedFieldName($this->primaryKeyName);
        $this->andWhere("{$primaryKey}={$pkParam}", [$pkParam => $pk]);
        return $this->one();
    }

    protected function buildAliasedFieldName($field, $alias = null) {
        $alias = $alias ? $alias : $this->getAlias();
        return "[[$alias]].[[$field]]";
    }

    protected function buildAliasedParamName($field, $alias = null) {
        $alias = $alias ? $alias : $this->getAlias();
        $paramName = ":{$alias}_{$field}";
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

    public function getMainTableName() {
        if ($this->_mainTableName == null) {
            $method = new \ReflectionMethod($this->modelClass, 'tableName');
            $this->_mainTableName = $method->invoke(null);
        }
        return $this->_mainTableName;
    }

    public function setMainTableName($mainTableName) {
        $this->_mainTableName = $mainTableName;
    }

    public function getAlias() {
        if ($this->_alias === null) {
            $this->_alias = $this->getMainTableName();
        }
        return $this->_alias;
    }
}
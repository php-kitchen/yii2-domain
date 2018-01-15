<?php

namespace PHPKitchen\Domain\web\mixins;

use yii\helpers\ArrayHelper;

/**
 * Represents
 *
 * @package PHPKitchen\Domain\web\mixins
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
trait ControllerActionsManagement {
    private $_actions = [];

    public function actions() {
        return $this->_actions;
    }

    protected function addAction($name, $definition) {
        $this->_actions[$name] = $definition;
    }

    protected function updateActionDefinition($name, $definition) {
        if (is_string($definition) || is_object($definition)) {
            $this->_actions[$name] = $definition;
        } elseif (is_array($definition)) {
            if ($this->isDynamicActionDefined($name) && is_array($this->_actions[$name])) {
                $this->_actions[$name] = ArrayHelper::merge($this->_actions[$name], $definition);
            } else {
                $this->_actions[$name] = $definition;
            }
        }
    }

    protected function removeAction($name) {
        unset($this->_actions[$name]);
    }

    protected function isDynamicActionDefined($name) {
        return isset($this->_actions[$name]);
    }

    protected function setActions(array $actions) {
        $this->_actions = $actions;
    }
}
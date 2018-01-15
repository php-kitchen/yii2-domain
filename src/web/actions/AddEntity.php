<?php

namespace PHPKitchen\Domain\web\actions;

use PHPKitchen\Domain\web\base\EntityModificationAction;

/**
 * Represents
 *
 * @package PHPKitchen\Domain\web\actions
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class AddEntity extends EntityModificationAction {
    public function init() {
        $this->setViewFileIfNotSetTo('add');
    }

    public function run() {
        return $this->loadModelAndSaveOrPrintView();
    }

    protected function initModel() {
        $this->_model = $this->createNewModel();
    }
}
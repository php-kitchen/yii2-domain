<?php

namespace dekey\domain\web\actions;

use dekey\domain\web\base\EntityModificationAction;

/**
 * Represents
 *
 * @package dekey\domain\web\actions
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
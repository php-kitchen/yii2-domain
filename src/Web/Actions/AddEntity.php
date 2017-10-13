<?php

namespace PHPKitchen\Domain\Web\Actions;

use PHPKitchen\Domain\Web\Base\EntityModificationAction;

/**
 * Represents
 *
 * @package PHPKitchen\Domain\Web\Actions
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
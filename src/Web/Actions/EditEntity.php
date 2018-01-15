<?php

namespace PHPKitchen\Domain\Web\Actions;

use PHPKitchen\Domain\Web\Base\EntityModificationAction;

/**
 * Represents
 *
 * @package PHPKitchen\Domain\Web\Actions
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class EditEntity extends EntityModificationAction {
    protected $entityId;

    public function init() {
        $this->setViewFileIfNotSetTo('edit');
    }

    public function run($id) {
        $this->entityId = $id;

        return $this->loadModelAndSaveOrPrintView();
    }

    protected function initModel() {
        $this->_model = $this->findModelByPk($this->entityId);
        $this->_model->loadAttributesFromEntity();
    }
}
<?php

namespace PHPKitchen\Domain\Web\Actions;

use PHPKitchen\Domain\Web\Base\EntityModificationAction;

/**
 * Represents entity modify process.
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
        $entity = $this->findEntityByIdentifierOrFail($this->entityId);
        $this->_model = $this->createViewModel($entity);
        $this->_model->loadAttributesFromEntity();
    }
}
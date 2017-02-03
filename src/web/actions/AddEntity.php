<?php

namespace dekey\domain\web\actions;

use dekey\domain\web\base\EntityModificationAction;
use dekey\domain\web\mixins\ModelSearching;
use dekey\domain\web\mixins\ViewModelManagement;

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

    protected function initModel() {
        $this->_model = $this->createNewModel();
    }
}
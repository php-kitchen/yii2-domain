<?php

namespace dekey\domain\web\mixins;


use dekey\domain\web\base\ViewModel;

/**
 * Represents
 *
 * @property \yii\web\Controller|\dekey\domain\contracts\EntityCrudController $controller
 * @property string $id
 *
 * @mixin \dekey\di\mixins\ServiceLocatorAccess
 * @mixin \dekey\di\mixins\ContainerAccess
 * @package dekey\domain\web\mixins
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
trait ViewModelManagement {
    private $_viewModelClassName;
    /**
     * @var string the scenario to be assigned to the new model before it is validated and saved.
     */
    public $scenario = ViewModel::SCENARIO_DEFAULT;

    protected function createNewModel() {
        $entity = $this->controller->getRepository()->createNewEntity();
        return $this->createViewModel($entity);
    }

    /**
     * @param \dekey\domain\base\Entity $entity
     * @return \dekey\domain\web\base\ViewModel
     */
    protected function createViewModel($entity) {
        $model = $this->container->create([
            'class' => $this->getViewModelClassName(),
            'entity' => $entity,
            'controller' => $this->controller,
        ]);
        $model->scenario = $this->scenario;
        return $model;
    }

    public function getViewModelClassName() {
        return $this->_viewModelClassName;
    }

    public function setViewModelClassName($viewModelClassName) {
        $this->_viewModelClassName = $viewModelClassName;
    }
}
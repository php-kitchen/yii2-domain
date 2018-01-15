<?php

namespace PHPKitchen\Domain\Web\Mixins;

use PHPKitchen\Domain\Web\Base\ViewModel;

/**
 * Represents
 *
 * @property \yii\web\Controller|\PHPKitchen\Domain\Contracts\EntityCrudController $controller
 * @property string $id
 * @property string $viewModelClassName
 *
 * @mixin \PHPKitchen\DI\Mixins\ServiceLocatorAccess
 * @mixin \PHPKitchen\DI\Mixins\ContainerAccess
 * @package PHPKitchen\Domain\Web\Mixins
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
     * @param \PHPKitchen\Domain\Base\Entity $entity
     *
     * @return \PHPKitchen\Domain\Web\Base\ViewModel
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
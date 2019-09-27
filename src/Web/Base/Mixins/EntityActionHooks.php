<?php

namespace PHPKitchen\Domain\Web\Base\Mixins;

/**
 * Mixin that provides properties and methods to work with DB repository.
 *
 * Own properties:
 *
 * @property \PHPKitchen\Domain\DB\EntitiesRepository $repository
 *
 * Globally available properties:
 * @property \PHPKitchen\DI\Container $container
 *
 * Parent properties:
 * @property \PHPKitchen\Domain\Contracts\EntityCrudController|\yii\web\Controller $controller
 *
 * @uses SessionMessagesManagement
 * @uses ResponseManagement
 *
 * @package PHPKitchen\Domain\Web\Base\Mixins
 */
trait EntityActionHooks {
    public $failToSaveErrorFlashMessage = 'Unable to save entity';
    public $validationFailedFlashMessage = 'Please correct errors.';
    public $successFlashMessage = 'Changes successfully saved.';

    abstract protected function printView();

    protected function handleSuccessfulOperation() {
        $this->addSuccessFlash($this->successFlashMessage);
        if ($this->redirectUrl !== false) {
            return $this->redirectToNextPage();
        }

        return $this->printView();
    }

    protected function handleFailedOperation() {
        $this->addErrorFlash($this->validationFailedFlashMessage);

        return $this->printView();
    }
}
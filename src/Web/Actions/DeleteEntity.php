<?php

namespace PHPKitchen\Domain\Web\Actions;

use PHPKitchen\Domain\Eexceptions\UnableToSaveEntityException;
use PHPKitchen\Domain\Web\Base\Action;

/**
 * Represents
 *
 * @package PHPKitchen\Domain\Web
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class DeleteEntity extends Action {
    public $failToDeleteErrorFlashMessage = 'Unable to delete entity';
    public $redirectUrl;

    public function init() {
        $this->setViewFileIfNotSetTo('list');
    }

    public function run($id) {
        $controller = $this->controller;
        $entity = $controller->findEntityByPk($id);
        $this->tryToDeleteEntity($entity);
        return $this->redirectToNextPage();
    }

    protected function tryToDeleteEntity($entity) {
        $controller = $this->controller;
        try {
            $savedSuccessfully = $controller->repository->delete($entity);
        } catch (UnableToSaveEntityException $e) {
            $savedSuccessfully = false;
        }
        if ($savedSuccessfully) {
            $this->addSuccessFlash($this->failToDeleteErrorFlashMessage);
        } else {
            $this->addErrorFlash($this->failToDeleteErrorFlashMessage);
        }
    }

    protected function redirectToNextPage() {
        if (null === $this->redirectUrl) {
            $redirectUrl = ['list'];
        } elseif (is_callable($this->redirectUrl)) {
            $redirectUrl = call_user_func($this->redirectUrl, $this);
        } else {
            $redirectUrl = $this->redirectUrl;
        }
        return $this->controller->redirect($redirectUrl);
    }
}
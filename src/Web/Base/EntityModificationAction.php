<?php

namespace PHPKitchen\Domain\Web\Base;

use PHPKitchen\Domain\Exceptions\UnableToSaveEntityException;
use PHPKitchen\Domain\Web\Mixins\ModelSearching;
use PHPKitchen\Domain\Web\Mixins\ViewModelManagement;

/**
 * Represents
 *
 * @package PHPKitchen\Domain\Web\Base
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
abstract class EntityModificationAction extends Action {
    use ViewModelManagement;
    use ModelSearching;
    public $redirectUrl;
    public $failToSaveErrorFlashMessage = 'Unable to save entity';
    public $validationFailedFlashMessage = 'Please correct errors.';
    public $successFlashMessage = 'Changes successfully saved.';
    /**
     * @var \PHPKitchen\Domain\Web\Base\ViewModel;
     */
    protected $_model;

    abstract protected function initModel();

    protected function loadModelAndSaveOrPrintView() {
        return $this->loadModelAndSave() ?
            $this->handleSuccessfulOperation()
            : $this->handledFailedOperation();
    }

    protected function loadModelAndSave() {
        $isSaved = false;
        if ($this->getModel()->load($this->getRequest()->post())) {
            $isSaved = $this->validateModelAndTryToSaveEntity();
        }

        return $isSaved;
    }

    protected function validateModelAndTryToSaveEntity() {
        if ($this->getModel()->validate()) {
            $result = $this->tryToSaveEntity();
        } else {
            $this->addErrorFlash($this->validationFailedFlashMessage);
            $result = false;
        }

        return $result;
    }

    protected function handleSuccessfulOperation() {
        if ($this->redirectUrl) {
            return $this->redirectToNextPage();
        }
        $model = $this->getModel();

        return $this->renderViewFile(compact('model'));
    }

    protected function handledFailedOperation() {
        $model = $this->getModel();

        return $this->renderViewFile(compact('model'));
    }

    protected function tryToSaveEntity() {
        $controller = $this->controller;
        $entity = $this->getModel()->convertToEntity();
        try {
            $savedSuccessfully = $controller->repository->validateAndSave($entity);
        } catch (UnableToSaveEntityException $e) {
            $savedSuccessfully = false;
        }
        if ($savedSuccessfully) {
            $this->addSuccessFlash($this->successFlashMessage);
        } else {
            $this->addErrorFlash($this->failToSaveErrorFlashMessage);
        }

        return $savedSuccessfully;
    }

    protected function redirectToNextPage() {
        $entity = $this->getModel()->convertToEntity();
        if (null === $this->redirectUrl) {
            $redirectUrl = ['edit', 'id' => $entity->id];
        } elseif (is_callable($this->redirectUrl)) {
            $redirectUrl = call_user_func($this->redirectUrl, $entity, $this);
        } else {
            $redirectUrl = $this->redirectUrl;
        }

        return $this->controller->redirect($redirectUrl);
    }

    public function getModel() {
        if (null === $this->_model) {
            $this->initModel();
        }

        return $this->_model;
    }
}
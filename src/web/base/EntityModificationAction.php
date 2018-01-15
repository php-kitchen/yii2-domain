<?php

namespace PHPKitchen\Domain\web\base;

use PHPKitchen\Domain\exceptions\UnableToSaveEntityException;
use PHPKitchen\Domain\web\mixins\ModelSearching;
use PHPKitchen\Domain\web\mixins\ViewModelManagement;

/**
 * Represents
 *
 * @package PHPKitchen\Domain\web\base
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
     * @var \PHPKitchen\Domain\web\base\ViewModel;
     */
    protected $_model;

    abstract protected function initModel();

    protected function loadModelAndSaveOrPrintView() {
        $isSaved = $this->loadModelAndSave();
        $model = $this->getModel();

        return $isSaved && $this->redirectUrl !== false ? $this->redirectToNextPage() : $this->renderViewFile(compact('model'));
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
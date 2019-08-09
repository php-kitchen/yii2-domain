<?php

namespace PHPKitchen\Domain\Web\Base;

use PHPKitchen\Domain\Contracts\ResponseHttpStatus;
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
     * @var int indicates whether to throw exception or handle it
     */
    public $throwExceptions = false;
    /**
     * @var \PHPKitchen\Domain\Web\Base\ViewModel;
     */
    protected $_model;

    abstract protected function initModel();

    protected function loadModelAndSaveOrPrintView() {
        return $this->modelLoaded()
            ? $this->saveModel()
            : $this->printView();
    }

    protected function modelLoaded(): bool {
        return $this->getModel()->load($this->getRequest()->post());
    }

    protected function saveModel() {
        return $this->validateModelAndTryToSaveEntity()
            ? $this->handleSuccessfulOperation()
            : $this->handleFailedOperation();
    }

    protected function printView() {
        return $this->renderViewFile(['model' => $this->getModel()]);
    }

    protected function validateModelAndTryToSaveEntity() {
        if ($this->getModel()->validate()) {
            $result = $this->tryToSaveEntity();
        } else {
            $result = false;
        }

        return $result;
    }

    protected function handleSuccessfulOperation() {
        $this->addSuccessFlash($this->successFlashMessage);
        if ($this->redirectUrl !== false) {
            return $this->redirectToNextPage();
        }

        return $this->renderViewFile(['model' => $this->getModel()]);
    }

    protected function handleFailedOperation() {
        $this->addErrorFlash($this->validationFailedFlashMessage);

        return $this->printView();
    }

    protected function tryToSaveEntity() {
        $model = $this->getModel();
        $entity = $model->convertToEntity();
        try {
            $savedSuccessfully = $this->getRepository()->validateAndSave($entity);
            $model->loadAttributesFromEntity();
        } catch (UnableToSaveEntityException $e) {
            $savedSuccessfully = false;
            if ($this->throwExceptions) {
                throw $e;
            }
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

        return $this->controller->redirect($redirectUrl, $this->getRequestStatusCore());
    }

    protected function getRequestStatusCore() {
        if ($this->getServiceLocator()->request->isAjax) {
            return ResponseHttpStatus::OK;
        }

        return ResponseHttpStatus::FOUND;
    }

    public function getModel() {
        if (null === $this->_model) {
            $this->initModel();
        }

        return $this->_model;
    }
}
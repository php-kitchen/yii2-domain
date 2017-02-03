<?php

namespace dekey\domain\web\base;

use dekey\domain\exceptions\UnableToSaveEntityException;
use dekey\domain\web\mixins\ModelSearching;
use dekey\domain\web\mixins\ViewModelManagement;

/**
 * Represents
 *
 * @package dekey\domain\web\base
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
     * @var \dekey\domain\web\base\ViewModel;
     */
    protected $_model;

    abstract protected function initModel();

    public function run() {
        return $this->loadModelAndSaveOrPrintView();
    }

    protected function loadModelAndSaveOrPrintView() {
        $model = $this->getModel();
        if ($this->getModel()->load($this->getRequest()->post())) {
            $result = $this->validateModelAndTryToSaveEntity();
        } else {
            $result = false;
        }
        if (is_bool($result)) {
            $result = $this->renderViewFile(compact('model'));
        }
        return $result;
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
            $savedSuccessfully = $controller->getRepository()->validateAndSave($entity);
            $result = $this->redirectUrl !== false ? $this->redirectToNextPage(): $savedSuccessfully;
        } catch (UnableToSaveEntityException $e) {
            $result = false;
            $savedSuccessfully = false;
        }
        if ($savedSuccessfully) {
            $this->addSuccessFlash($this->failToSaveErrorFlashMessage);
        } else {
            $this->addErrorFlash($this->failToSaveErrorFlashMessage);
        }
        return $result;
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
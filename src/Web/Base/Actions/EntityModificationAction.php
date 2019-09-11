<?php

namespace PHPKitchen\Domain\Web\Base\Actions;

use PHPKitchen\Domain\Contracts\ResponseHttpStatus;
use PHPKitchen\Domain\Exceptions\UnableToSaveEntityException;
use PHPKitchen\Domain\Web\Base\Mixins\ResponseManagement;
use PHPKitchen\Domain\Web\Base\Mixins\EntityActionHooks;
use PHPKitchen\Domain\Web\Base\Mixins\SessionMessagesManagement;
use PHPKitchen\Domain\Web\Mixins\ModelSearching;
use PHPKitchen\Domain\Web\Mixins\ViewModelManagement;

/**
 * Represents a base class for all actions that modify entity.
 *
 * @package PHPKitchen\Domain\Web\Base
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
abstract class EntityModificationAction extends Action {
    use ViewModelManagement;
    use ModelSearching;
    use EntityActionHooks;
    /**
     * @var int indicates whether to throw exception or handle it
     */
    public $throwExceptions = false;
    /**
     * @var \PHPKitchen\Domain\Web\Base\ViewModel;
     */
    protected $_model;

    public function __construct($id, $controller, $config = []) {
        $this->defaultRedirectUrlAction = 'edit';

        parent::__construct($id, $controller, $config);
    }

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

    protected function validateModelAndTryToSaveEntity() {
        if ($this->getModel()->validate()) {
            $result = $this->tryToSaveEntity();
        } else {
            $result = false;
        }

        return $result;
    }

    protected function tryToSaveEntity() {
        $model = $this->getModel();
        $entity = $model->convertToEntity();
        try {
            $savedSuccessfully = $this->getRepository()->validateAndSave($entity);
            $this->getRepository()->refresh($entity);
            $model->loadAttributesFromEntity();
        } catch (UnableToSaveEntityException $e) {
            $savedSuccessfully = false;
            if ($this->throwExceptions) {
                throw $e;
            }
        }
        if ($savedSuccessfully) {
            // @TODO seems like duplicates handleSuccessfulOperation - need to investigate
            $this->addSuccessFlash($this->successFlashMessage);
        } else {
            $this->addErrorFlash($this->failToSaveErrorFlashMessage);
        }

        return $savedSuccessfully;
    }

    /**
     * Defines default redirect URL.
     *
     * If you need to change redirect action, set {@link defaultRedirectUrlAction} at action init.
     *
     * Override this method if you need to define custom format of URL.
     *
     * @return array url definition;
     */
    protected function prepareDefaultRedirectUrl() {
        $entity = $this->getModel()->convertToEntity();

        return [$this->defaultRedirectUrlAction, 'id' => $entity->id];
    }

    /**
     * @override base implementation for BC compatibility.
     * @TODO remove it in the next major release
     */
    protected function callRedirectUrlCallback(): array {
        $entity = $this->getModel()->convertToEntity();

        return call_user_func($this->redirectUrl, $entity, $this);
    }

    public function getModel() {
        if (null === $this->_model) {
            $this->initModel();
        }

        return $this->_model;
    }

    /**
     * @override
     */
    protected function prepareViewContext(): array {
        $context = $this->prepareViewContext();
        $context['model'] = $this->getModel();

        return $context;
    }
}
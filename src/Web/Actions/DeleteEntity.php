<?php

namespace PHPKitchen\Domain\Web\Actions;

use PHPKitchen\Domain\Exceptions\UnableToSaveEntityException;
use PHPKitchen\Domain\Web\Base\Action;
use PHPKitchen\Domain\Web\Mixins\ModelSearching;

/**
 * Represents
 *
 * @package PHPKitchen\Domain\Web
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class DeleteEntity extends Action {
    use ModelSearching;
    public $failToDeleteErrorFlashMessage = 'Unable to delete entity';
    public $successfulDeleteFlashMessage = 'Entity successfully deleted';
    public $redirectUrl;

    public function init() {
        $this->setViewFileIfNotSetTo('list');
    }

    /**
     * @param int $id
     *
     * @return \yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function run($id) {
        $entity = $this->findEntityByIdentifierOrFail($id);
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
            $this->addSuccessFlash($this->successfulDeleteFlashMessage);
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
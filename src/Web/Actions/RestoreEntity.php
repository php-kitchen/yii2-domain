<?php

namespace PHPKitchen\Domain\Web\Actions;

use PHPKitchen\Domain\Exceptions\UnableToSaveEntityException;
use PHPKitchen\Domain\Web\Base\Action;

/**
 * Represents entity restore
 *
 * @package PHPKitchen\Domain\Web\Actions
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
class RestoreEntity extends Action {
    public $failToRestoreErrorFlashMessage = 'Unable to restore entity';
    public $successfulRestoreFlashMessage = 'Entity successfully restored';
    public $redirectUrl;
    public $restoredIdsKey = 'restored-ids';

    public function init() {
        $this->setViewFileIfNotSetTo('list');
    }

    public function run($id = null) {
        $controller = $this->controller;
        $ids = ($id) ? [$id] : $this->serviceLocator->request->post($this->restoredIdsKey, []);

        $savedResults = [];
        foreach ($ids as $id) {
            $entity = $controller->findEntityByPk($id);
            $savedResults[] = $this->tryToRestoreEntity($entity);
        }

        $savedNotSuccessfully = array_filter($savedResults, function ($value) {
            return !$value;
        });
        if ($savedNotSuccessfully) {
            $this->addErrorFlash($this->failToRestoreErrorFlashMessage);
        } else {
            $this->addSuccessFlash($this->successfulRestoreFlashMessage);
        }

        return $this->redirectToNextPage();
    }

    protected function tryToRestoreEntity($entity) {
        $controller = $this->controller;
        try {
            $savedSuccessfully = $controller->repository->restore($entity);
        } catch (UnableToSaveEntityException $e) {
            $savedSuccessfully = false;
        }
        return $savedSuccessfully;
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
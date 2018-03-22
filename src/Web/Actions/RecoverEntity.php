<?php

namespace PHPKitchen\Domain\Web\Actions;

use PHPKitchen\Domain\Contracts\RecoverableRepository;
use PHPKitchen\Domain\Exceptions\UnableToSaveEntityException;
use PHPKitchen\Domain\Web\Base\Action;
use PHPKitchen\Domain\Web\Mixins\ModelSearching;

/**
 * Represents entity recovering process.
 *
 * @package PHPKitchen\Domain\Web\Actions
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
class RecoverEntity extends Action {
    use ModelSearching;
    public $failedToRecoverFlashMessage = 'Unable to recover entity';
    public $successfullyRecoveredFlashMessage = 'Entity successfully recovered';
    public $redirectUrl;
    public $recoveredListFieldName = 'restored-ids';

    public function init() {
        $this->setViewFileIfNotSetTo('list');
    }

    public function run($id = null) {
        $ids = ($id) ? [$id] : $this->serviceLocator->request->post($this->recoveredListFieldName, []);

        $savedResults = [];
        foreach ($ids as $id) {
            $entity = $this->findEntityByIdentifierOrFail($id);
            $savedResults[] = $this->tryToRecoverEntity($entity);
        }

        $savedNotSuccessfully = array_filter($savedResults, function ($value) {
            return !$value;
        });
        if ($savedNotSuccessfully) {
            $this->addErrorFlash($this->failedToRecoverFlashMessage);
        } else {
            $this->addSuccessFlash($this->successfullyRecoveredFlashMessage);
        }

        return $this->redirectToNextPage();
    }

    protected function tryToRecoverEntity($entity) {
        $repository = $this->repository;
        try {
            if ($repository instanceof RecoverableRepository) {
                $savedSuccessfully = $repository->recover($entity);
            } else {
                $savedSuccessfully = false;
            }
        } catch (UnableToSaveEntityException $e) {
            $savedSuccessfully = false;
        }

        return $savedSuccessfully;
    }

    // @todo fix duplicate with EntityModificationAction
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
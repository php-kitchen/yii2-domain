<?php

namespace PHPKitchen\Domain\Web\Base\Actions;

use PHPKitchen\DI\Contracts\ContainerAware;
use PHPKitchen\DI\Contracts\ServiceLocatorAware;
use PHPKitchen\DI\Mixins\ContainerAccess;
use PHPKitchen\DI\Mixins\ServiceLocatorAccess;
use PHPKitchen\Domain\DB\EntitiesRepository;
use PHPKitchen\Domain\Web\Contracts\RepositoryAware;
use yii\base\InvalidArgumentException;
use yii\helpers\Inflector;

/**
 * Represents
 *
 * @property \PHPKitchen\Domain\Contracts\EntityCrudController|\yii\web\Controller $controller
 * @property \yii\web\Request $request
 * @property \yii\web\Session $session
 * @property \PHPKitchen\Domain\DB\EntitiesRepository $repository
 *
 * @package PHPKitchen\Domain\Web\Base
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class Action extends \yii\base\Action implements ServiceLocatorAware, ContainerAware , RepositoryAware {
    use ServiceLocatorAccess;
    use ContainerAccess;
    /**
     * @var string name of the view, which should be rendered
     */
    public $viewFile;
    /**
     * @var callable callback that prepares params for a view. Use it to extend default view params list.
     */
    public $prepareViewParams;
    public $useFlashMessages = true;
    public $successFlashMessageKey = 'success';
    public $errorFlashMessageKey = 'error';
    private $_repository;

    /**
     * Checks whether action with specified ID exists in owner controller.
     *
     * @param string $id action ID.
     *
     * @return boolean whether action exists or not.
     */
    protected function isActionExistsInController($id) {
        $inlineActionMethodName = 'action' . Inflector::camelize($id);
        if (method_exists($this->controller, $inlineActionMethodName)) {
            return true;
        }
        if (array_key_exists($id, $this->controller->actions())) {
            return true;
        }

        return false;
    }

    public function addErrorFlash($message) {
        $this->setFlash([$this->errorFlashMessageKey => $message]);
    }

    public function addSuccessFlash($message) {
        $this->setFlash([$this->successFlashMessageKey => $message]);
    }

    protected function setViewFileIfNotSetTo($file) {
        $this->viewFile = isset($this->viewFile) ? $this->viewFile : $file;
    }

    /**
     * Sets a flash message.
     *
     * @param string|array|null $message flash message(s) to be set.
     * If plain string is passed, it will be used as a message with the key 'success'.
     * You may specify multiple messages as an array, if element name is not integer, it will be used as a key,
     * otherwise 'success' will be used as key.
     * If empty value passed, no flash will be set.
     * Particular message value can be a PHP callback, which should return actual message. Such callback, should
     * have following signature:
     *
     * ```php
     * function (array $params) {
     *     // return string
     * }
     * ```
     *
     * @param array $params extra params for the message parsing in format: key => value.
     */
    public function setFlash($message, $params = []) {
        if (!$this->useFlashMessages || empty($message)) {
            return;
        }
        $session = $this->serviceLocator->session;
        foreach ((array)$message as $key => $value) {
            if (is_scalar($value)) {
                $value = preg_replace_callback("/{(\\w+)}/", function ($matches) use ($params) {
                    $paramName = $matches[1];

                    return isset($params[$paramName]) ? $params[$paramName] : $paramName;
                }, $value);
            } else {
                $value = call_user_func($value, $params);
            }
            if (is_int($key)) {
                $session->setFlash($this->successFlashMessageKey, $value);
            } else {
                $session->setFlash($key, $value);
            }
        }
    }

    protected function renderViewFile($params) {
        if (is_callable($this->prepareViewParams)) {
            $params = call_user_func($this->prepareViewParams, $params, $this);
        }

        return $this->controller->render($this->viewFile, $params);
    }

    /**
     * @return mixed|\yii\console\Request|\yii\web\Request
     */
    protected function getRequest() {
        return $this->serviceLocator->request;
    }

    /**
     * @return \yii\web\Session
     */
    protected function getSession() {
        return $this->serviceLocator->session;
    }

    public function getRepository(): EntitiesRepository {
        if (null === $this->_repository) {
            // fallback to support old approach with defining repositories in controllers
            $this->_repository = $this->controller->repository ?? null;
        }

        return $this->_repository;
    }

    public function setRepository($repository): void {
        if ($this->isObjectValidRepository($repository)) {
            $this->_repository = $repository;
        } else {
            $this->createAndSetRepositoryFromDefinition($repository);
        }
    }

    protected function createAndSetRepositoryFromDefinition($definition): void {
        $repository = $this->container->create($definition);
        if (!$this->isObjectValidRepository($repository)) {
            throw new InvalidArgumentException('Repository should be an instance of ' . EntitiesRepository::class);
        }
        $this->_repository = $repository;
    }

    protected function isObjectValidRepository($object) {
        return is_object($object) && $object instanceof EntitiesRepository;
    }
}
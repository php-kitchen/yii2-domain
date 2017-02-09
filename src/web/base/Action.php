<?php

namespace dekey\domain\web\base;

use dekey\di\contracts\ContainerAware;
use dekey\di\contracts\ServiceLocatorAware;
use dekey\di\mixins\ContainerAccess;
use dekey\di\mixins\ServiceLocatorAccess;
use yii\helpers\Inflector;

/**
 * Represents
 *
 * @property \dekey\domain\contracts\EntityCrudController|\yii\web\Controller $controller
 * @property \yii\web\Request $request
 * @property \yii\web\Session $session
 *
 * @package dekey\domain\web
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class Action extends \yii\base\Action implements ServiceLocatorAware, ContainerAware {
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

    /**
     * Checks whether action with specified ID exists in owner controller.
     *
     * @param string $id action ID.
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
}
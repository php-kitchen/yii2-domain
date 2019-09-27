<?php

namespace PHPKitchen\Domain\Web\Base\Mixins;

/**
 * Mixin that provides properties and methods to work with session messages
 *
 * Own properties:
 * @property \yii\web\Session $session
 *
 * Globally available properties:
 * @property \PHPKitchen\DI\Container $container
 * @property \yii\di\ServiceLocator|\yii\base\Application $serviceLocator
 *
 * Parent properties:
 * @property \PHPKitchen\Domain\Contracts\EntityCrudController|\yii\web\Controller $controller
 *
 * @package PHPKitchen\Domain\Web\Base\Mixins
 */
trait SessionMessagesManagement {
    public $useFlashMessages = true;
    public $successFlashMessageKey = 'success';
    public $errorFlashMessageKey = 'error';


    public function addErrorFlash($message): void {
        $this->setFlash([$this->errorFlashMessageKey => $message]);
    }

    public function addSuccessFlash($message): void {
        $this->setFlash([$this->successFlashMessageKey => $message]);
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
    public function setFlash($message, $params = []): void {
        if (!$this->useFlashMessages || empty($message)) {
            return;
        }
        $session = $this->session;
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

    /**
     * @return \yii\web\Session
     */
    protected function getSession() {
        return $this->serviceLocator->session;
    }

}
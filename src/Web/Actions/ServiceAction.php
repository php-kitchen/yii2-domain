<?php

namespace PHPKitchen\Domain\Web\Actions;

use PHPKitchen\Domain\Web\Base\Actions\Action;
use yii\base\InvalidConfigException;

/**
 * Represents a base class for actions that utilize services to
 *
 * Own properties:
 *
 * @property object $b service class object
 *
 * @package PHPKitchen\Domain\Web\Actions
 * @author Dima Kolodko <prowwid@gmail.com>
 */
abstract class ServiceAction extends Action {
    /**
     * @var object a service class object
     */
    private $_service;

    public function getService() {
        if (!is_object($this->_service)) {
            $this->initService();
        }

        return $this->_service;
    }

    public function setService($service): void {
        if (!is_object($service) && (!class_exists($service) || !$this->container->has($service))) {
            throw new InvalidConfigException("Service must be an object or container definition");
        }
        $this->_service = $service;
    }

    protected function initService(): void {
        $this->_service = $this->container->get($this->_service);
    }
}
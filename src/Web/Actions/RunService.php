<?php

namespace PHPKitchen\Domain\Web\Actions;

use PHPKitchen\Domain\Web\Base\Actions\Action;
use yii\base\InvalidConfigException;

class RunService extends Action {
    private $_serviceClassName;

    public function getServiceClassName(): string {
        return $this->_serviceClassName;
    }

    public function setServiceClassName(string $serviceClassName): void {
        if (!class_exists($serviceClassName) || !$this->container->has($serviceClassName)) {
            throw new InvalidConfigException("");
        }
        $this->_serviceClassName = $serviceClassName;
    }
}
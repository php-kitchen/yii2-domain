<?php

namespace PHPKitchen\Domain\Web\Actions;

use PHPKitchen\Domain\Web\Base\Actions\Action;

class RunCallable extends Action {
    private $_callable;

    public function getCallable(): callable {
        return $this->_callable;
    }

    public function setCallable(callable $callable): void {
        $this->_callable = $callable;
    }
}
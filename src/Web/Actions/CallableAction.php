<?php

namespace PHPKitchen\Domain\Web\Actions;

use PHPKitchen\Domain\Web\Base\Actions\Action;

/**
 * Represents a base class for actions that invoke callable's like strategies to process the request.
 *
 * Action provide a property {@link callable} and a method {@link runCallable} to invoke it passing action as an argument.
 *
 * Own properties:
 *
 * @property callable $callable a callable that being executed by the action.
 *
 * @package PHPKitchen\Domain\Web\Actions
 * @author Dima Kolodko <prowwid@gmail.com>
 */
abstract class CallableAction extends Action {
    private $_callable;

    protected function runCallable() {
        call_user_func($this->callable, $this);
    }

    public function getCallable(): callable {
        return $this->_callable;
    }

    public function setCallable(callable $callable): void {
        $this->_callable = $callable;
    }
}
<?php
namespace dekey\domain\base;

use dekey\domain\contracts;
use yii\base\Event;

/**
 * Represents
 *
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
abstract class Strategy extends Component implements contracts\Strategy {
    abstract protected function executeCallAction();

    /**
     * @param array ...$params algorithm params.
     * @return mixed strategy result.
     */
    public function __invoke(...$params) {
        return $this->call(...$params);
    }

    public function call() {
        $this->executeBeforeCall();

        $result = $this->executeCallAction();

        $this->executeAfterCall();

        return $result;
    }

    protected function executeBeforeCall() {
        $this->trigger(self::EVENT_BEFORE_CALL, new Event());
    }

    protected function executeAfterCall() {
        $this->trigger(self::EVENT_AFTER_CALL, new Event());
    }
}
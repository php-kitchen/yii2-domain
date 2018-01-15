<?php

namespace PHPKitchen\Domain\log;

use yii\log\Logger as BaseLogger;

/**
 * Extends base logger to provide ability to log messages with trace for any specific use case event if {@link traceLevel}
 * is disabled.
 * Such function useful for exception logging as on production trace level is disabled but for exceptions it's very important to include
 * trace level to message.
 *
 * @package PHPKitchen\Domain\log
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class Logger extends BaseLogger {
    public $defaultTraceLevel = 7;

    public function logWithTrace($message, $level, $category = 'application') {
        if (!$this->traceLevel && $this->defaultTraceLevel) {
            $oldTraceLevel = $this->traceLevel;
            $this->traceLevel = $this->defaultTraceLevel;
            parent::log($message, $level, $category);
            $this->traceLevel = $oldTraceLevel;
        } else {
            parent::log($message, $level, $category);
        }
    }
}
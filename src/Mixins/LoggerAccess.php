<?php

namespace PHPKitchen\Domain\Mixins;

/**
 * Trait provides functions for logging and profiling.
 *
 * @see \PHPKitchen\Domain\Contracts\LoggerAware
 *
 * @package PHPKitchen\Domain\Mixins
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
trait LoggerAccess {
    /**
     * @var \yii\log\Logger logger component.
     */
    protected static $_logger;

    /**
     * Logs a message with the given type and category.
     * If [[traceLevel]] is greater than 0, additional call stack information about
     * the application code will be logged as well.
     *
     * @param string|array $message the message to be logged. This can be a simple string or a more
     * complex data structure that will be handled by a [[Target|log target]].
     * @param integer $level the level of the message. This must be one of the following:
     * `Logger::LEVEL_ERROR`, `Logger::LEVEL_WARNING`, `Logger::LEVEL_INFO`, `Logger::LEVEL_TRACE`,
     * `Logger::LEVEL_PROFILE_BEGIN`, `Logger::LEVEL_PROFILE_END`.
     * @param string $category the category of the message.
     */
    public function log($message, $level, $category = '') {
        if (empty($category)) {
            $category = static::class;
        }
        static::getLogger()->log($message, $level, $category);
    }

    /**
     * Logs an informative message.
     * An informative message is typically logged by an application to keep record of
     * something important (e.g. an administrator logs in).
     *
     * @param string $message the message to be logged.
     * @param string $category the category of the message.
     */
    public function logInfo($message, $category = '') {
        static::log($message, \yii\log\Logger::LEVEL_INFO, $category);
    }

    /**
     * Logs a warning message.
     * A warning message is typically logged when an error occurs while the execution
     * can still continue.
     *
     * @param string $message the message to be logged.
     * @param string $category the category of the message.
     */
    public function logWarning($message, $category = '') {
        static::log($message, \yii\log\Logger::LEVEL_WARNING, $category);
    }

    /**
     * Logs an error message.
     * An error message is typically logged when an unrecoverable error occurs
     * during the execution of an application.
     *
     * @param string $message the message to be logged.
     * @param string $category the category of the message.
     */
    public function logError($message, $category = '') {
        static::log($message, \yii\log\Logger::LEVEL_ERROR, $category);
    }

    /**
     * Marks the beginning of a code block for profiling.
     * This has to be matched with a call to [[endProfile]] with the same category name.
     * The begin- and end- calls must also be properly nested. For example,
     *
     * ```php
     * $this->beginProfile('block1');
     * // some code to be profiled
     *     $this->beginProfile('block2');
     *     // some other code to be profiled
     *     $this->endProfile('block2');
     * $this->endProfile('block1');
     * ```
     *
     * @param string $token token for the code block
     * @param string $category the category of this log message
     * @see endProfile()
     */
    public function beginProfile($token, $category = '') {
        static::getLogger()->log($token, \yii\log\Logger::LEVEL_PROFILE_BEGIN, $category);
    }

    /**
     * Marks the end of a code block for profiling.
     * This has to be matched with a previous call to [[beginProfile]] with the same category name.
     *
     * @param string $token token for the code block
     * @param string $category the category of this log message
     * @see beginProfile()
     */
    public function endProfile($token, $category = '') {
        static::getLogger()->log($token, \yii\log\Logger::LEVEL_PROFILE_END, $category);
    }

    /**
     * Logs a trace message.
     * Trace messages are logged mainly for development purpose to see
     * the execution work flow of some code.
     *
     * @param string $message the message to be logged.
     * @param string $category the category of the message.
     */
    public function trace($message, $category = '') {
        static::getLogger()->log($message, \yii\log\Logger::LEVEL_TRACE, $category);
    }

    /**
     * @return \yii\log\Logger
     */
    protected function getLogger() {
        if (!isset(static::$_logger)) {
            static::$_logger = \Yii::$app->log->getLogger();
        }
        return static::$_logger;
    }
}
<?php

namespace PHPKitchen\Domain\Contracts;

/**
 * Defines interfaces of classes aware of logger.
 *
 * @package PHPKitchen\Domain\Contracts
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
interface LoggerAware {
    /**
     * Logs an informative message.
     * An informative message is typically logged by an application to keep record of
     * something important (e.g. an administrator logs in).
     *
     * @param string $message the message to be logged.
     * @param string $category the category of the message.
     */
    public function logInfo($message, $category = '');

    /**
     * Logs a warning message.
     * A warning message is typically logged when an error occurs while the execution
     * can still continue.
     *
     * @param string $message the message to be logged.
     * @param string $category the category of the message.
     */
    public function logWarning($message, $category = '');

    /**
     * Logs an error message.
     * An error message is typically logged when an unrecoverable error occurs
     * during the execution of an application.
     *
     * @param string $message the message to be logged.
     * @param string $category the category of the message.
     */
    public function logError($message, $category = '');

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
    public function beginProfile($token, $category = '');

    /**
     * Marks the end of a code block for profiling.
     * This has to be matched with a previous call to [[beginProfile]] with the same category name.
     *
     * @param string $token token for the code block
     * @param string $category the category of this log message
     * @see beginProfile()
     */
    public function endProfile($token, $category = '');

    /**
     * Logs a trace message.
     * Trace messages are logged mainly for development purpose to see
     * the execution work flow of some code.
     *
     * @param string $message the message to be logged.
     * @param string $category the category of the message.
     */
    public function trace($message, $category = '');
}
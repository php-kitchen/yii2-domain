<?php

namespace PHPKitchen\Domain\Mixins;

/**
 * Represents
 *
 * @package PHPKitchen\Domain\Mixins
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
trait StaticSelfAccess {
    /**
     * @return \PHPKitchen\Domain\Base\Component[]
     */
    protected static $_instances = [];

    /**
     * @return $this
     */
    public static function getInstance() {
        if (!isset(static::$_instances[static::class])) {
            static::initializeInstance();
        }
        return static::$_instances[static::class];
    }

    protected static function initializeInstance() {
        static::$_instances[static::class] = \Yii::$container->create(static::class);
    }
}
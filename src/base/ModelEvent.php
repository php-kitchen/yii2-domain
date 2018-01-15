<?php

namespace PHPKitchen\Domain\base;

use PHPKitchen\Domain\contracts\DomainEntity;
use yii\base\Event;

/**
 * Represents
 *
 * @package PHPKitchen\Domain\base
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class ModelEvent extends Event {
    /**
     * @var DomainEntity
     */
    protected $_entity;
    protected $_valid = true;

    public function __construct(DomainEntity $entity, $config = []) {
        $this->_entity = $entity;
        parent::__construct($config);
    }

    public function isValid() {
        return $this->_valid;
    }

    public function fail() {
        $this->_valid = false;
    }

    public function getEntity() {
        return $this->_entity;
    }
}
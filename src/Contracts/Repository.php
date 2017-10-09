<?php

namespace PHPKitchen\Domain\Contracts;

/**
 * Represents
 *
 * @package PHPKitchen\Domain\Contracts
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
interface Repository {
    const EVENT_BEFORE_SAVE = 'beforeSave';
    const EVENT_AFTER_SAVE = 'afterSave';
    const EVENT_BEFORE_ADD = 'beforeAdd';
    const EVENT_BEFORE_UPDATE = 'beforeUpdate';
    const AFTER_BEFORE_ADD = 'afterAdd';
    const EVENT_AFTER_UPDATE = 'afterUpdate';
    const EVENT_BEFORE_DELETE = 'beforeDelete';
    const EVENT_AFTER_DELETE = 'afterDelete';

    public function validateAndSave(DomainEntity $entity, $attributes = null);

    public function saveWithoutValidation(DomainEntity $entity, $attributes = null);

    public function delete(DomainEntity $entity);

    public function validate(DomainEntity $entity);

    public function findOneWithPk($pk);

    public function findAll();

    public function each();

    public function find();

    public function createNewEntity();

    public function getEntitiesProvider();
}
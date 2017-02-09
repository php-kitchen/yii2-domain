<?php

namespace dekey\domain\contracts;

/**
 * Represents
 *
 * @package dekey\domain\contracts
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
interface Repository {
    const EVENT_BEFORE_SAVE = 'beforeSave';
    const EVENT_AFTER_SAVE = 'beforeSave';
    const EVENT_BEFORE_DELETE = 'beforeSave';
    const EVENT_AFTER_DELETE = 'beforeSave';

    public function validateAndSave(DomainEntity $entity, $attributes = null);

    public function saveWithoutValidation(DomainEntity $entity, $attributes = null);

    public function delete(DomainEntity $entity);

    public function validate(DomainEntity $entity);

    public function findOneWithPk($pk);

    public function findAll();

    public function each();

    public function find();

    public function createNewEntity();

    /**
     * @param \dekey\domain\db\Record $record
     * @return DomainEntity
     */
    public function createEntityFromSource(EntityDataSource $record);

    public function getEntitiesProvider();
}
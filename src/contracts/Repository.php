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

    /**
     * @param Record $record
     * @return DomainEntity
     */
    public function createEntityFromRecord(Record $record);

    public function createRecordsDataProvider();
}
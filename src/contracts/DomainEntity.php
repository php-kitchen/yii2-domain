<?php

namespace dekey\domain\contracts;

/**
 * Represents domain entity.
 * This class should be used in chain with AR. AR plays role of a data source.
 *
 * @property EntityDataSource|Record $dataSource
 *
 * @package dekey\domain
 * @author Dmitry Kolodko <dangel@quartsoft.com>
 */
interface DomainEntity {
    public function save($runValidation = true, $attributeNames = null);

    public function deleteUsingTransaction();

    public function delete();

    /**
     * @return \dekey\domain\contracts\EntityDataSource entity data source as an AR.
     */
    public function getDataSource();
}
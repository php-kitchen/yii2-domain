<?php

namespace PHPKitchen\Domain\DB\Mixins;

use PHPKitchen\Domain\Contracts\DomainEntity;

/**
 * Represents recovered entities functionality for DB repository.
 *
 * @property \PHPKitchen\Domain\DB\Base\Repository $this
 *
 * @package PHPKitchen\Domain\DB\Mixins
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
class RecoveredRepositoryFunctions {
    /**
     * @param DomainEntity $entity
     *
     * @return bool result.
     */
    public function restore(DomainEntity $entity) {
        $result = false;
        if ($this->triggerModelEvent(self::EVENT_BEFORE_DELETE, $entity)) {
            $dataSource = $entity->getDataMapper()->getDataSource();
            if ($dataSource->hasMethod('restore')) {
                $result = $dataSource->restore();
            }
        }
        if ($result) {
            $this->triggerModelEvent(self::EVENT_AFTER_DELETE, $entity);
        }

        return $result;
    }
}
<?php

namespace PHPKitchen\Domain\DB\Mixins;

use PHPKitchen\Domain\Contracts\DomainEntity;

/**
 * Represents mixin designed for recovering entities that was previously deleted.
 *
 * @mixin \PHPKitchen\Domain\DB\Base\Repository
 *
 * @package PHPKitchen\Domain\DB\Mixins
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
trait EntityRecovering {
    /**
     * @param DomainEntity $entity
     *
     * @return bool result.
     */
    public function recover(DomainEntity $entity) {
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
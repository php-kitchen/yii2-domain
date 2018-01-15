<?php

namespace PHPKitchen\Examples\User;

use PHPKitchen\Domain\db\EntitiesRepository;

/**
 * Represents users repository.
 *
 * @package PHPKitchen\Examples\User
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class UserRepository extends EntitiesRepository {
    public function init() {
        $this->on(self::EVENT_BEFORE_SAVE, function () {
            $this->log('here we can handle events');
        });
    }
}
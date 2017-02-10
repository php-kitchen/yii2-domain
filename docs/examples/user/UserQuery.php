<?php

namespace DeKey\Examples\User;

use dekey\domain\db\RecordQuery;

/**
 * Represents user DB record query.
 *
 * @package DeKey\Examples\User
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class UserQuery extends RecordQuery {
    public function active() {
        return $this->andWhere('status=:status', ['status' => UserEntity::STATUS_ACTIVE]);
    }

    public function inactive() {
        return $this->andWhere('status=:status', ['status' => UserEntity::STATUS_INACTIVE]);
    }
}
<?php

namespace DeKey\Examples\User;

use PHPKitchen\Domain\DB\Record;

/**
 * Represents user profile record in the DB.
 *
 * Attributes:
 *
 * @property int $fullName
 * @property int $dateOfBirth
 *
 * @package DeKey\Examples\User
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class ProfileRecord extends Record {
    /**
     * @override
     * @inheritdoc
     */
    public static function tableName() {
        return 'UserProfile';
    }

    /**
     * @override
     * @inheritdoc
     */
    public function rules() {
        return [
            [
                [
                    'id',
                    'userId',
                    'fullName',
                    'dateOfBirth',
                ],
                'required',
            ],
        ];
    }
}
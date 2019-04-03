<?php

namespace PHPKitchen\Domain\Dev\App\Models;

class User extends \yii\base\BaseObject implements \yii\web\IdentityInterface {
    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id) {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null) {
        return null;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     *
     * @return static|null
     */
    public static function findByUsername($username) {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getId() {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey() {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey) {
        return false;
    }
}

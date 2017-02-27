<?php

namespace dekey\domain\web\mixins;

use dekey\domain\contracts\Repository;
use yii\base\InvalidConfigException;
use yii\web\NotFoundHttpException;

/**
 * Represents
 *
 * @property \dekey\domain\db\EntitiesRepository $repository
 *
 * @mixin \dekey\di\mixins\ServiceLocatorAccess
 * @mixin \dekey\di\mixins\ContainerAccess
 *
 * @package dekey\domain\web\mixins
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
trait EntityManagement {
    public $notFoundModelExceptionMessage = 'Requested page does not exist!';
    public $notFoundModelExceptionClassName = NotFoundHttpException::class;
    /**
     * @var \dekey\domain\db\EntitiesRepository
     */
    private $_repository;

    public function findEntityByPk($pk) {
        $entity = $this->getRepository()->find()->oneWithPk($pk);
        if (null === $entity) {
            /**
             * @var NotFoundHttpException $exception
             */
            $exception = $this->getContainer()
                ->create($this->notFoundModelExceptionClassName, [$this->notFoundModelExceptionMessage]);
            throw $exception;
        }
        return $entity;
    }

    public function getRepository() {
        if ($this->_repository === null) {
            throw new InvalidConfigException('Repository should be set in ' . static::class);
        }
        return $this->_repository;
    }

    public function setRepository($repository) {
        if (is_string($repository) || is_array($repository)) {
            $this->_repository = $this->container->create($repository);
        } elseif (is_object($repository) && $repository instanceof Repository) {
            $this->_repository = $repository;
        } else {
            throw new InvalidConfigException('Repository should be a valid container config or an instance of ' . Repository::class);
        }
    }
}
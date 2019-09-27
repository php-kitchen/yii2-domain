<?php


namespace PHPKitchen\Domain\Web\Base\Mixins;


use PHPKitchen\Domain\DB\EntitiesRepository;
use yii\base\InvalidArgumentException;

/**
 * Mixin that provides properties and methods to work with DB repository.
 *
 * Own properties:
 * @property \PHPKitchen\Domain\DB\EntitiesRepository $repository
 *
 * Globally available properties:
 * @property \PHPKitchen\DI\Container $container
 *
 * Parent properties:
 * @property \PHPKitchen\Domain\Contracts\EntityCrudController|\yii\web\Controller $controller
 *
 * @package PHPKitchen\Domain\Web\Base\Mixins
 */
trait RepositoryAccess {
    /**
     * @var \PHPKitchen\Domain\DB\EntitiesRepository DB repository.
     */
    private $_repository;

    public function getRepository(): EntitiesRepository {
        if (null === $this->_repository) {
            // fallback to support old approach with defining repositories in controllers
            $this->_repository = $this->controller->repository ?? null;
        }

        return $this->_repository;
    }

    public function setRepository($repository): void {
        if ($this->isObjectValidRepository($repository)) {
            $this->_repository = $repository;
        } else {
            $this->createAndSetRepositoryFromDefinition($repository);
        }
    }

    protected function createAndSetRepositoryFromDefinition($definition): void {
        $repository = $this->container->create($definition);
        if (!$this->isObjectValidRepository($repository)) {
            throw new InvalidArgumentException('Repository should be an instance of ' . EntitiesRepository::class);
        }
        $this->_repository = $repository;
    }

    protected function isObjectValidRepository($object): bool {
        return is_object($object) && $object instanceof EntitiesRepository;
    }
}
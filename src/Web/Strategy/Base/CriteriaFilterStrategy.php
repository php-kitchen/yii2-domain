<?php

namespace PHPKitchen\Domain\Web\Strategy\Base;

use PHPKitchen\DI\Contracts;
use PHPKitchen\DI\Mixins\ContainerAccess;
use PHPKitchen\Domain\DB\EntitiesRepository;
use yii\base\InvalidConfigException;

/**
 * Represents
 *
 * @author Dmitry Kolodko <prowwid@gmail.com>
 * @since 1.0
 */
abstract class CriteriaFilterStrategy implements Contracts\ContainerAware {
    use ContainerAccess;
    protected $steps;
    protected $repository;

    /**
     * @param \PHPKitchen\Domain\Contracts\Specification $finder
     *
     * @return mixed
     *
     * @since 1.0
     */
    abstract protected function searchBy($finder);

    public function __construct($steps, Contracts\Repository $repository) {
        $this->steps = $steps;
        $this->repository = $repository;
    }

    public function do() {
        $finder = $this->repository->find();
        foreach ($this->steps as $step) {
            if (is_string($step) || is_array($step)) {
                $stepObject = $this->container->create($step);
            } elseif (is_callable($step)) {
                $stepObject = $step;
            } elseif (is_object($step)) {
                $stepObject = $step;
            } else {
                throw new InvalidConfigException('Invalid strategy');
            }
            $stepObject($finder);
        }
    }
}
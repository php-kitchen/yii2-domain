<?php

namespace PHPKitchen\Domain\Mixins;

/**
 * Represents
 *
 * @mixin \PHPKitchen\DI\Mixins\ContainerAccess
 *
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
trait StrategiesComposingAlgorithm {
    /**
     * @var \PHPKitchen\Domain\Base\Strategy[]|array
     */
    private $_chainedStrategies;

    public function executeCallAction() {
        $chainedStrategies = $this->getChainedStrategies();
        $container = $this->container;
        foreach ($chainedStrategies as $key => $chainedStrategy) {
            if (!is_object($chainedStrategy)) {
                $chainedStrategy = $container->create($chainedStrategy, $this->getStrategyConstructorArguments());
            }
            $chainedStrategy->call();
        }
    }

    protected function getStrategyConstructorArguments() {
        return [];
    }

    public function getChainedStrategies() {
        return $this->_chainedStrategies;
    }

    public function setChainedStrategies($chainedStrategies) {
        $this->_chainedStrategies = $chainedStrategies;
    }
}
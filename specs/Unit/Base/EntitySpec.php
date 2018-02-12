<?php

namespace PHPKitchen\Domain\Specs\Unit\Base;

use PHPKitchen\Domain\Specs\Base\Spec;
use PHPKitchen\Domain\Specs\Unit\Stubs\Models\Dummy\DummyEntity;
use PHPKitchen\Domain\Specs\Unit\Stubs\Models\Dummy\DummyRepository;

/**
 * Specification of {@link PHPKitchen\Domain\Base\Entity}
 *
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class EntitySpec extends Spec {
    /**
     * @test
     */
    public function phpEmptyOnNullAttributeBehavior() {
        $entity = $this->createEmptyEntity();
        $I = $this->tester;

        $I->expectThat('if data source attribute is null, then PHP constructions `isset` and `empty` can see it');
        $I->seeBool(empty($entity->id))
          ->isTrue();
        $I->seeBool(isset($entity->id))
          ->isFalse();
    }

    /**
     * @test
     */
    public function phpEmptyOnFilledAttributeBehavior() {
        $entity = $this->createEmptyEntity();
        $I = $this->tester;

        $entity->id = 1;

        $I->expectThat('if data source attribute is filled with value, then PHP constructions `isset` and `empty` can see it');
        $I->seeBool(empty($entity->id))
          ->isFalse();
        $I->seeBool(isset($entity->id))
          ->isTrue();
    }

    private function createEmptyEntity(): DummyEntity {
        $repository = new DummyRepository();

        return $repository->createNewEntity();
    }
}
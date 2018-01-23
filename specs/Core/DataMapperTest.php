<?php

namespace PHPKitchen\Domain\Specs\Core;

use PHPKitchen\Domain\Base\DataMapper;
use PHPKitchen\Domain\Specs\Base\Spec;
use PHPKitchen\Domain\Specs\Stubs\Models\Dummy\DummyEntity;
use PHPKitchen\Domain\Specs\Stubs\Models\Dummy\DummyRecord;
use PHPKitchen\Domain\Specs\Stubs\Models\Tmux\TmuxRecord;
use PHPKitchen\Domain\Specs\Stubs\Models\Tmux\TmuxRepository;

/**
 * Represents unit test of {@link DataMapper}
 *
 * @coversDefaultClass \PHPKitchen\Domain\base\DataMapper
 *
 * @package tests\core
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class DataMapperTest extends Spec {
    const DUMMY_RECORD_ID = 123;
    protected $record;

    /**
     * @test
     */
    public function constructorBehavior() {
        $this->tester
            ->seeObject($this->createMapper())
            ->isInstanceOf(DataMapper::class);
    }

    /**
     * @test
     * @covers ::get
     * @covers ::canGet
     * @covers ::getPropertyFromDataSource
     * @covers ::findRepositoryForRecord
     */
    public function getEntityBehavior() {
        $mapper = $this->createMapper();
        $dummyEntity = $mapper->get('dummy');
        $I = $this->tester;
        $I->describe('trying to get related entity object from data source');

        $I->expectThat('mapper creates entity from given record as data source');
        $I->seeObject($dummyEntity)
          ->isInstanceOf(DummyEntity::class);
        $I->see($dummyEntity->id)
          ->isEqualTo(self::DUMMY_RECORD_ID);
    }

    /**
     * @test
     * @covers ::get
     * @covers ::propertyIsAnArrayOfRecords
     * @covers ::arrayHasOnlyRecords
     */
    public function getEntitiesArrayBehavior() {
        $mapper = $this->createMapper();
        $records = $mapper->get('listOfSelfRecords');
        $I = $this->tester;
        $I->describe('trying to get related entities list from data source');
        $I->expectThat('mapper creates a list of entities from given records as data source');
        $I->seeArray($records)
          ->isNotEmpty()
          ->isEqualTo($this->getEntitiesListFromRecord());
    }

    protected function getEntitiesListFromRecord() {
        $tmuxRepository = new TmuxRepository();
        $tmuxEntity = $tmuxRepository->createEntityFromSource($this->record);

        return [
            $tmuxEntity,
            $tmuxEntity,
            $tmuxEntity,
        ];
    }

    /**
     * @test
     * @covers ::get
     */
    public function getArrayOfMixedValuesBehavior() {
        $mapper = $this->createMapper();
        $records = $mapper->get('listOfMixedValues');

        $I = $this->tester;
        $I->describe('trying to get list of mixed values from data source');
        $I->expectThat('mapper creates a list of entities from given records as data source');
        $I->seeArray($records)
          ->isEqualTo($this->getListOfMixedValuesFromRecord());
    }

    protected function getListOfMixedValuesFromRecord() {
        return [
            $this->record,
            'value',
            1,
        ];
    }

    protected function createMapper(): DataMapper {
        $tmuxRecord = new TmuxRecord();
        $dummyRecord = new DummyRecord();
        $dummyRecord->id = self::DUMMY_RECORD_ID;
        $tmuxRecord->dummyRecord = $dummyRecord;
        $this->record = $tmuxRecord;

        return new DataMapper($tmuxRecord);
    }
}
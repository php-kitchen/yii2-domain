<?php

namespace tests\core;

use dekey\domain\base\DataMapper;
use tests\base\TestCase;
use tests\stubs\models\dummy\DummyEntity;
use tests\stubs\models\dummy\DummyRecord;
use tests\stubs\models\tmux\TmuxRecord;
use tests\stubs\models\tmux\TmuxRepository;

/**
 * Represents unit test of {@link DataMapper}
 *
 * @coversDefaultClass \dekey\domain\base\DataMapper
 *
 * @package tests\core
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class DataMapperTest extends TestCase {
    const DUMMY_RECORD_ID = 123;
    protected $record;

    public function testConstruct() {
        $this->createMapper();
    }

    /**
     * @covers ::get
     * @covers ::canGet
     * @covers ::getPropertyFromDataSource
     * @covers ::findRepositoryForRecord
     */
    public function testGetEntity() {
        $mapper = $this->createMapper();
        $dummyEntity = $mapper->get('dummy');

        $this->tester->checksScenario('trying to get related entity object from data source')
            ->expectsThat('mapper creates entity from given record as data source')
            ->object($dummyEntity)
            ->isInstanceOf(DummyEntity::class)
            ->and()
            ->valueOf($dummyEntity->id)
            ->isEqualTo(self::DUMMY_RECORD_ID);
    }

    /**
     * @covers ::get
     * @covers ::propertyIsAnArrayOfRecords
     * @covers ::arrayHasOnlyRecords
     */
    public function testGetEntitiesArray() {
        $mapper = $this->createMapper();
        $records = $mapper->get('listOfSelfRecords');

        $this->tester->checksScenario('trying to get related entities list from data source')
            ->expectsThat('mapper creates a list of entities from given records as data source')
            ->theArray($records)
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
     * @covers ::get
     */
    public function testGetArrayOfMixedValues() {
        $mapper = $this->createMapper();
        $records = $mapper->get('listOfMixedValues');

        $this->tester->checksScenario('trying to get list of mixed values from data source')
            ->expectsThat('mapper creates a list of entities from given records as data source')
            ->theArray($records)
            ->isEqualTo($this->getListOfMixedValuesFromRecord());
    }

    protected function getListOfMixedValuesFromRecord() {
        return [
            $this->record,
            'value',
            1,
        ];
    }

    protected function createMapper() {
        $tmuxRecord = new TmuxRecord();
        $dummyRecord = new DummyRecord();
        $dummyRecord->id = self::DUMMY_RECORD_ID;
        $tmuxRecord->dummyRecord = $dummyRecord;
        $this->record = $tmuxRecord;

        return new DataMapper($tmuxRecord);
    }
}
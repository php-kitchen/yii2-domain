<?php

namespace tests\data;

use PHPKitchen\Domain\data\EntitiesProvider;
use tests\base\TestCase;
use tests\stubs\models\dummy\DummyEntity;
use tests\stubs\models\dummy\DummyQuery;
use tests\stubs\models\dummy\DummyRecord;
use tests\stubs\models\dummy\DummyRepository;

/**
 * Unit test for {@link EntitiesProvider}
 *
 * @coversDefaultClass \PHPKitchen\Domain\data\EntitiesProvider
 *
 * @package tests\data
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class EntityProviderTest extends TestCase {
    const STUBBED_RECORDS_COUNT = 5;

    /**
     * @covers ::getModels
     */
    public function testGetRecordsDataAsEntities() {
        $dataProvider = $this->createDatProviderWithStubbedRecordsData();
        $count = $dataProvider->getCount();
        $tester = $this->tester;
        $tester->expectsThat('data provider correctly calculates entities number')
            ->valueOf($count)
            ->isEqualTo(self::STUBBED_RECORDS_COUNT);
        $models = $dataProvider->getModels();
        $tester->expectsThat('data provider correctly calculates entities number')
            ->theArray($models)
            ->countIsEqualToCountOf(self::STUBBED_RECORDS_COUNT);
        foreach ($models as $model) {
            $tester->expectsThat('data provider have converted record to entity')
                ->object($model)
                ->isInstanceOf(DummyEntity::class);
        }
    }
    /**
     * @covers ::getModels
     */
    public function testGetArrayData() {
        $dataProvider = $this->createDatProviderWithStubbedArrayData();
        $count = $dataProvider->getCount();
        $tester = $this->tester;
        $tester->expectsThat('data provider correctly calculates entities number')
            ->valueOf($count)
            ->isEqualTo(self::STUBBED_RECORDS_COUNT);
        $models = $dataProvider->getModels();
        $tester->expectsThat('data provider correctly calculates entities number')
            ->theArray($models)
            ->countIsEqualToCountOf(self::STUBBED_RECORDS_COUNT);
        foreach ($models as $model) {
            $tester->expectsThat('data provider have converted record to entity')
                ->valueOf($model)
                ->isInternalType('array');
        }
    }

    protected function createDatProviderWithStubbedRecordsData() {
        $repository = new DummyRepository();
        $query = new DummyQuery(DummyRecord::class);
        $query->records = [
            new DummyRecord(),
            new DummyRecord(),
            new DummyRecord(),
            new DummyRecord(),
            new DummyRecord(),
        ];
        return new EntitiesProvider([
            'query' => $query,
            'repository' => $repository,
            'pagination' => false,
            'sort' => false,
        ]);
    }

    protected function createDatProviderWithStubbedArrayData() {
        $repository = new DummyRepository();
        $query = new DummyQuery(DummyRecord::class);
        $query->records = [
            ['id' =>1],
            ['id' =>1],
            ['id' =>1],
            ['id' =>1],
            ['id' =>1],
        ];
        return new EntitiesProvider([
            'query' => $query,
            'repository' => $repository,
            'pagination' => false,
            'sort' => false,
        ]);
    }
}
<?php

namespace QuadLayers\WP_Orm\Tests\Collection;

use PHPUnit\Framework\TestCase;
use Brain\Monkey\Functions;
use QuadLayers\WP_Orm\Builder\CollectionRepositoryBuilder;
use QuadLayers\WP_Orm\Repository\CollectionRepository;

class CollectionRepositoryBuilderTestPrimaryKeyString extends TestCase
{
    private string $table = 'test_table';
    private CollectionRepository $repository;

    protected function setUp(): void
    {

        $builder = (new CollectionRepositoryBuilder())
            ->setTable($this->table)
            ->setEntity('\QuadLayers\WP_Orm\Tests\Collection\CollectionEntityTestPrimaryKeyString')
            ->setAutoIncrement(false);

        $this->repository = $builder->getRepository();



        // When get_option is called, return testInput
        Functions\when('get_option')->justReturn([]);
    }

    public function testFindAll()
    {

        $test = [
            [
                'test_id' => 'test1',
                'key1' => 'test',
            ],
            [
                'test_id' => 'test2',
                'key1' => 'test2',
            ],
            [
                'test_id' => 'test9',
                'key1' => 'test3',
            ],
        ];

        // Initialize a variable to keep track of the update_option call count
        $callCount = 0;

        Functions\when('update_option')->alias(
            function ($option, $value) use (&$callCount, $test) {
                // Check if the table is correct
                if ($this->table !== $option) {
                    return false;
                }

                // // Increase the call count
                $callCount++;

                // // Check if the value matches the test values up to the current call count
                if (serialize(array_slice($test, 0, $callCount + 1)) !== serialize($value)) {
                    return false;
                }

                return true;
            }
        );

        foreach ($test as $index => $data) {
            $this->repository->create($data);
        }

        $results = $this->repository->findAll();

        foreach ($results as $index => $entity) {
            $testEntity = array_merge(
                $test[$index],
                ['test_id' => $test[$index]['test_id']]
            );
            $this->assertEquals($entity->getModifiedProperties(), $testEntity);
        }
    }

    public function testDeleteAll()
    {
        Functions\when('delete_option')->justReturn(true);
        $this->repository->deleteAll();
        $this->assertEquals($this->repository->findAll(), null);
    }

    public function testCreate()
    {

        Functions\when('update_option')->justReturn(true);

        $entity = $this->repository->create(['test_id' => 'test1','key1' => 'value1_2_updated']);

        $result = $entity->getModifiedProperties();

        $this->assertEquals($result, ['test_id' => 'test1','key1' => 'value1_2_updated']);
    }

    public function testUpdate()
    {

        Functions\when('update_option')->justReturn(true);

        $entity0 = $this->repository->create(['test_id' => 'test1','key1' => 'value1_2_updated']);
        $entity0 = $this->repository->update('test1', ['key1' => 'value1_2_updated']);

        $result = $entity0->getModifiedProperties();

        $this->assertEquals($result, ['test_id' => 'test1','key1' => 'value1_2_updated']);
    }

    public function testDelete()
    {

        Functions\when('update_option')->justReturn(true);

        $entity0 = $this->repository->create(['test_id' => 'test1']);
        $entity1 = $this->repository->create(['test_id' => 'test2']);
        $entity2 = $this->repository->create(['test_id' => 'test3']);

        $result = $this->repository->delete('test1');

        $this->assertTrue($result);
        $this->assertEquals(null, $this->repository->find('test1'));
        $this->assertEquals($entity1, $this->repository->find('test2'));
        $this->assertEquals($entity2, $this->repository->find('test3'));
    }

    public function testDefaults()
    {
        $entity0 = $this->repository->create(['test_id' => 'test1']);
        $entity1 = $this->repository->create(['test_id' => 'test2']);
        $entity2 = $this->repository->create(['test_id' => 'test3']);

        $results = $this->repository->findAll();

        foreach ($results as $index => $entity) {
            $defaults = $entity->getDefaults();
            $this->assertEquals($defaults, [
                'test_id' => '',
                'key1' => 'default_value_1',
                'key2' =>  'default_value_2',
                'key3' => [
                    'key_3_1' => 'default_value_3',
                    'key_3_2' => 'default_value_4',
                ]
            ]);
        }
    }
}

<?php

namespace QuadLayers\WP_Orm\Tests;

use PHPUnit\Framework\TestCase;
use Brain\Monkey\Functions;
use Brain\Monkey;
use QuadLayers\WP_Orm\Builder\CollectionRepositoryBuilder;
use QuadLayers\WP_Orm\Factory\CollectionFactory;
use QuadLayers\WP_Orm\Repository\CollectionRepository;

class CollectionRepositoryBuilderTest extends TestCase
{
    private array $testInput = [
        [
            'key1' => 'value1_1',
            'key2' => 'value2_1',
        ],
        [
            'key1' => 'value1_2',
            'key2' => 'value2_2',
        ],
        [
            'key1' => 'value1_3',
            'key2' => 'value2_3',
        ],
    ];

    private array $testOutput = [
        [
            'id' => 0,
            'key1' => 'value1_1',
            'key2' => 'value2_1',
        ],
        [
            'id' => 1,
            'key1' => 'value1_2',
            'key2' => 'value2_2',
        ],
        [
            'id' => 2,
            'key1' => 'value1_3',
            'key2' => 'value2_3',
        ],
    ];

    private string $table = 'test_table';
    private CollectionRepository $repository;

    protected function setUp(): void
    {

        $builder = (new CollectionRepositoryBuilder())
            ->setTable($this->table)
            ->setEntity('\QuadLayers\WP_Orm\Tests\CollectionEntityTest')
            ->setPrimaryKey('id');

        $this->repository = $builder->getRepository();

        // Initialize a variable to keep track of the update_option call count
        $callCount = 0;

        Functions\when('update_option')->alias(
            function ($option, $value) use (&$callCount) {
                // Check if the table is correct
                if ($this->table !== $option) {
                    return false;
                }

                // Check if the value matches the test values up to the current call count
                if (serialize(array_slice($this->testOutput, 0, $callCount + 1)) !== serialize($value)) {
                    return false;
                }

                // // Increase the call count
                $callCount++;

                return true;
            }
        );

        // When get_option is called, return testInput
        Functions\when('get_option')->justReturn([]);

        foreach ($this->testInput as $index => $data) {
            $entity = $this->repository->create($data);

            $this->assertEquals($entity->getProperties(), [
                'id' => $index,
                'key1' => $data['key1'],
                'key2' => $data['key2'],
            ]);
        }
    }

    public function testFindAll()
    {

        $results = $this->repository->findAll();

        $this->assertEquals($results, array_map(
            function ($item) {
                $factory = new CollectionFactory('\QuadLayers\WP_Orm\Tests\CollectionEntityTest');
                return $factory->create($item);
            },
            $this->testOutput
        ));
    }

    public function testDeleteAll()
    {
        Functions\when('delete_option')->justReturn(true);
        $this->repository->deleteAll();
        $this->assertEquals($this->repository->findAll(), null);
    }

    public function testUpdate()
    {

        Functions\when('update_option')->justReturn(true);

        $entity = $this->repository->update(1, ['key1' => 'value1_2_updated']);

        $this->assertEquals($entity->getProperties(), [
            'id' => 1,
            'key1' => 'value1_2_updated',
            'key2' => 'value2_2',
        ]);
    }

    public function testDelete()
    {

        Functions\when('update_option')->alias(
            function ($option) use (&$callCount) {
                return true;
            }
        );

        $result = $this->repository->delete(1);

        $this->assertTrue($result);
    }

    public function testDefaults()
    {
        $results = $this->repository->findAll();
        foreach ($results as $index => $entity) {
            $defaults = $entity->getDefaults();
            $schema = $entity->getSchema();
            $this->assertEquals($defaults, [
                'id' => 0,
                'key1' => 'default_value_1',
                'key2' =>  'default_value_2',
            ]);
            $this->assertEquals($schema, [
                "id" => [
                    "type" => "integer",
                    "default" => 0,
                ],
                "key1" => [
                    "type" => "string",
                    "default" => "default_value_1",
                ],
                "key2" => [
                    "type" => "string",
                    "default" => "default_value_2",
                ],
            ]);
        }
    }
}

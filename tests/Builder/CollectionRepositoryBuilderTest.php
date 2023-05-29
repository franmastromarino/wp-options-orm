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
        // [
        //     'key1' => 'value1_2',
        //     'key2' => 'value2_2',
        // ],
        // [
        //     'key1' => 'value1_3',
        //     'key2' => 'value2_3',
        // ],
    ];

    private array $testOutput = [
        [
            'id' => 0,
            'key1' => 'value1_1',
            'key2' => 'value2_1',
            'key3' => [
                'key_3_1' => 'default_value_3',
                'key_3_2' => 'default_value_4',
            ]
        ],
        // [
        //     'id' => 1,
        //     'key1' => 'value1_2',
        //     'key2' => 'value2_2',
        //     'key3' => [
        //         'key_3_1' => 'default_value_3',
        //         'key_3_2' => 'default_value_4',
        //     ]
        // ],
        // [
        //     'id' => 2,
        //     'key1' => 'value1_3',
        //     'key2' => 'value2_3',
        //     'key3' => [
        //         'key_3_1' => 'default_value_3',
        //         'key_3_2' => 'default_value_4',
        //     ]
        // ],
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

                // $this->testOutput[2] = (object) $this->testOutput[2];

                // error_log('this->testOutput: ' . json_encode($this->testOutput, JSON_PRETTY_PRINT));
                // error_log('value: ' . json_encode($value, JSON_PRETTY_PRINT));

                // // Check if the value matches the test values up to the current call count
                // if (serialize(array_slice($this->testOutput, 0, $callCount + 1)) !== serialize($value)) {
                //     return false;
                // }

                // // Increase the call count
                $callCount++;

                return true;
            }
        );

        // When get_option is called, return testInput
        Functions\when('get_option')->justReturn([]);

        foreach ($this->testOutput as $index => $data) {
            $entity = $this->repository->create($data);

            // error_log('entity: ' . json_encode($entity, JSON_PRETTY_PRINT));

            $properties = $entity->getProperties();

            // error_log('properties: ' . json_encode($properties, JSON_PRETTY_PRINT));

            $this->assertEquals(json_encode($properties), json_encode($data));
        }
    }

    public function testFindAll()
    {

        $results = $this->repository->findAll();

        $factory = new CollectionFactory('\QuadLayers\WP_Orm\Tests\CollectionEntityTest');

        $test = array_map(
            function ($item) use ($factory) {
                return $factory->create($item);
            },
            $this->testOutput
        );

        $this->assertEquals(json_encode($results), json_encode($test));
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

        $entity = $this->repository->update(0, ['key1' => 'value1_2_updated']);

        $result = $entity->getModifiedProperties();

        $this->assertEquals($result, ['key1' => 'value1_2_updated', 'key2' => 'value2_1']);
    }

    public function testDelete()
    {

        Functions\when('update_option')->justReturn(true);

        $result = $this->repository->delete(0);

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
                'key3' => [
                    'key_3_1' => 'default_value_3',
                    'key_3_2' => 'default_value_4',
                ]
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
                "key3" => [
                    "type" => "array",
                    "default" => [
                        "key_3_1" => "default_value_3",
                        "key_3_2" => "default_value_4",
                    ],
                ],
            ]);
        }
    }
}

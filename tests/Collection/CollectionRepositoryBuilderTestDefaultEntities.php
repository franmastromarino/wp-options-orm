<?php

namespace QuadLayers\WP_Orm\Tests\Collection;

use PHPUnit\Framework\TestCase;
use Brain\Monkey\Functions;
use QuadLayers\WP_Orm\Builder\CollectionRepositoryBuilder;
use QuadLayers\WP_Orm\Repository\CollectionRepository;

class CollectionRepositoryBuilderTestDefaultEntities extends TestCase
{
    private string $table = 'test_table';
    private CollectionRepository $repository;
    private array $defaultEntities;

    protected function setUp(): void
    {

        // Create default entities
        $this->defaultEntities = [
            ['id' => 5, 'key1' => 'defaultEntities1'],
            ['id' => 6, 'key1' => 'defaultEntities2']
        ];

         // Set up the builder with default entities
        $builder = (new CollectionRepositoryBuilder())
            ->setTable($this->table)
            ->setEntity('\QuadLayers\WP_Orm\Tests\Collection\CollectionEntityTest')
            ->setAutoIncrement(true)
            ->setDefaultEntities($this->defaultEntities); // Set default entities


        $this->repository = $builder->getRepository();

        // When get_option is called, return testInput
        Functions\when('get_option')->justReturn($this->defaultEntities);
    }

    public function testFindAll()
    {

        $test = [
            [
                'key1' => 'test',
            ],
            [
                'key1' => 'test2',
            ],
            [
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

        $merged = array_merge($this->defaultEntities, $test);

        // Then proceed with count check
        $this->assertCount(count($merged), $results);

        // Check if the found entities match the default entities properties
        foreach ($results as $index => $entity) {
            // Assuming getProperties() returns an associative array of entity properties
            $properties = $entity->getProperties();
            $expectedProperties = $merged[$index];

            foreach ($expectedProperties as $key => $value) {
                $this->assertArrayHasKey($key, $properties, "Property $key should exist in the entity");
                $this->assertEquals($value, $properties[$key], "Property $key should match the expected value");
            }
        }
    }

    public function testDeleteAll()
    {
        Functions\when('delete_option')->justReturn(true);
        $this->repository->deleteAll();
        $results = $this->repository->findAll();
         // Then proceed with count check
         $this->assertCount(count($this->defaultEntities), $results);

         // Check if the found entities match the default entities properties
         foreach ($results as $index => $entity) {
             // Assuming getProperties() returns an associative array of entity properties
             $properties = $entity->getProperties();
             $expectedProperties = $this->defaultEntities[$index];
 
             foreach ($expectedProperties as $key => $value) {
                 $this->assertArrayHasKey($key, $properties, "Property $key should exist in the entity");
                 $this->assertEquals($value, $properties[$key], "Property $key should match the expected value");
             }
         }
    }

    public function testCreate()
    {

        Functions\when('update_option')->justReturn(true);

        $entity = $this->repository->create(['key1' => 'value1_2_updated']);

        $result = $entity->getModifiedProperties();

        $this->assertEquals($result, ['id' => end($this->defaultEntities)['id'] + 1,'key1' => 'value1_2_updated']);
    }

    public function testUpdate()
    {

        Functions\when('update_option')->justReturn(true);

        $id = $this->defaultEntities[0]['id'];

        $entity0 = $this->repository->create([]);
        $entity0 = $this->repository->update($id, ['key1' => 'value1_2_updated']);

        $result = $entity0->getModifiedProperties();

        $this->assertEquals($result, ['id' => $id,'key1' => 'value1_2_updated']);
    }

    public function testDelete()
    {

        Functions\when('update_option')->justReturn(true);

        $id = end($this->defaultEntities)['id'];

        $entity0 = $this->repository->create([]);
        $entity1 = $this->repository->create([]);
        $entity2 = $this->repository->create([]);

        $result = $this->repository->delete($id);

        $this->assertTrue($result);
        $this->assertEquals(null, $this->repository->find($id));
        $this->assertEquals($entity1, $this->repository->find($id + 2));
        $this->assertEquals($entity2, $this->repository->find($id + 3));
    }

    public function testDefaults()
    {
        $entity0 = $this->repository->create([]);
        $entity1 = $this->repository->create([]);
        $entity2 = $this->repository->create([]);

        $results = $this->repository->findAll();

        foreach ($results as $index => $entity) {
            $defaults = $entity->getDefaults();
            $this->assertEquals($defaults, [
                'id' => 0,
                'key1' => 'default_value_1',
                'key2' =>  'default_value_2',
                'key3' => [
                    'key_3_1' => 'default_value_3',
                    'key_3_2' => 'default_value_4',
                ]
            ]);
        }
    }

    public function testWithDefaultEntities()
    {

       // Test if the repository contains the default entities
        $result = $this->repository->findAll();
        // Check if $result is iterable
        $this->assertIsIterable($result, 'The result of findAll() should be iterable'); 

        // Then proceed with count check
        $this->assertCount(count($this->defaultEntities), $result);

        // Check if the found entities match the default entities properties
        foreach ($result as $index => $entity) {
            // Assuming getProperties() returns an associative array of entity properties
            $properties = $entity->getProperties();
            $expectedProperties = $this->defaultEntities[$index];

            foreach ($expectedProperties as $key => $value) {
                $this->assertArrayHasKey($key, $properties, "Property $key should exist in the entity");
                $this->assertEquals($value, $properties[$key], "Property $key should match the expected value");
            }
        }
        
    }
}

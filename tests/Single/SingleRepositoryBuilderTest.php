<?php

namespace QuadLayers\WP_Orm\Tests\Single;

use PHPUnit\Framework\TestCase;
use Brain\Monkey\Functions;
use Brain\Monkey;
use QuadLayers\WP_Orm\Builder\SingleRepositoryBuilder;
use QuadLayers\WP_Orm\Repository\SingleRepository;

class SingleRepositoryBuilderTest extends TestCase
{
    private array $testValue;
    private array $testDefaults;
    private string $table;
    private SingleRepository $repository;

    protected function setUp(): void
    {

        $this->table = 'settings';

        $this->testDefaults = [
            'key1' => 'default_value_1',
            'key2' => 'default_value_2'
        ];

        $this->testValue = [
            'key1' => 'value1',
            'key2' => 'value2',
        ];

        $builder = (new SingleRepositoryBuilder())
        ->setTable($this->table)
        ->setEntity('\QuadLayers\WP_Orm\Tests\Single\SingleEntityTest');

        $this->repository = $builder->getRepository();

        Functions\when('update_option')->alias(
            function ($option, $value) {
                if (serialize($this->testValue) !== serialize($value)) {
                    fwrite(STDOUT, "testValue => " . json_encode($this->testValue, true));
                    fwrite(STDOUT, "value => " . json_encode($value, true));
                    return false;
                }
                if ($this->table !== $option) {
                    fwrite(STDOUT, "option => " . json_encode($option, true));
                    fwrite(STDOUT, "table => " . json_encode($this->table, true));
                    return false;
                }
                return true;
            }
        );

        // When get_option is called, return existingData
        Functions\when('get_option')->justReturn($this->testValue);
    }

    protected function tearDown(): void
    {
        Monkey\tearDown();
        parent::tearDown();
    }

    public function testSave()
    {

        $result = $this->repository->create($this->testValue);

        $this->assertTrue($result);
    }

    public function testGet()
    {
        // Invoke get method
        $result = $this->repository->find();

        // Ensure the result matches our expectations
        // key1 should be the same as existing data
        $this->assertEquals($this->testValue['key1'], $result->getKey1());
        // key2 should be the default value from the schema
        $this->assertEquals($this->testValue['key2'], $result->getKey2());
    }

    public function testDefaults()
    {

        $result = $this->repository->create($this->testValue);

        // Ensure the result matches our expectations
        // key1 should be the same as existing data
        $this->assertEquals($this->testDefaults['key1'], 'default_value_1');
        // key2 should be the default value from the schema
        $this->assertEquals($this->testDefaults['key2'], 'default_value_2');
    }
}

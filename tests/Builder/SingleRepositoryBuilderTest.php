<?php

namespace QuadLayers\WP_Orm\Tests;

use PHPUnit\Framework\TestCase;
use Brain\Monkey\Functions;
use Brain\Monkey;
use QuadLayers\WP_Orm\Builder\SingleRepositoryBuilder;
use QuadLayers\WP_Orm\Repository\SingleRepository;

class SingleRepositoryBuilderTest extends TestCase
{
    private array $testValue;
    private array $testSchema;
    private string $testTable;
    private SingleRepository $repository;

    protected function setUp(): void
    {
        $this->testValue = TestValues::getValue();
        $this->testTable = TestValues::getOptionName();
        $this->testSchema = TestValues::getSchema();

        $builder = (new SingleRepositoryBuilder())
        ->setTable($this->testTable)
        ->setSchema($this->testSchema);

        $this->repository = $builder->getRepository();
    }

    protected function tearDown(): void
    {
        Monkey\tearDown();
        parent::tearDown();
    }

    public function testSave()
    {

        Functions\when('update_option')->alias(
            function ($option, $value) {
                if (serialize($this->testValue) !== serialize($value)) {
                    fwrite(STDOUT, "value => " . json_encode($value, true));
                    fwrite(STDOUT, "testValue => " . json_encode($this->testValue, true));
                    return false;
                }
                if ($this->testTable !== $option) {
                    fwrite(STDOUT, "option => " . json_encode($option, true));
                    fwrite(STDOUT, "this->testTable => " . json_encode($this->testTable, true));
                    return false;
                }
                return true;
            }
        );

        $result = $this->repository->create($this->testValue);

        $this->assertTrue($result);
    }

    public function testGet()
    {

        // When get_option is called, return existingData
        Functions\when('get_option')->justReturn($this->testValue);

        // Invoke get method
        $result = $this->repository->find();

        // Ensure the result matches our expectations
        // key1 should be the same as existing data
        $this->assertEquals($this->testValue['key1'], $result->getKey1());
        // key2 should be the default value from the schema
        $this->assertEquals($this->testValue['key2'], $result->getKey2());
    }
}

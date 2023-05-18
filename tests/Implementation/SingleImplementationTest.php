<?php

namespace YourNamespace\Tests;

use PHPUnit\Framework\TestCase;
use Brain\Monkey\Functions;
use Brain\Monkey;
use YourNamespace\Implementation\SingleImplementation;

class TestSingleImplementation extends SingleImplementation
{
}

class SingleImplementationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Monkey\setUp();
    }

    protected function tearDown(): void
    {
        Monkey\tearDown();
        parent::tearDown();
    }

    public function testSave()
    {

        $testValue = TestValues::getValue();
        $testOptionName = TestValues::getOptionName();
        $testSchema = TestValues::getSchema();

        Functions\when('update_option')->alias(
            function ($option, $value) use ($testValue, $testOptionName) {
                if (serialize($testValue) !== serialize($value)) {
                    fwrite(STDOUT, "value => " . json_encode($value, true));
                    fwrite(STDOUT, "testValue => " . json_encode($testValue, true));
                    return false;
                }
                if ($testOptionName !== $option) {
                    fwrite(STDOUT, "option => " . json_encode($option, true));
                    fwrite(STDOUT, "testOptionName => " . json_encode($testOptionName, true));
                    return false;
                }
                return true;
            }
        );

        $testSingleImplementation = TestSingleImplementation::getInstance($testOptionName, $testSchema);
        $result = $testSingleImplementation->save($testValue);

        $this->assertTrue($result);
    }
}

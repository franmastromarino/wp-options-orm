<?php

namespace YourPluginNamespace\Tests;

use PHPUnit\Framework\TestCase;
use Brain\Monkey;
use Brain\Monkey\Functions;
use Mockery;
use YourNamespace\Tests\TestValues;
use YourNamespace\Service\SingleService;
use YourNamespace\Repository\SingleRepository;
use YourNamespace\Mapper\SingleMapper;
use YourNamespace\Entity\SingleFactory;

class SingleServiceWordPressIntegrationTest extends TestCase
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

    public function testProcess()
    {
        $testValue = TestValues::getValue();
        $testOptionName = TestValues::getOptionName();
        $testSchema = TestValues::getSchema();
        //Mock WordPress functions
        Functions\when('update_option')->alias(
            function ($option, $value) use ($testValue, $testOptionName) {
                if (serialize($testValue) !== serialize($value)) {
                    fwrite(STDOUT, "testValue => " . json_encode($testValue, true));
                    return false;
                }
                if ($testOptionName !== $option) {
                    fwrite(STDOUT, "testOptionName => " . json_encode($option, true));
                    return false;
                }
                return true;
            }
        );

        Functions\when('get_option')->justReturn([]);

        $factory = new SingleFactory($testSchema);
        $mapper = new SingleMapper($factory);
        $repository = new SingleRepository($mapper, $testOptionName);

        /**
         * Test 1
         */
        $result = $repository->create($testValue);
        $this->assertTrue($result);
        /**
         * Test 2
         */

        // Clean up
        Mockery::close();
    }
}

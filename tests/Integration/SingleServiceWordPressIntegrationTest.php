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
use YourNamespace\DTO\SingleDTO;

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
        $testValues = TestValues::getValues();
        $testOptionName = TestValues::getOptionName();
        //Mock WordPress functions
        Functions\when('update_option')->alias(
            function ($option, $value) use ($testValues, $testOptionName) {
                if (serialize($testValues) !== serialize($value)) {
                    fwrite(STDOUT, "testValues => " . json_encode($testValues, true));
                    return false;
                }
                if ($testOptionName !== $option) {
                    fwrite(STDOUT, "option => " . json_encode($option, true));
                    return false;
                }
                return true;
            }
        );

        Functions\when('get_option')->justReturn([]);

        $mapper = new SingleMapper();
        $repository = new SingleRepository($mapper, $testOptionName);
        $service = new SingleService($repository, $mapper);

        /**
         * Test 1
         */
        $dto1 = new SingleDTO($testValues);
        $result1 = $service->process($dto1);
        $this->assertTrue($result1);
        /**
         * Test 2
         */

        // Clean up
        Mockery::close();
    }
}

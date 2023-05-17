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
        $test1 = TestValues::getTest1();
        //Mock WordPress functions
        Functions\when('update_option')->alias(
            function ($option, $value) use ($test1) {
                if (serialize($test1) === serialize($value)) {
                    return true;
                }
                fwrite(STDOUT, "test1 => " . json_encode($test1, true));
                fwrite(STDOUT, "value => " . json_encode($value, true));
                return false;
            }
        );

        Functions\when('get_option')->justReturn([]);

        $mapper = new SingleMapper();
        $repository = new SingleRepository($mapper);
        $service = new SingleService($repository, $mapper);

        /**
         * Test 1
         */
        $dto1 = new SingleDTO($test1);
        $result1 = $service->process($dto1);
        $this->assertTrue($result1);
        /**
         * Test 2
         */

        // Clean up
        Mockery::close();
    }
}

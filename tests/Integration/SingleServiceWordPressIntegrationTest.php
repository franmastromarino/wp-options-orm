<?php

namespace YourPluginNamespace\Tests;

use PHPUnit\Framework\TestCase;
use Brain\Monkey;
use Brain\Monkey\Functions;
use Mockery;
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

        $test1 = ["key1" => "plugin_setting_1","key2" => "value1"];

        //Mock WordPress functions
        Functions\when('update_option')->alias(
            function ($option, $value) {
                fwrite(STDOUT, "update_option called with: {$option} => " . json_encode($value, true));
                return true;
            }
        );

        Functions\when('get_option')->justReturn([]);

        $mapper = new SingleMapper();
        $repository = new SingleRepository($mapper);
        $service = new SingleService($repository, $mapper);

        $dto1 = new SingleDTO();
        $dto1->setKey1('plugin_setting_1');
        $dto1->setKey2('value1');

        $dto2 = new SingleDTO();
        $dto2->setKey1('plugin_setting_2');
        $dto2->setKey2('value2');

        $result1 = $service->process($dto1);
        $result2 = $service->process($dto2);

        $this->assertTrue($result1);
        $this->assertTrue($result2);

        // Clean up
        Mockery::close();
    }
}

<?php

namespace YourNamespace\Tests;

class TestValues
{
    public static function getValue(): array
    {
        return [
            "key1" => "plugin_setting_1",
            "key2" => "value1"
        ];
    }

    public static function getOptionName(): string
    {
        return "option_name_1";
    }

    public static function getSchema(): array
    {
        return [
            'type'       => 'object',
            'properties' => [
                "key1" => [
                    "type" => "string",
                    "default" => "plugin_setting_1"
                ],
                "key2" => [
                    "type" => "string",
                    "default" => "value1"
                ]
            ]
        ];
    }
}

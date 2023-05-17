<?php

namespace YourNamespace\Tests;

class TestValues
{
    public static function getValues(): array
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
}

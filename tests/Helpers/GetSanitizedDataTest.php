<?php

namespace QuadLayers\WP_Orm\Tests\Helpers;

use PHPUnit\Framework\TestCase;

use function QuadLayers\WP_Orm\Helpers\getSanitizedData;

class GetSanitizedDataTest extends TestCase
{
    private array $schema;

    public function setUp(): void
    {
        $this->schema = [
            'id' => [
                'sanitizeFunction' => 'integer',
                'default' => 0
            ],
            'key1' => [
                'sanitizeFunction' => 'string',
                'default' => 'key1_default_value1'
            ],
            'key2' => [
                'sanitizeFunction' => 'boolean',
                'default' => false
            ],
            'key3' => [
                'sanitizeFunction' => 'object',
                'default' => (object) [
                    'key3_1' => 'key3_default_value1',
                    'key3_2' => 'key3_default_value2'
                ],
                'properties' => [
                    'key3_1' => [
                        'sanitizeFunction' => 'string',
                        'default' => 'key3_default_value1'
                    ],
                    'key3_2' => [
                        'sanitizeFunction' => 'string',
                        'default' => 'key3_default_value2'
                    ],
                ]
            ],
            'key4' => [
                'sanitizeFunction' => 'number',
                'default' => 1
            ],
            'key5' => [
                'sanitizeFunction' => 'number'
            ],
            'key6' => [
                'sanitizeFunction' => 'array',
                'default' => [
                    'key6_1' => 'key6_default_value1',
                    'key6_2' => 'key6_default_value2'
                ],
                'properties' => [
                    'key6_1' => [
                        'sanitizeFunction' => 'string',
                        'default' => 'key6_default_value1'
                    ],
                    'key6_2' => [
                        'sanitizeFunction' => 'string',
                        'default' => 'key6_default_value2'
                    ],
                ]
            ],
        ];
        
    }

    public function testGetSanitizedDataStrict()
    {

        $data = [
            'id' => 0,
            'key1' => 'value1_1',
            'key2' => true,
            'key3' => [
                'key3_1' => 'test',
                'key3_2' => 'test'
            ],
            'key4' => '1'
        ];

        $result = getSanitizedData($data, $this->schema, true);

        $expected = [
            'id' => 0,
            'key1' => 'value1_1',
            'key2' => true
        ];

        $this->assertSame($expected, $result);
    }

    public function testGetSanitizedDataNonStrictArrayToObject()
    {

        $data = [
            'id' => 0,
            'key1' => 'value1_1',
            'key2' => 1,
            'key3' => [
                'key3_1' => 'value_3',
                'key3_2' => 'value_4'
            ],
            'key4' => '1',
            'key5' => '-1'
        ];

        $expected = [
            'id' => 0,
            'key1' => 'value1_1',
            'key2' => true,
            'key3' => (object) [
                'key3_1' => 'value_3',
                'key3_2' => 'value_4'
            ],
            'key4' => 1,
            'key5' => -1,
            'key6' => [
                'key6_1' => 'key6_default_value1',
                'key6_2' => 'key6_default_value2'
            ]
        ];

        $result = getSanitizedData($data, $this->schema, false);

        $this->assertEquals($expected, $result);
    }

    public function testGetSanitizedDataNonStrictObjectToArray()
    {

        $data = [
            'id' => 0,
            'key1' => 'value1_1',
            'key2' => 1,
            'key3' => [
                'key3_1' => 'value_3',
                'key3_2' => 'value_4'
            ],
            'key4' => '1',
            'key5' => '-1',
            'key6' => (object) [
                'key6_1' => 'value_3',
                'key6_2' => 'value_4'
            ]
        ];

        $result = getSanitizedData($data, $this->schema, false);

        $expected = [
            'id' => 0,
            'key1' => 'value1_1',
            'key2' => true,
            'key3' => (object) [
                'key3_1' => 'value_3',
                'key3_2' => 'value_4'
            ],
            'key4' => 1,
            'key5' => -1,
            'key6' => [
                'key6_1' => 'value_3',
                'key6_2' => 'value_4'
            ]
        ];

        $this->assertEquals($expected, $result);
    }

    public function testGetSanitizedDataWithUnsupportedSanitizeFunction()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Error sanitizing value for key 'test': Call to undefined function unsupported_sanitize_function()");

        $data = [
            'test' => '2',
        ];

        $schema = [
            'test' => [
                'sanitizeFunction' => 'unsupported_sanitize_function',
                'default' => 0
            ],
        ];

        $result = getSanitizedData($data, $schema, false);
    }

    public function testGetSanitizedDataNonStrictWithInvalidNumber()
    {
        $data = [
            'key' => 'not a number'
        ];

        $schema = [
            'key' => [
                'sanitizeFunction' => 'number',
                'default' => 0
            ],
        ];

        $expected = [
            'key' => 0
        ];

        $result = getSanitizedData($data, $schema, false);

        $this->assertSame($expected, $result);
    }

    public function testGetSanitizedDataStrictWithInvalidNumber()
    {
        $data = [
            'key' => 'not a number'
        ];

        $schema = [
            'key' => [
                'sanitizeFunction' => 'number',
                'default' => 0
            ],
        ];

        $expected = [];

        $result = getSanitizedData($data, $schema, true);

        $this->assertSame($expected, $result);
    }

    public function testGetSanitizedDataNonStrictWithFloatNumber()
    {
        $data = [
            'key' => '10.5'
        ];

        $schema = [
            'key' => [
                'sanitizeFunction' => 'number',
                'default' => 0
            ],
        ];

        $expected = [
            'key' => 10.5
        ];

        $result = getSanitizedData($data, $schema, false);

        $this->assertEquals($expected, $result);
    }

    public function testGetSanitizedDataStrictWithFloatNumber()
    {
        $data = [
            'key' => '10.5'
        ];

        $schema = [
            'key' => [
                'sanitizeFunction' => 'number',
                'default' => 0
            ],
        ];

        $expected = [];

        $result = getSanitizedData($data, $schema, true);

        $this->assertEquals($expected, $result);
    }

    public function testGetSanitizedDataNonStrictWithInvalidBoolean()
    {
        $data = [
            'key' => 'not a boolean'
        ];

        $schema = [
            'key' => [
                'sanitizeFunction' => 'boolean',
                'default' => false
            ],
        ];

        $expected = [
            'key' => false
        ];

        $result = getSanitizedData($data, $schema, false);

        $this->assertEquals($expected, $result);
    }

    public function testGetSanitizedDataStrictWithInvalidBoolean()
    {
        $data = [
            'key' => 'not a boolean'
        ];

        $schema = [
            'key' => [
                'sanitizeFunction' => 'boolean',
                'default' => false
            ],
        ];

        $expected = [];

        $result = getSanitizedData($data, $schema, true);

        $this->assertEquals($expected, $result);
    }

    public function testGetSanitizedDataStrictMissingValue()
    {
        $data = [
            'key1' => 'value1',
        ];

        $schema = [
            'key1' => [
                'sanitizeFunction' => 'string',
                'default' => 'default1'
            ],
            'key2' => [
                'sanitizeFunction' => 'string',
                'default' => 'default2'
            ],
        ];

        $expected = [
            'key1' => 'value1',
        ];

        $result = getSanitizedData($data, $schema, true);

        $this->assertEquals($expected, $result);
    }

    public function testGetSanitizedDataNonStrictMissingValue()
    {
        $data = [
             'key1' => 'value1',
        ];

        $schema = [
            'key1' => [
                'sanitizeFunction' => 'string',
                'default' => 'default1'
            ],
            'key2' => [
                'sanitizeFunction' => 'string',
                'default' => 'default2'
            ],
        ];

        $expected = [
            'key1' => 'value1',
            'key2' => 'default2'
        ];

        $result = getSanitizedData($data, $schema, false);

        $this->assertEquals($expected, $result);
    }

    // public function testGetSanitizedDataNonStrictInvalidSanitizeFunction()
    public function testGetSanitizedDataNonStrictInvalidType()
    {
        $data = [
            'key' => 'not a number'
        ];

        $schema = [
            'key' => [
                'sanitizeFunction' => 'number',
                'default' => 0
            ],
        ];

        $expected = [
            'key' => 0
        ];

        $result = getSanitizedData($data, $schema, false);

        $this->assertEquals($expected, $result);
    }

    public function testNonStringValueForStringFieldNonStrict()
    {
        $data = ['key1' => 1];
        $schema = ['key1' => ['sanitizeFunction' => 'string', 'default' => 'default1']];

        $expected = ['key1' => '1'];

        $this->assertEquals($expected, getSanitizedData($data, $schema, false));
    }

    public function testNonStringValueForStringFieldStrict()
    {
        $data = ['key1' => 1];
        $schema = ['key1' => ['sanitizeFunction' => 'string', 'default' => 'default1']];

        $expected = [];

        $this->assertEquals($expected, getSanitizedData($data, $schema, true));
    }

    public function testNonObjectValueForObjectFieldNonStrict()
    {
        $data = [
            'key1' => 'string'
        ];

        $schema = [
            'key1' => [
                'sanitizeFunction' => 'object',
                'default' => (object) [
                    'key' => 'default'
                ]
            ]
        ];

        $expected = [
            'key1' => (object) ['key' => 'default']
        ];

        $this->assertEquals($expected, getSanitizedData($data, $schema, false));
    }

    public function testNonObjectValueForObjectFieldStrict()
    {
        $data = ['key1' => 'string'];
        $schema = ['key1' => ['sanitizeFunction' => 'object', 'default' => (object) ['key' => 'default']]];

        $expected = [];

        $this->assertEquals($expected, getSanitizedData($data, $schema, true));
    }

    public function testNonIntegerValueForIntegerFieldNonStrict()
    {
        $data = ['key1' => 'string'];
        $schema = ['key1' => ['sanitizeFunction' => 'integer', 'default' => 0]];

        $expected = ['key1' => 0];

        $this->assertEquals($expected, getSanitizedData($data, $schema, false));
    }

    public function testNonIntegerValueForIntegerFieldStrict()
    {
        $data = ['key1' => 'string'];
        $schema = ['key1' => ['sanitizeFunction' => 'integer', 'default' => 0]];

        $expected = [];

        $this->assertEquals($expected, getSanitizedData($data, $schema, true));
    }

    public function testStringTrueFalseForBooleanFieldNonStrict()
    {
        $data = ['key1' => 'true', 'key2' => 'false'];
        $schema = ['key1' => ['sanitizeFunction' => 'boolean', 'default' => false], 'key2' => ['sanitizeFunction' => 'boolean', 'default' => false]];

        $expected = ['key1' => true, 'key2' => false];

        $this->assertEquals($expected, getSanitizedData($data, $schema, false));
    }

    public function testStringTrueFalseForBooleanFieldStrict()
    {
        $data = ['key1' => 'true', 'key2' => 'false'];
        $schema = ['key1' => ['sanitizeFunction' => 'boolean', 'default' => false], 'key2' => ['sanitizeFunction' => 'boolean', 'default' => false]];

        $expected = [];

        $this->assertEquals($expected, getSanitizedData($data, $schema, true));
    }

    public function testNonBooleanValueForBooleanFieldNonStrict()
    {
        $data = ['key1' => 'string'];
        $schema = ['key1' => ['sanitizeFunction' => 'boolean', 'default' => false]];

        $expected = ['key1' => false];

        $this->assertEquals($expected, getSanitizedData($data, $schema, false));
    }

    public function testNonBooleanValueForBooleanFieldStrict()
    {
        $data = ['key1' => 'string'];
        $schema = ['key1' => ['sanitizeFunction' => 'boolean', 'default' => false]];

        $expected = [];

        $this->assertEquals($expected, getSanitizedData($data, $schema, true));
    }
}

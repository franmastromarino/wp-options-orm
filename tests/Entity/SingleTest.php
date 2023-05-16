<?php

namespace YourNamespace\Tests\Entity;

use PHPUnit\Framework\TestCase;
use YourNamespace\Entity\Single;

class SingleTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $single = new Single();
        $single->setKey1('value1');
        $single->setKey2('value2');

        $this->assertEquals('value1', $single->getKey1());
        $this->assertEquals('value2', $single->getKey2());
    }
}

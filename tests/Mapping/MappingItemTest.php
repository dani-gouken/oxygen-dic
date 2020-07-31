<?php


namespace Oxygen\DI\Test\Mapping;

use Oxygen\DI\Mapping\MappingItem;
use Oxygen\DI\Test\BaseTestCase;
use Oxygen\DI\Value;

class MappingItemTest extends BaseTestCase
{
    public function testMappedEntityKeyAndGetStorable()
    {
        $mappingItem = new MappingItem("foo", $storable = new Value("bar"));
        $this->assertEquals("foo", $mappingItem->getMappedEntityKey());
        $this->assertEquals($storable, $mappingItem->getStorable());
    }
}

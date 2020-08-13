<?php


namespace Oxygen\DI\Test\Mapping;

use Oxygen\DI\Mapping\MappingItem;
use Oxygen\DI\Test\BaseTestCase;
use Oxygen\DI\Definitions\Value;

class MappingItemTest extends BaseTestCase
{
    public function testMappedEntityKeyAndGetDefinition()
    {
        $mappingItem = new MappingItem("foo", $definition = new Value("bar"));
        $this->assertEquals("foo", $mappingItem->getMappedEntityKey());
        $this->assertEquals($definition, $mappingItem->getDefinition());
    }
}

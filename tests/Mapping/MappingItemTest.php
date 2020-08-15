<?php


namespace Atom\DI\Test\Mapping;

use Atom\DI\Mapping\MappingItem;
use Atom\DI\Test\BaseTestCase;
use Atom\DI\Definitions\Value;

class MappingItemTest extends BaseTestCase
{
    public function testMappedEntityKeyAndGetDefinition()
    {
        $mappingItem = new MappingItem("foo", $definition = new Value("bar"));
        $this->assertEquals("foo", $mappingItem->getMappedEntityKey());
        $this->assertEquals($definition, $mappingItem->getDefinition());
    }
}

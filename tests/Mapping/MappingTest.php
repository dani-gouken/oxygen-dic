<?php


namespace Oxygen\DI\Test\Mapping;

use Oxygen\DI\Exceptions\ContainerException;
use Oxygen\DI\Mapping\Mapping;
use Oxygen\DI\Mapping\MappingItem;
use Oxygen\DI\Test\BaseTestCase;
use Oxygen\DI\Definitions\Value;

class MappingTest extends BaseTestCase
{
    /**
     * @throws ContainerException
     */
    public function testAdd()
    {
        $mapping = new Mapping();
        $this->assertFalse($mapping->hasMappingFor("foo"));
        $this->assertEmpty($mapping->getMappedEntities());
        $mapping->add(new MappingItem("foo", new Value("bar")));
        $mapping->add(new MappingItem("bar", new Value("baz")));

        $this->assertTrue($mapping->hasMappingFor("foo"));
        $this->assertEquals($mapping->getMappingFor("foo")->getDefinition()->getValue(), "bar");
        $this->assertCount(2, $mapping->getMappedEntities());
    }

    public function testGetMappedEntities()
    {
        $mapping = new Mapping();
        $this->assertEmpty($mapping->getMappedEntities());
        $mapping->add(new MappingItem("foo", new Value("bar")));
        $mapping->add(new MappingItem("bar", new Value("baz")));
        $this->assertEquals(["foo", "bar"], $mapping->getMappedEntities());
    }

    public function testHasMappingFor()
    {
        $mapping = new Mapping();
        $this->assertFalse($mapping->hasMappingFor("foo"));
        $mapping->add(new MappingItem("foo", new Value("bar")));
        $this->assertTrue($mapping->hasMappingFor("foo"));
    }

    /**
     * @throws ContainerException
     */
    public function testGetMappingFor()
    {
        $mapping = new Mapping();
        $mapping->add($item = new MappingItem("foo", new Value("bar")));
        $this->assertEquals($item, $mapping->getMappingFor("foo"));
        $this->expectException(ContainerException::class);
        $mapping->getMappingFor("baz");
    }
}

<?php


namespace Oxygen\DI\Test\Definitions;

use Oxygen\DI\Definitions\BuildObject;
use Oxygen\DI\Definitions\Value;
use Oxygen\DI\Storage\ClassBindingTrait;
use Oxygen\DI\Test\BaseTestCase;
use Oxygen\DI\Test\Misc\Dummy1;
use PHPUnit\Framework\MockObject\MockObject;

class ClassBindingTraitTest extends BaseTestCase
{
    public function makeTrait()
    {
        /**
         * @var ClassBindingTrait|MockObject $object
         */
        $object =  $this->getMockForTrait(ClassBindingTrait::class);
        return $object;
    }

    public function testBindClass()
    {
        $definition = $this->makeTrait();
        $definition->method("store")->willReturn("foo");
        $instance =$definition->bindClass("foo");
        $this->assertInstanceOf(BuildObject::class, $instance);
        $this->assertEquals("foo", $instance->getExtractionParameter()->getClassName());
    }

    public function testBindInstance()
    {
        $definition = $this->makeTrait();
        $definition->method("store")->willReturn("foo");
        $instance = $definition->bindInstance($value = new Dummy1());
        $this->assertInstanceOf(Value::class, $instance);
        $this->assertEquals($value, $instance->getExtractionParameter()->getValue());
    }
}

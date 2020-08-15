<?php


namespace Atom\DI\Test\Definitions;

use Atom\DI\Definitions\BuildObject;
use Atom\DI\Definitions\Value;
use Atom\DI\Storage\ClassBindingTrait;
use Atom\DI\Test\BaseTestCase;
use Atom\DI\Test\Misc\Dummy1;
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

<?php


namespace Oxygen\DI\Test\Extraction\ExtractionParameters;

use Oxygen\DI\Extraction\ExtractionParameters\AbstractExtractionParameter;
use Oxygen\DI\Mapping\Mapping;
use Oxygen\DI\Mapping\MappingItem;
use Oxygen\DI\Test\BaseTestCase;
use Oxygen\DI\Value;

class AbstractExtractionParameterTest extends BaseTestCase
{
    public function makeParameter(): AbstractExtractionParameter
    {
        return $this->getMockForAbstractClass(AbstractExtractionParameter::class);
    }

    public function testItIsInstantiatedWithObjectAndParameterMapping()
    {
        $parameter = $this->makeParameter();
        $this->assertInstanceOf(Mapping::class, $parameter->getParameterMapping());
        $this->assertInstanceOf(Mapping::class, $parameter->getObjectMapping());
    }

    public function testSetObjectMappingAndSetParameterMapping()
    {
        $parameter = $this->makeParameter();
        $parameterMapping = new Mapping();
        $parameterMapping->add(new MappingItem("foo", new Value("bar")));
        $objectMapping = new Mapping();
        $objectMapping->add(new MappingItem("john", new Value("doe")));

        $this->assertNotEquals($objectMapping, $parameter->getObjectMapping());
        $this->assertNotEquals($parameterMapping, $parameter->getParameterMapping());

        $parameter->setObjectMapping($objectMapping);
        $parameter->setParameterMapping($parameterMapping);
        $this->assertEquals($objectMapping, $parameter->getObjectMapping());
        $this->assertEquals($parameterMapping, $parameter->getParameterMapping());
    }
}

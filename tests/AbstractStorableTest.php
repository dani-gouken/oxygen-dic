<?php


namespace Oxygen\DI\Test;

use Oxygen\DI\AbstractStorable;
use Oxygen\DI\Extraction\ExtractionParameters\ValueExtractionParameter;
use Oxygen\DI\Value;
use PHPUnit\Framework\MockObject\MockObject;

class AbstractStorableTest extends BaseTestCase
{
    /**
     * @return AbstractStorable
     */
    public function makeStorable(): AbstractStorable
    {
        return $this->getMockForAbstractClass(AbstractStorable::class);
    }

    public function testBind()
    {
        $storable = $this->makeStorable();
        $storable->expects($this->any())
            ->method('getExtractionParameter')
            ->will($this->returnValue(new ValueExtractionParameter("foo")));
        $storable->bind("foo", $value = new Value("bar"));
        $this->assertEquals(
            'bar',
            $storable->getExtractionParameter()
                ->getObjectMapping()->getMappingFor("foo")
                ->getStorable()->getValue()
        );
    }

    public function testWithParameter()
    {
        $storable = $this->makeStorable();
        $storable->expects($this->any())
            ->method('getExtractionParameter')
            ->will($this->returnValue(new ValueExtractionParameter("foo")));
        $storable->withParameter("foo", $value = new Value("bar"));
        $this->assertEquals(
            'bar',
            $storable->getExtractionParameter()
                ->getParameterMapping()->getMappingFor("foo")
                ->getStorable()->getValue()
        );
    }

    public function testGetResolutionCallback()
    {
        $storable = $this->makeStorable();
        $this->assertNull($storable->getResolutionCallback());
        $storable->resolved(function () {
            return "foo";
        });
        $this->assertEquals("foo", $storable->getResolutionCallback()());
    }
}

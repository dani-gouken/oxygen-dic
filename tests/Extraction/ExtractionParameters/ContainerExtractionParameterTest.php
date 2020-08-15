<?php


namespace Atom\DI\Test\Extraction\ExtractionParameters;

use Atom\DI\Extraction\ExtractionParameters\ContainerExtractionParameter;
use Atom\DI\Test\BaseTestCase;

class ContainerExtractionParameterTest extends BaseTestCase
{
    public function makeParameter(string $key)
    {
        return new ContainerExtractionParameter($key);
    }

    public function testItCanBeInstantiated()
    {
        $this->assertInstanceOf(ContainerExtractionParameter::class, $this->makeParameter("foo"));
    }

    public function testGetExtractionKey()
    {
        $this->assertEquals(
            $this->makeParameter("foo")->getExtractionKey(),
            "foo"
        );
    }
}

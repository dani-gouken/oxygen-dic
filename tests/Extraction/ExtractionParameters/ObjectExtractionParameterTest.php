<?php


namespace Atom\DI\Test\Extraction\ExtractionParameters;

use Atom\DI\Extraction\ExtractionParameters\ObjectExtractionParameter;
use Atom\DI\Test\BaseTestCase;

class ObjectExtractionParameterTest extends BaseTestCase
{

    /**
     * @param string $className
     * @param array $constructorArgs
     * @param bool $cacheResult
     * @return ObjectExtractionParameter
     */
    public function makeParameter(
        string $className,
        array $constructorArgs = [],
        bool $cacheResult = false
    ): ObjectExtractionParameter {
        return new ObjectExtractionParameter($className, $constructorArgs, $cacheResult);
    }

    public function testGetExtractionKey()
    {
        $this->assertEquals($this->makeParameter("foo")->getExtractionKey(), "foo");
    }

    public function testGetConstructorArgs()
    {
        $this->assertEquals($this->makeParameter("foo", $params = ["foo"=>"bar"])->getConstructorArgs(), $params);
    }

    public function testGetClassName()
    {
        $this->assertEquals($this->makeParameter("foo")->getClassName(), "foo");
    }

    public function testCanCacheResult()
    {
        $this->assertTrue($this->makeParameter("foo", [], true)->canCacheResult());
        $this->assertFalse($this->makeParameter("foo", [])->canCacheResult());
    }

    public function testSetConstructorArgs()
    {
        $param = $this->makeParameter("foo");
        $this->assertEmpty($param->getConstructorArgs());
        $param->setConstructorArgs($params = ["foo"=>"bar"]);
        $this->assertEquals($params, $param->getConstructorArgs());
    }
}

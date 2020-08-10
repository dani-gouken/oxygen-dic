<?php


namespace Oxygen\DI\Test\Extraction;

use Oxygen\DI\Exceptions\CircularDependencyException;
use Oxygen\DI\Exceptions\ContainerException;
use Oxygen\DI\Exceptions\NotFoundException;
use Oxygen\DI\Exceptions\StorageNotFoundException;
use Oxygen\DI\Exceptions\UnsupportedInvokerException;
use Oxygen\DI\Extraction\ExtractionParameters\FunctionExtractionParameter;
use Oxygen\DI\Extraction\ExtractionParameters\MethodExtractionParameter;
use Oxygen\DI\Extraction\MethodExtractor;
use Oxygen\DI\Get;
use Oxygen\DI\Test\BaseTestCase;
use Oxygen\DI\Test\Misc\Dummy1;
use Oxygen\DI\Test\Misc\Dummy2;
use Oxygen\DI\Test\Misc\Dummy3;
use Oxygen\DI\Value;
use ReflectionException;

class MethodExtractorTest extends BaseTestCase
{
    private function makeExtractor(): MethodExtractor
    {
        return new MethodExtractor();
    }

    public function testIsValidExtractionParameter()
    {
        $extractor = $this->makeExtractor();
        $this->assertTrue($extractor->isValidExtractionParameter(new MethodExtractionParameter("foo", "bar")));
        $this->assertFalse($extractor->isValidExtractionParameter(new FunctionExtractionParameter("foo")));
    }

    /**
     * @throws CircularDependencyException
     * @throws ContainerException
     * @throws NotFoundException
     * @throws StorageNotFoundException
     * @throws ReflectionException
     * @throws UnsupportedInvokerException
     */
    public function testExtract()
    {
        $extractor = $this->makeExtractor();
        $container = $this->getContainer();
        $this->assertEquals("foo", $extractor->extract(
            new MethodExtractionParameter(new Dummy2("foo"), "getFoo"),
            $container
        ));

        $this->assertEquals(
            "foo",
            $extractor->extract(
                new MethodExtractionParameter(Dummy3::class, "__invoke"),
                $this->getContainer()
            )
        );

        $this->assertEquals(
            "bar",
            $extractor->extract(
                new MethodExtractionParameter(Dummy3::class, "getBar", ["bar"=>"bar"]),
                $this->getContainer()
            )
        );
        $container->values()->store(
            Dummy2::class,
            new Value(new Dummy2("John doe"))
        );
        $this->assertEquals(
            "John doe",
            $extractor->extract(
                new MethodExtractionParameter(new Get(Dummy2::class), "getFoo"),
                $container
            )
        );
    }

    /**
     * @throws CircularDependencyException
     * @throws ContainerException
     * @throws NotFoundException
     * @throws StorageNotFoundException
     * @throws UnsupportedInvokerException
     */
    public function testBuildObject()
    {
        $container = $this->getContainer();
        $extractor = $this->makeExtractor();
        $container->values()->store(
            Dummy2::class,
            new Value(new Dummy2("John doe"))
        );

        $this->assertInstanceOf(
            Dummy1::class,
            $extractor->getObject(
                new MethodExtractionParameter(Dummy1::class, "foo"),
                $this->getContainer()
            )
        );

        $this->assertEquals("John doe", $extractor->getObject(
            new MethodExtractionParameter(Dummy2::class, "foo"),
            $container
        )->getFoo());

        $this->assertEquals("baz", $extractor->getObject(
            new MethodExtractionParameter(new Dummy2("baz"), "foo"),
            $container
        )->getFoo());
    }
}

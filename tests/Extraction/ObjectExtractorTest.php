<?php


namespace Atom\DI\Test\Extraction;

use Atom\DI\Exceptions\CircularDependencyException;
use Atom\DI\Exceptions\ContainerException;
use Atom\DI\Exceptions\NotFoundException;
use Atom\DI\Exceptions\StorageNotFoundException;
use Atom\DI\Extraction\ExtractionParameters\ContainerExtractionParameter;
use Atom\DI\Extraction\ExtractionParameters\ObjectExtractionParameter;
use Atom\DI\Extraction\ObjectExtractor;
use Atom\DI\Test\BaseTestCase;
use Atom\DI\Test\Misc\Dummy1;
use Atom\DI\Test\Misc\Dummy2;
use Atom\DI\Test\Misc\NotInstantiable;

class ObjectExtractorTest extends BaseTestCase
{
    private function makeExtractor(): ObjectExtractor
    {
        return new ObjectExtractor();
    }

    public function testIsValidExtractionParameter()
    {
        $extractor = $this->makeExtractor();
        $this->assertTrue($extractor->isValidExtractionParameter(new ObjectExtractionParameter("foo")));
        $this->assertFalse($extractor->isValidExtractionParameter(new ContainerExtractionParameter("foo")));
    }

    /**
     * @throws ContainerException
     * @throws CircularDependencyException
     * @throws NotFoundException
     * @throws StorageNotFoundException
     */
    public function testItThrowIfTheClassDoesntExists()
    {
        $extractor = $this->makeExtractor();
        $container = $this->getContainer();
        $this->expectException(ContainerException::class);
        $extractor->extract(new ObjectExtractionParameter("foo"), $container);
    }

    /**
     * @throws ContainerException
     * @throws CircularDependencyException
     * @throws NotFoundException
     * @throws StorageNotFoundException
     */
    public function testItThrowIfTheClassIsNotInstantiable()
    {
        $extractor = $this->makeExtractor();
        $container = $this->getContainer();
        $this->expectException(ContainerException::class);
        $extractor->extract(new ObjectExtractionParameter(NotInstantiable::class), $container);
    }

    /**
     * @throws CircularDependencyException
     * @throws ContainerException
     * @throws NotFoundException
     * @throws StorageNotFoundException
     */
    public function testItExtractWhenThereIsNoConstructorArgs()
    {
        $extractor = $this->makeExtractor();
        $container = $this->getContainer();
        $this->assertInstanceOf(
            Dummy1::class,
            $extractor->extract(
                new ObjectExtractionParameter(Dummy1::class),
                $container
            )
        );
    }

    /**
     * @throws CircularDependencyException
     * @throws ContainerException
     * @throws NotFoundException
     * @throws StorageNotFoundException
     */
    public function testItExtractWithConstructorParameters()
    {
        $extractor = $this->makeExtractor();
        $container = $this->getContainer();
        $instance = $extractor->extract(
            (new ObjectExtractionParameter(
                Dummy2::class,
                ["foo" => "baz"]
            )),
            $container
        );
        $this->assertEquals(
            "baz",
            $instance->getFoo()
        );
        $this->assertEquals(
            "bar",
            $instance->getBar()
        );
        $instance = $extractor->extract(
            (new ObjectExtractionParameter(
                Dummy2::class,
                ["foo" => "baz", "bar" => "baz"]
            )),
            $container
        );
        $this->assertEquals(
            "baz",
            $instance->getBar()
        );
    }

    /**
     * @throws CircularDependencyException
     * @throws ContainerException
     * @throws NotFoundException
     * @throws StorageNotFoundException
     */
    public function testItCacheTheResult()
    {
        $extractor = $this->makeExtractor();
        $container = $this->getContainer();
        $this->assertInstanceOf(
            Dummy1::class,
            $extractor->extract(
                new ObjectExtractionParameter(Dummy1::class, []),
                $container
            )
        );
        $this->assertFalse($container->values()->has(Dummy1::class));
         $this->assertInstanceOf(
             Dummy1::class,
             $extractor->extract(
                 new ObjectExtractionParameter(Dummy1::class, [], true),
                 $container
             )
         );
        $this->assertTrue($container->values()->has(Dummy1::class));
    }
}

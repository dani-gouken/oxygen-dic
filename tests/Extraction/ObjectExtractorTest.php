<?php


namespace Oxygen\DI\Test\Extraction;

use Oxygen\DI\Exceptions\CircularDependencyException;
use Oxygen\DI\Exceptions\ContainerException;
use Oxygen\DI\Exceptions\NotFoundException;
use Oxygen\DI\Exceptions\StorageNotFoundException;
use Oxygen\DI\Extraction\ExtractionParameters\ContainerExtractionParameter;
use Oxygen\DI\Extraction\ExtractionParameters\ObjectExtractionParameter;
use Oxygen\DI\Extraction\ObjectExtractor;
use Oxygen\DI\Test\BaseTestCase;
use Oxygen\DI\Test\Misc\Dummy1;
use Oxygen\DI\Test\Misc\Dummy2;
use Oxygen\DI\Test\Misc\NotInstantiable;

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
        $this->assertFalse($container->value()->has(Dummy1::class));
         $this->assertInstanceOf(
             Dummy1::class,
             $extractor->extract(
                 new ObjectExtractionParameter(Dummy1::class, [], true),
                 $container
             )
         );
        $this->assertTrue($container->value()->has(Dummy1::class));
    }
}

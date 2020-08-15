<?php

namespace Atom\DI\Test;

use InvalidArgumentException;
use Atom\DI\Definitions\CallFunction;
use Atom\DI\Contracts\DefinitionContract;
use Atom\DI\Contracts\StorageContract;
use Atom\DI\Definitions\DefinitionFactory;
use Atom\DI\DIC;
use Atom\DI\Exceptions\CircularDependencyException;
use Atom\DI\Exceptions\ContainerException;
use Atom\DI\Exceptions\NotFoundException;
use Atom\DI\Exceptions\StorageNotFoundException;
use Atom\DI\Exceptions\UnsupportedInvokerException;
use Atom\DI\Extraction\ContainerExtractor;
use Atom\DI\Extraction\ExtractionChain;
use Atom\DI\Extraction\ExtractionParameters\ValueExtractionParameter;
use Atom\DI\Extraction\FunctionExtractor;
use Atom\DI\Extraction\MethodExtractor;
use Atom\DI\Extraction\ObjectExtractor;
use Atom\DI\Extraction\ValueExtractor;
use Atom\DI\Storage\FactoryStorage;
use Atom\DI\Storage\SingletonStorage;
use Atom\DI\Storage\ValueStorage;
use Atom\DI\Test\Misc\CircularDependency\CDDummy2;
use Atom\DI\Test\Misc\Dummy1;
use Atom\DI\Test\Misc\Dummy2;
use Atom\DI\Definitions\Value;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Container\ContainerInterface;

class DICTest extends BaseTestCase
{
    /**
     * @group  DICTest
     */
    public function testTheContainerCanBeInstantiated()
    {
        $container = $this->getContainer();
        $this->assertInstanceOf(DIC::class, $container);
    }

    public function testItImplementPsr4()
    {
        $container = $this->getContainer();
        $this->assertInstanceOf(ContainerInterface::class, $container);
    }

    /**
     * @throws ContainerException
     */
    public function testItIsASingleton()
    {
        $container = $this->getContainer();
        $instance = DIC::getInstance();
        $this->assertEquals($container, $instance);
    }

    /**
     * @throws ContainerException
     */
    public function testGetInstance()
    {
        DIC::clearInstance();
        $container = DIC::getInstance();
        $instance = DIC::getInstance();
        $this->assertEquals($container, $instance);
    }

    public function testDefaultStorageAndExtractorsAreLoaded()
    {
        $container = $this->getContainer();
        $extractors = array_keys($container->getExtractors());
        $expectedExtractors = [
            MethodExtractor::class,
            ObjectExtractor::class,
            FunctionExtractor::class,
            ValueExtractor::class,
            ContainerExtractor::class
        ];
        foreach ($expectedExtractors as $extractor) {
            $this->assertContains($extractor, $extractors);
        }
    }

    /**
     * @throws ContainerException
     */
    public function testItCanResolvedARegisteredExtractorByHisNameOrThrowsOtherwise()
    {
        $this->assertInstanceOf(MethodExtractor::class, $this->getContainer()
            ->getExtractor(MethodExtractor::class));
        $this->expectException(ContainerException::class);
        $this->getContainer()->getExtractor("foo");
    }

    public function testHasExtractor()
    {
        $this->assertTrue($this->getContainer()->hasExtractor(MethodExtractor::class));
        $this->assertFalse($this->getContainer()->hasExtractor("foo"));
    }

    /**
     * @throws ContainerException
     * @throws StorageNotFoundException
     */
    public function testAddStorage()
    {
        /** @var StorageContract|MockObject $storage */
        $storage = $this->createMock(StorageContract::class);
        $storage->method('getStorageKey')
            ->willReturn('foo');
        $container = $this->getContainer();
        $container->addStorage($storage);
        $this->assertEquals($storage, $container->getStorage("foo"));
        $this->expectException(ContainerException::class);
        $container->addStorage($storage);
    }

    public function testHasStorage()
    {
        $container = $this->getContainer();
        $this->assertTrue($container->hasStorage("FACTORIES"));
        $this->assertFalse($container->hasStorage("foo"));
    }

    /**
     * @throws StorageNotFoundException
     */
    public function testGetStorage()
    {
        $storage = $this->getContainer();
        $this->assertInstanceOf(StorageContract::class, $storage->getStorage(FactoryStorage::STORAGE_KEY));
        $this->expectException(StorageNotFoundException::class);
        $storage->getStorage("foo");
    }

    public function testGetContainer()
    {
        $this->assertCount(4, $this->getContainer()->getContainer());
    }

    /**
     * @throws NotFoundException
     * @throws StorageNotFoundException
     */
    public function testGetDefaultStorage()
    {
        $container = $this->getContainer();
        $this->assertEquals(
            $container->getStorage($container->getDefaultStorageAlias()),
            $container->getDefaultStorage()
        );
    }

    public function testDefaultStorageAlias()
    {
        $this->assertEquals($this->getContainer()->getDefaultStorageAlias(), SingletonStorage::STORAGE_KEY);
    }

    /**
     * @throws StorageNotFoundException
     */
    public function testItCanLoadFactoryStorage()
    {
        $this->assertInstanceOf(FactoryStorage::class, $this->getContainer()->factories());
    }

    /**
     * @throws StorageNotFoundException
     */
    public function testItCanLoadSingletonStorage()
    {
        $this->assertInstanceOf(SingletonStorage::class, $this->getContainer()->singletons());
    }

    /**
     * @throws StorageNotFoundException
     */
    public function testItCanLoadValueStorage()
    {
        $this->assertInstanceOf(ValueStorage::class, $this->getContainer()->values());
    }

    /**
     * @throws ContainerException
     */
    public function testItCanExtractADefinition()
    {
        $container = $this->getContainer();
        /** @var DefinitionContract|MockObject $definition */
        $definition = $this->createMock(DefinitionContract::class);
        $definition->method("getExtractorClassName")->willReturn(ValueExtractor::class);
        $definition->method("getExtractionParameter")->willReturn(new ValueExtractionParameter("foo"));
        $this->assertEquals("foo", $container->extract($definition));
        /** @var DefinitionContract|MockObject $definition */
        $definition = $this->createMock(DefinitionContract::class);
        $definition->method("getExtractorClassName")->willReturn(MethodExtractor::class);
        $definition->method("getExtractionParameter")->willReturn(new ValueExtractionParameter("foo"));
        $this->expectException(ContainerException::class);
        $container->extract($definition);
    }

    /**
     * @throws ContainerException
     */
    public function testTheResolutionCallbackIsCalled()
    {
        $container = $this->getContainer();
        /** @var DefinitionContract|MockObject $definition */
        $definition = $this->createMock(DefinitionContract::class);
        $definition->method("getExtractorClassName")->willReturn(ValueExtractor::class);
        $definition->method("getExtractionParameter")->willReturn(new ValueExtractionParameter("foo"));
        $data = null;
        $testContainer = null;
        $definition->expects($this->any())
            ->method("getResolutionCallback")->willReturn(function ($value, $c) use (&$data, &$testContainer) {
                $data = $value;
                $testContainer = $c;
            });
        $container->extract($definition);
        $this->assertEquals("foo", $data);
        $this->assertInstanceOf(DIC::class, $testContainer);
    }

    /**
     * @throws CircularDependencyException
     * @throws ContainerException
     */
    public function testExtractDependency()
    {
        $container = $this->getContainer();
        /** @var DefinitionContract|MockObject $definition */
        $definition = $this->createMock(DefinitionContract::class);
        $definition->method("getExtractorClassName")->willReturn(ValueExtractor::class);
        $definition->method("getExtractionParameter")->willReturn(new ValueExtractionParameter("foo"));
        $this->assertEquals("foo", $container->extractDependency($definition, "foo"));
        $this->assertTrue($container->getExtractionChain()->contains("foo"));
    }

    /**
     * @throws CircularDependencyException
     * @throws ContainerException
     * @throws NotFoundException
     * @throws StorageNotFoundException
     * @throws UnsupportedInvokerException
     */
    public function testGet()
    {
        $container = $this->getContainer();
        $container->getExtractionChain()->append("bar");
        $container->getExtractionChain()->append("baz");

        $container->values()->store("foo", new Value("bar"));
        $this->assertEquals("bar", $container->get("foo"));
        $this->assertEquals("bar", $container->get("foo", ValueStorage::STORAGE_KEY));

        $this->assertCount(1, $container->getExtractionChain()->chain);
        $this->assertEquals("foo", $container->getExtractionChain()->chain[0]);
        $this->expectException(NotFoundException::class);
        $container->get("baz", null, [], false);

        $this->expectException(ContainerException::class);
        $container->get("baz", null, []);
    }

    /**
     * @throws CircularDependencyException
     * @throws ContainerException
     * @throws NotFoundException
     * @throws StorageNotFoundException
     * @throws UnsupportedInvokerException
     */
    public function testGetThrowsIfTheValueIsNotInTheStorageOrTheStorageDoesntExists()
    {
        $container = $this->getContainer();
        $container->values()->store("foo", new Value("bar"));
        $this->assertEquals("bar", $container->get("foo", ValueStorage::STORAGE_KEY));
        $this->expectException(NotFoundException::class);
        $container->get("foo", FactoryStorage::STORAGE_KEY, [], false);
        $this->expectException(StorageNotFoundException::class);
        $container->get("foo", "FOO", [], false);
    }

    /**
     * @throws CircularDependencyException
     * @throws ContainerException
     * @throws NotFoundException
     * @throws StorageNotFoundException
     * @throws UnsupportedInvokerException
     */
    public function testGetMakeTheObjectIfTheValueIsNotAvailable()
    {
        $container = $this->getContainer();
        $container->values()->store("foo", new Value("bar"));
        $this->assertInstanceOf(Dummy1::class, $container->get(Dummy1::class));
    }

    /**
     * @throws CircularDependencyException
     * @throws ContainerException
     * @throws NotFoundException
     * @throws StorageNotFoundException
     */
    public function testGetThrowsInCaseOfCircularDependency()
    {
        $container = $this->getContainer();
        $this->expectException(CircularDependencyException::class);
        $container->get(CDDummy2::class);
    }

    /**
     * @throws CircularDependencyException
     * @throws ContainerException
     * @throws NotFoundException
     * @throws StorageNotFoundException
     * @throws UnsupportedInvokerException
     */
    public function testGetDependency()
    {
        $container = $this->getContainer();
        $container->getExtractionChain()->append("jhon");
        $container->getExtractionChain()->append("doe");

        $container->values()->store("foo", new Value("bar"));
        $this->assertEquals("bar", $container->getDependency("foo"));
        $this->assertCount(3, $container->getExtractionChain()->chain);
    }

    /**
     * @throws CircularDependencyException
     * @throws ContainerException
     * @throws NotFoundException
     * @throws StorageNotFoundException
     * @throws UnsupportedInvokerException
     */
    public function testGetDependencyThrowsIfTheValueIsNotInTheStorageOrTheStorageDoesntExists()
    {
        $container = $this->getContainer();
        $container->values()->store("foo", new Value("bar"));
        $this->assertEquals("bar", $container->get("foo", ValueStorage::STORAGE_KEY));
        $this->expectException(NotFoundException::class);
        $container->getExtractionChain()->clear();
        $container->getDependency("foo", FactoryStorage::STORAGE_KEY, [], false);
        $this->expectException(StorageNotFoundException::class);
        $container->getDependency("foo", "FOO", [], false);
    }

    /**
     * @throws CircularDependencyException
     * @throws ContainerException
     * @throws NotFoundException
     * @throws StorageNotFoundException
     * @throws UnsupportedInvokerException
     */
    public function testGetDependencyMakeTheObjectIfTheValueIsNotAvailable()
    {
        $container = $this->getContainer();
        $container->values()->store("foo", new Value("bar"));
        $this->assertInstanceOf(Dummy1::class, $container->getDependency(Dummy1::class));
    }

    /**
     * @throws CircularDependencyException
     * @throws ContainerException
     * @throws NotFoundException
     * @throws StorageNotFoundException
     */
    public function testGetDependencyThrowsInCaseOfCircularDependency()
    {
        $container = $this->getContainer();
        $this->expectException(CircularDependencyException::class);
        $container->getDependency(CDDummy2::class);
    }

    /**
     * @throws StorageNotFoundException
     * @throws UnsupportedInvokerException
     */
    public function testHas()
    {
        $container = $this->getContainer();
        $container->values()->store("foo", new Value("bar"));
        $this->assertTrue($container->has("foo"));
        $this->assertTrue($container->has("foo", ValueStorage::STORAGE_KEY));
        $this->assertFalse($container->has("foo", FactoryStorage::STORAGE_KEY));
        $this->assertFalse($container->has("baz", FactoryStorage::STORAGE_KEY));
        $this->assertFalse($container->has("baz"));
        $this->assertFalse($container->has("baz", ValueStorage::STORAGE_KEY));
    }

    /**
     * @throws NotFoundException
     * @throws StorageNotFoundException
     * @throws UnsupportedInvokerException
     */
    public function testGetStorageFor()
    {
        $container = $this->getContainer();
        $container->values()->store("foo", new Value("bar"));
        $container->singletons()->store("bar", new Value("baz"));
        $this->assertInstanceOf(ValueStorage::class, $container->getStorageFor("foo"));
        $this->assertInstanceOf(SingletonStorage::class, $container->getStorageFor("bar"));
        $this->expectException(NotFoundException::class);
        $container->getStorageFor("baz");
    }


    public function testTheContainerImplementsArrayAccess()
    {
        $container = $this->getContainer();
        //STORE VALUES
        $container["foo"] = new Value("bar");
        $container["VALUES::foo"] = new Value("baz");
        $container["FACTORIES::bar"] = new CallFunction('Atom\DI\Test\Misc\returnFoo');
        //RETREIVE VALUES
        $this->assertEquals($container["foo"], "bar");
        $this->assertEquals($container["VALUES::foo"], "baz");
        $this->assertEquals($container["FACTORIES::bar"], "foo");
        $this->assertEquals($container["bar"], "foo");
        //UPDATE VALUES, cannot update SINGLETON(default) because it will return the same value everytimes
        $container["VALUES::foo"] = new Value("jhon");
        $container["FACTORIES::bar"] = new callFunction('Atom\DI\Test\Misc\returnBar');
        $this->assertEquals($container["VALUES::foo"], "jhon");
        $this->assertEquals($container["bar"], "bar");
        //UNSET VALUES
        unset($container["VALUES::foo"]);
        $this->expectException(ContainerException::class);
        $container["VALUES::foo"];
        $this->assertEquals($container["foo"], "bar");
        $this->assertTrue(isset($container["foo"]));
        $this->assertTrue($container->offsetExists("foo"));
        $this->assertFalse(isset($container["VALUES::foo"]));
        unset($container["foo"]);
        $this->assertFalse(isset($container["foo"]));
        $this->assertNull($container["foo"]);
        $this->assertEquals($container["bar"], "bar");
        //EXISTS
        $this->assertFalse(isset($container["VALUES::foo"]));
        $this->assertFalse(isset($container["foo"]));
        $this->assertFalse(isset($container["jhon"]));
        $this->assertTrue(isset($container["bar"]));
        $this->assertTrue(isset($container["FACTORIES::bar"]));
    }

    /**
     * @throws ContainerException
     */
    public function testItCanMakeObject()
    {
        $container = $this->getContainer();
        $this->assertInstanceOf(Dummy1::class, $container->make(Dummy1::class));
        $dummy2 = $container->make(Dummy2::class, ["foo" => "bar"]);
        $this->assertInstanceOf(Dummy2::class, $dummy2);
        $this->assertEquals("bar", $dummy2->getFoo());
        $this->expectException(ContainerException::class);
        $container->make(Dummy2::class);
    }

    public function testGetExtractors()
    {
        $container = $this->getContainer();
        $expectedExtractors = [
            MethodExtractor::class,
            ObjectExtractor::class,
            FunctionExtractor::class,
            ValueExtractor::class,
            ContainerExtractor::class
        ];
        foreach ($expectedExtractors as $extractor) {
            $this->assertArrayHasKey($extractor, $container->getExtractors());
        }
    }

    public function testGetExtractionChain()
    {
        $this->assertInstanceOf(ExtractionChain::class, $this->getContainer()->getExtractionChain());
    }

    public function testLazy()
    {
        $this->assertInstanceOf(DefinitionFactory::class, $this->getContainer()->lazy());
    }
    public function testAs()
    {
        $this->assertInstanceOf(DefinitionFactory::class, $this->getContainer()->as());
    }

    public function testGlobalResolutionCallback()
    {
        $container = $this->getContainer();
        $i = 0;
        $resolvedValue = null;
        $resolvedContainer = null;
        $container->resolved(function ($value, $container) use (&$i, &$resolvedValue, &$resolvedContainer) {
            $i++;
            $resolvedContainer = $container;
            $resolvedValue = $value;
        });
        $container->values()->store("foo", $container->as()->value("bar"));
        $container->values()->store("bar", $container->as()->value("baz"));

        $bar = $container->get("foo");
        $this->assertEquals(1, $i);
        $this->assertInstanceOf(DIC::class, $resolvedContainer);
        $this->assertEquals("bar", $resolvedValue);


        $baz = $container->get("bar");
        $this->assertEquals(2, $i);
        $this->assertInstanceOf(DIC::class, $resolvedContainer);
        $this->assertEquals("baz", $baz);
        $this->assertEquals("bar", $bar);

        $this->expectException(InvalidArgumentException::class);
        $container->resolved(function () {
        }, function () {
        });
    }

    public function testResolutionCallback()
    {
        $container = $this->getContainer();
        $resolvedValue = null;
        $resolvedContainer = null;
        $i = 0;
        $container->values()->store("foo", $container->as()->value("bar"));
        $container->resolved(
            "foo",
            function ($value, $container) use (&$i, &$resolvedValue, &$resolvedContainer) {
                $i+=1;
                $resolvedValue = $value;
                $resolvedContainer = $container;
                return "baz";
            }
        );

        $foo = $container->get("foo");
        $this->assertEquals(1, $i);
        $this->assertEquals($resolvedValue, "bar");
        $this->assertEquals($foo, "baz");
        $this->assertInstanceOf(DIC::class, $resolvedContainer);
    }
}

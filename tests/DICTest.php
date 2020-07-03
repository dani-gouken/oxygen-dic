<?php

use Oxygen\DI\Contracts\StorableContract;
use Oxygen\DI\Contracts\StorageContract;
use Oxygen\DI\DIC;
use Oxygen\DI\Exceptions\CircularDependencyException;
use Oxygen\DI\Exceptions\ContainerException;
use Oxygen\DI\Exceptions\NotFoundException;
use Oxygen\DI\Exceptions\StorageNotFoundException;
use Oxygen\DI\Extraction\ContainerExtractor;
use Oxygen\DI\Extraction\ExtractionChain;
use Oxygen\DI\Extraction\ExtractionParameters\ValueExtractionParameter;
use Oxygen\DI\Extraction\FunctionExtractor;
use Oxygen\DI\Extraction\MethodExtractor;
use Oxygen\DI\Extraction\ObjectExtractor;
use Oxygen\DI\Extraction\ValueExtractor;
use Oxygen\DI\Storage\FactoryStorage;
use Oxygen\DI\Storage\SingletonStorage;
use Oxygen\DI\Storage\ValueStorage;
use Oxygen\DI\Test\BaseTestCase;
use Oxygen\DI\Test\Misc\CircularDependency\CDDummy2;
use Oxygen\DI\Test\Misc\Dummy1;
use Oxygen\DI\Test\Misc\Dummy2;

class DICTest extends BaseTestCase
{
    /**
     * @group  DICTest
     */
    public function testTheContainerCanBeInstantiated()
    {
        $container = $this->getContainer();
        $this->assertInstanceOf(DIC::class, $container,);
    }

    public function testItImplementPsr4()
    {
        $container = $this->getContainer();
        $this->assertInstanceOf(Psr\Container\ContainerInterface::class, $container);
    }

    public function testItIsASingleton()
    {
        $container = $this->getContainer();
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

    public function testItCanReslovedARegisteredExtractorByHisNameOrThrowsOtherwise()
    {
        $this->assertInstanceOf(MethodExtractor::class, $this->getContainer()->getExtractor(MethodExtractor::class));
        $this->expectException(ContainerException::class);
        $this->getContainer()->getExtractor("foo");
    }

    public function testHasExtractor()
    {
        $this->assertTrue($this->getContainer()->hasExtractor(MethodExtractor::class));
        $this->assertFalse($this->getContainer()->hasExtractor("foo"));
    }

    public function testAddStorage()
    {
        /** @var StorageContract|\PHPUnit\Framework\MockObject\MockObject $storage*/
        $storage = $this->createMock(StorageContract::class);
        // Configurer le bouchon.
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

    public function testGetStorage()
    {
        $storage = $this->getContainer();
        $this->assertInstanceOf(StorageContract::class, $storage->getStorage(FactoryStorage::STORAGE_KEY));
        $this->expectException(StorageNotFoundException::class);
        $storage->getStorage("foo");
    }

    public function testGetContainer()
    {
        $this->assertCount(3, $this->getContainer()->getContainer());
    }

    public function testGetDefaultStorage()
    {
        $container = $this->getContainer();
        $this->assertEquals($container->getStorage($container->getDefaultStorageAlias()), $container->getDefaultStorage());
    }
    public function testDefautStorageAlias()
    {
        $this->assertEquals($this->getContainer()->getDefaultStorageAlias(), SingletonStorage::STORAGE_KEY);
    }

    public function testItCanLoadFactoryStorage()
    {
        $this->assertInstanceOf(FactoryStorage::class, $this->getContainer()->factory());
    }

    public function testItCanLoadSingletonStorage()
    {
        $this->assertInstanceOf(SingletonStorage::class, $this->getContainer()->singleton());
    }

    public function testItCanLoadValueStorage()
    {
        $this->assertInstanceOf(ValueStorage::class, $this->getContainer()->value());
    }

    public function testItCanExtractAStorable()
    {
        $container = $this->getContainer();
        /** @var StorableContract|\PHPUnit\Framework\MockObject\MockObject $storable */
        $storable = $this->createMock(StorableContract::class);
        $storable->method("getExtractorClassName")->willReturn(ValueExtractor::class);
        $storable->method("getExtractionParameter")->willReturn(new ValueExtractionParameter("foo"));
        $this->assertEquals("foo", $container->extract($storable));
        /** @var StorableContract|\PHPUnit\Framework\MockObject\MockObject $storable */
        $storable = $this->createMock(StorableContract::class);
        $storable->method("getExtractorClassName")->willReturn(MethodExtractor::class);
        $storable->method("getExtractionParameter")->willReturn(new ValueExtractionParameter("foo"));
        $this->expectException(ContainerException::class);
        $container->extract($storable);
    }

    public function testExtractDependency()
    {
        $container = $this->getContainer();
        /** @var StorableContract|\PHPUnit\Framework\MockObject\MockObject $storable */
        $storable = $this->createMock(StorableContract::class);
        $storable->method("getExtractorClassName")->willReturn(ValueExtractor::class);
        $storable->method("getExtractionParameter")->willReturn(new ValueExtractionParameter("foo"));
        $this->assertEquals("foo", $container->extractDependency($storable, "foo"));
        $this->assertTrue($container->getExtractionChain()->contains("foo"));
    }

    public function testGet()
    {
        $container = $this->getContainer();
        $container->getExtractionChain()->append("bar");
        $container->getExtractionChain()->append("baz");

        $container->value()->store("foo", value("bar"));
        $this->assertEquals("bar", $container->get("foo"));
        $this->assertEquals("bar", $container->get("foo", ValueStorage::STORAGE_KEY));

        $this->assertCount(1, $container->getExtractionChain()->chain);
        $this->assertEquals("foo", $container->getExtractionChain()->chain[0]);
        $this->expectException(NotFoundException::class);
        $container->get("baz", null, [], false);

        $this->expectException(ContainerException::class);
        $container->get("baz", null, []);
    }

    public function testGetThrowsIfTheValueIsNotInTheStorageOrTheStorageDoesntExists()
    {
        $container = $this->getContainer();
        $container->value()->store("foo", value("bar"));
        $this->assertEquals("bar", $container->get("foo", ValueStorage::STORAGE_KEY));
        $this->expectException(NotFoundException::class);
        $container->get("foo", FactoryStorage::STORAGE_KEY, [], false);
        $this->expectException(StorageNotFoundException::class);
        $container->get("foo", "FOO", [], false);
    }

    public function testGetMakeTheObjectIfTheValueIsNotAvailable()
    {
        $container = $this->getContainer();
        $container->value()->store("foo", value("bar"));
        $this->assertInstanceOf(Dummy1::class, $container->get(Dummy1::class));
    }

    public function testGetThrowsInCaseOfCircularDependency()
    {
        $container = $this->getContainer();
        $this->expectException(CircularDependencyException::class);
        $container->get(CDDummy2::class);
    }

    public function testGetDependency()
    {
        $container = $this->getContainer();
        $container->getExtractionChain()->append("jhon");
        $container->getExtractionChain()->append("doe");

        $container->value()->store("foo", value("bar"));
        $this->assertEquals("bar", $container->getDependency("foo"));
        $this->assertCount(3, $container->getExtractionChain()->chain);
    }


    public function testGetDependencyThrowsIfTheValueIsNotInTheStorageOrTheStorageDoesntExists()
    {
        $container = $this->getContainer();
        $container->value()->store("foo", value("bar"));
        $this->assertEquals("bar", $container->get("foo", ValueStorage::STORAGE_KEY));
        $this->expectException(NotFoundException::class);
        $container->getExtractionChain()->clear();
        $container->getDependency("foo", FactoryStorage::STORAGE_KEY, [], false);
        $this->expectException(StorageNotFoundException::class);
        $container->getDependency("foo", "FOO", [], false);
    }

    public function testGetDependencyMakeTheObjectIfTheValueIsNotAvailable()
    {
        $container = $this->getContainer();
        $container->value()->store("foo", value("bar"));
        $this->assertInstanceOf(Dummy1::class, $container->getDependency(Dummy1::class));
    }

    public function testGetDependencyThrowsInCaseOfCircularDependency()
    {
        $container = $this->getContainer();
        $this->expectException(CircularDependencyException::class);
        $container->getDependency(CDDummy2::class);
    }

    public function testHas()
    {
        $container = $this->getContainer();
        $container->value()->store("foo", value("bar"));
        $this->assertTrue($container->has("foo"));
        $this->assertTrue($container->has("foo", ValueStorage::STORAGE_KEY));
        $this->assertFalse($container->has("foo", FactoryStorage::STORAGE_KEY));
        $this->assertFalse($container->has("baz", FactoryStorage::STORAGE_KEY));
        $this->assertFalse($container->has("baz"));
        $this->assertFalse($container->has("baz", ValueStorage::STORAGE_KEY));
    }

    public function TestGetStorageFor()
    {
        $container = $this->getContainer();
        $container->value()->store("foo", value("bar"));
        $container->singleton()->store("bar", value("baz"));
        $this->assertInstanceOf(ValueStorage::class, $container->getStorageFor("foo"));
        $this->assertInstanceOf(SingletonStorage::class, $container->getStorageFor("bar"));
        $this->expectException(NotFoundException::class);
        $container->getStorageFor("baz");
    }

    /**
     * @covers DIC::offsetGet()
     * @covers DIC::offsetUnset()
     * @covers DIC::offsetSet()
     * @covers DIC::offsetExists()
     */
    public function testTheContainerImplementsArrayAccess()
    {
        $container = $this->getContainer();
        //STORE VALUES
        $container["foo"] = value("bar");
        $container["VALUES::foo"] = value("baz");
        $container["FACTORIES::bar"] = callFunction('Oxygen\DI\Test\Misc\returnFoo');
        //RETREIVE VALUES
        $this->assertEquals($container["foo"], "bar");
        $this->assertEquals($container["VALUES::foo"], "baz");
        $this->assertEquals($container["FACTORIES::bar"], "foo");
        $this->assertEquals($container["bar"], "foo");
        //UPDATE VALUES, cannot update SINGLETON(default) because it will return the same value everytimes
        $container["VALUES::foo"] = value("jhon");
        $container["FACTORIES::bar"] = callFunction('Oxygen\DI\Test\Misc\returnBar');
        $this->assertEquals($container["VALUES::foo"], "jhon");
        $this->assertEquals($container["bar"], "bar");
        //UNSET VALUES
        unset($container["VALUES::foo"]);
        $this->expectException(ContainerException::class);
        $container["VALUES::foo"];
        $this->assertEquals($container["foo"], "bar");
        $this->assertTrue(isset($container["foo"]));
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

    public function testItCanMakeObject()
    {
        $container = $this->getContainer();
        $this->assertInstanceOf(Dummy1::class, $container->make(Dummy1::class));
        $dummy2 = $container->make(Dummy2::class, ["foo" => "bar"]);
        $this->assertInstanceOf(Dummy2::class, $dummy2);
        $this->assertEquals("bar", $dummy2->getFoo());
        $this->expectException(ContainerException::class);
        $dummy2 = $container->make(Dummy2::class);
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
}

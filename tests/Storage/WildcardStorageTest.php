<?php


namespace Atom\DI\Test\Storage;

use Atom\DI\Definitions\Value;
use Atom\DI\Definitions\Wildcard;
use Atom\DI\Exceptions\NotFoundException;
use Atom\DI\Exceptions\UnsupportedInvokerException;
use Atom\DI\Storage\WildcardStorage;
use Atom\DI\Test\BaseTestCase;

class WildcardStorageTest extends BaseTestCase
{
    public function makeStorage(): WildcardStorage
    {
        return new WildcardStorage($this->getContainer());
    }

    public function testStore()
    {
        $store = $this->makeStorage();
        $definition = new Wildcard("bar");
        $definition->setPattern("foo");
        $store->store("foo", $definition);
        $this->assertTrue($store->has('foo'));
        $this->assertEquals($definition, $store->resolve('foo'));

        $definition = new Wildcard("bar");
        $store->store("baz", $definition);
        $this->assertTrue($store->has('baz'));
        $this->assertEquals($definition, $store->resolve('baz'));

        $this->expectException(UnsupportedInvokerException::class);
        $store->store("foo", new Value("foo"));
    }

    public function testHas()
    {
        $store = $this->makeStorage();
        $definition = new Wildcard("bar*");
        $store->store("foo.*", $definition);
        $this->assertFalse($store->has('foo'));
        $this->assertFalse($store->has('baz'));
        $this->assertTrue($store->has('foo.bar'));
        $this->assertTrue($store->has('foo.baz'));
    }

    public function testResolve()
    {
        $store = $this->makeStorage();
        $definition = new Wildcard("bar.*");
        $store->store("foo.*", $definition);
        $this->assertEquals($definition, $store->resolve("foo.baz"));
        $this->assertEquals('foo.baz', $store->resolve("foo.baz")->getClass());
        $this->expectException(NotFoundException::class);
        $store->resolve("baz.foo");
    }

    public function testGetStorageKey()
    {
        $this->assertEquals(
            WildcardStorage::STORAGE_KEY,
            $this->makeStorage()->getStorageKey()
        );
    }

    public function testAdd()
    {
        $storage = $this->makeStorage();
        $storage->add("foo.*", "bar.*");
        $this->assertTrue($storage->has("foo.baz"));
        /**
         * @var Wildcard $definition
         */
        $definition = $storage->resolve("foo.baz");
        $this->assertEquals("foo.baz", $definition->getClass());
        $this->assertEquals("foo.*", $definition->getPattern());
        $this->assertEquals("bar.*", $definition->getReplacement());
    }
}

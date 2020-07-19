<?php

namespace Oxygen\DI\Test\Storage;

use Oxygen\DI\AbstractStorable;
use Oxygen\DI\BuildObject;
use Oxygen\DI\Contracts\StorageContract;
use Oxygen\DI\Contracts\StorableContract;
use Oxygen\DI\Exceptions\ContainerException;
use Oxygen\DI\Exceptions\NotFoundException;
use Oxygen\DI\Exceptions\UnsupportedInvokerException;
use Oxygen\DI\Extraction\ExtractionParameters\ValueExtractionParameter;
use Oxygen\DI\Extraction\FunctionExtractor;
use Oxygen\DI\Extraction\ValueExtractor;
use Oxygen\DI\Get;
use Oxygen\DI\Storage\SingletonStorage;
use Oxygen\DI\Storage\ValueStorage;
use Oxygen\DI\Test\BaseTestCase;
use Oxygen\DI\Test\Misc\Dummy1;
use Oxygen\DI\Value;
use PHPUnit\Framework\MockObject\MockObject;

class ValueStorageTest extends BaseTestCase
{
    private function makeStorage(): ValueStorage
    {
        return new ValueStorage($this->getContainer());
    }
    public function testStorageKey()
    {
        $this->assertEquals("VALUES", $this->makeStorage()->getStorageKey());
    }

    /**
     * @throws UnsupportedInvokerException
     */
    public function testStoreWithInvalidExtractor()
    {
        $storable = $this->createMock(StorableContract::class);
        /** @var StorableContract|MockObject  $storable */
        $storable->method("getExtractorClassName")->willReturn(FunctionExtractor::class);

        $this->expectException(UnsupportedInvokerException::class);
        $this->makeStorage()->store("foo", $storable);
    }

    /**
     * @throws UnsupportedInvokerException
     * @throws ContainerException
     * @throws NotFoundException
     */
    public function testStore()
    {
        $storage = $this->makeStorage();
        $this->assertCount(0, $storage->getDescriptions());

        $storage->store("bar", new Value("baz"));
        $storage->store("foo", new BuildObject(Dummy1::class));

        $this->assertCount(2, $storage->getDescriptions());
        $this->assertInstanceOf(Dummy1::class, $storage->get("foo"));
        $this->assertEquals("baz", $storage->get("bar"));
    }
}

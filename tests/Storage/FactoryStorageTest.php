<?php

namespace Oxygen\DI\Test\Storage;

use Oxygen\DI\CallFunction;
use Oxygen\DI\CallMethod;
use Oxygen\DI\Contracts\StorableContract;
use Oxygen\DI\Exceptions\UnsupportedInvokerException;
use Oxygen\DI\Extraction\ExtractionParameters\ValueExtractionParameter;
use Oxygen\DI\Storage\FactoryStorage;
use Oxygen\DI\Test\BaseTestCase;
use Oxygen\DI\Test\Misc\Dummy1;
use Oxygen\DI\Test\Misc\Dummy2;

class FactoryStorageTest extends BaseTestCase
{
    private function makeStorage(): FactoryStorage
    {
        return new FactoryStorage($this->getContainer());
    }
    public function testStorageKey()
    {
        $this->assertEquals("FACTORIES", $this->makeStorage()->getStorageKey());
    }

    public function testStoreWithInvalidExtractor()
    {
        $storable = $this->createMock(StorableContract::class);
        /** @var StorableContract|\PHPUnit\Framework\MockObject\MockObject  $storable */
        $storable->method("getExtractorClassName")->willReturn(ValueExtractionParameter::class);

        $this->expectException(UnsupportedInvokerException::class);
        $this->makeStorage()->store("foo", $storable);
    }

    public function testStore()
    {
        $storage = $this->makeStorage();
        $this->assertCount(0, $storage->getDescriptions());

        $storage->store("bar", new CallFunction(function () {
            return "baz";
        }));
        $storage->store("foo", (new CallMethod("getFoo"))->on(new Dummy2("foo")));

        $this->assertCount(2, $storage->getDescriptions());
        $this->assertEquals("foo", $storage->get("foo"));
        $this->assertEquals("baz", $storage->get("bar"));
    }
}

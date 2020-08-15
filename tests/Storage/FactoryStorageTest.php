<?php

namespace Atom\DI\Test\Storage;

use Atom\DI\Definitions\CallFunction;
use Atom\DI\Definitions \CallMethod;
use Atom\DI\Contracts\DefinitionContract;
use Atom\DI\Exceptions\ContainerException;
use Atom\DI\Exceptions\NotFoundException;
use Atom\DI\Exceptions\UnsupportedInvokerException;
use Atom\DI\Extraction\ExtractionParameters\ValueExtractionParameter;
use Atom\DI\Storage\FactoryStorage;
use Atom\DI\Test\BaseTestCase;
use Atom\DI\Test\Misc\Dummy2;
use PHPUnit\Framework\MockObject\MockObject;

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

    /**
     * @throws UnsupportedInvokerException
     */
    public function testStoreWithInvalidExtractor()
    {
        $definition = $this->createMock(DefinitionContract::class);
        /** @var DefinitionContract|MockObject  $definition */
        $definition->method("getExtractorClassName")->willReturn(ValueExtractionParameter::class);

        $this->expectException(UnsupportedInvokerException::class);
        $this->makeStorage()->store("foo", $definition);
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

        $storage->store("bar", new CallFunction(function () {
            return "baz";
        }));
        $storage->store("foo", (new CallMethod("getFoo"))->on(new Dummy2("foo")));

        $this->assertCount(2, $storage->getDescriptions());
        $this->assertEquals("foo", $storage->get("foo"));
        $this->assertEquals("baz", $storage->get("bar"));
    }
}

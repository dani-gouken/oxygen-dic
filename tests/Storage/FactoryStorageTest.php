<?php

namespace Oxygen\DI\Test\Storage;

use Oxygen\DI\Definitions\CallFunction;
use Oxygen\DI\Definitions \CallMethod;
use Oxygen\DI\Contracts\DefinitionContract;
use Oxygen\DI\Exceptions\ContainerException;
use Oxygen\DI\Exceptions\NotFoundException;
use Oxygen\DI\Exceptions\UnsupportedInvokerException;
use Oxygen\DI\Extraction\ExtractionParameters\ValueExtractionParameter;
use Oxygen\DI\Storage\FactoryStorage;
use Oxygen\DI\Test\BaseTestCase;
use Oxygen\DI\Test\Misc\Dummy2;
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

<?php

namespace Oxygen\DI\Test\Storage;

use Oxygen\DI\Definitions\BuildObject;
use Oxygen\DI\Contracts\DefinitionContract;
use Oxygen\DI\Exceptions\ContainerException;
use Oxygen\DI\Exceptions\NotFoundException;
use Oxygen\DI\Exceptions\UnsupportedInvokerException;
use Oxygen\DI\Extraction\FunctionExtractor;
use Oxygen\DI\Storage\ValueStorage;
use Oxygen\DI\Test\BaseTestCase;
use Oxygen\DI\Test\Misc\Dummy1;
use Oxygen\DI\Definitions\Value;
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
        $definition = $this->createMock(DefinitionContract::class);
        /** @var DefinitionContract|MockObject  $definition */
        $definition->method("getExtractorClassName")->willReturn(FunctionExtractor::class);

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

        $storage->store("bar", new Value("baz"));
        $storage->store("foo", new BuildObject(Dummy1::class));

        $this->assertCount(2, $storage->getDescriptions());
        $this->assertInstanceOf(Dummy1::class, $storage->get("foo"));
        $this->assertEquals("baz", $storage->get("bar"));
    }
}

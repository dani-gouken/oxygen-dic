<?php

namespace Atom\DI\Test\Storage;

use Atom\DI\Definitions\AbstractDefinition;
use Atom\DI\Contracts\DefinitionContract;
use Atom\DI\Exceptions\ContainerException;
use Atom\DI\Exceptions\NotFoundException;
use Atom\DI\Extraction\ExtractionParameters\ValueExtractionParameter;
use Atom\DI\Extraction\ValueExtractor;
use Atom\DI\Storage\SingletonStorage;
use Atom\DI\Test\BaseTestCase;
use PHPUnit\Framework\MockObject\MockObject;

class SingletonStorageTest extends BaseTestCase
{
    private function makeStorage(): SingletonStorage
    {
        return new SingletonStorage($this->getContainer());
    }
    public function testStorageKey()
    {
        $this->assertEquals("SINGLETONS", $this->makeStorage()->getStorageKey());
    }

    /**
     * @throws NotFoundException
     * @throws ContainerException
     */
    public function testGet()
    {
        $storage = $this->makeStorage();
        /** @var MockObject|DefinitionContract  $definition */
        $definition = $this->createMock(AbstractDefinition::class);
        $definition
            ->method("getExtractionParameter")
            ->willReturn(new ValueExtractionParameter("bar"));
        $definition
            ->method("getExtractorClassName")
            ->willReturn(ValueExtractor::class);
        $definition
            ->expects($this->once())
            ->method("getExtractionParameter");

        $storage->store("foo", $definition);
        $storage->get("foo");
        $this->assertEquals("bar", $storage->get("foo"));
        $this->expectException(NotFoundException::class);
        $storage->get("baz");
    }
}

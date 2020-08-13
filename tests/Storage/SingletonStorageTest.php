<?php

namespace Oxygen\DI\Test\Storage;

use Oxygen\DI\Definitions\AbstractDefinition;
use Oxygen\DI\Contracts\DefinitionContract;
use Oxygen\DI\Exceptions\ContainerException;
use Oxygen\DI\Exceptions\NotFoundException;
use Oxygen\DI\Extraction\ExtractionParameters\ValueExtractionParameter;
use Oxygen\DI\Extraction\ValueExtractor;
use Oxygen\DI\Storage\SingletonStorage;
use Oxygen\DI\Test\BaseTestCase;
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

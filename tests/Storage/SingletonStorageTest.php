<?php

namespace Oxygen\DI\Test\Storage;

use Oxygen\DI\AbstractStorable;
use Oxygen\DI\Contracts\StorageContract;
use Oxygen\DI\Contracts\StorableContract;
use Oxygen\DI\Exceptions\NotFoundException;
use Oxygen\DI\Extraction\ExtractionParameters\ValueExtractionParameter;
use Oxygen\DI\Extraction\ValueExtractor;
use Oxygen\DI\Storage\SingletonStorage;
use Oxygen\DI\Storage\ValueStorage;
use Oxygen\DI\Test\BaseTestCase;

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

    public function testGet()
    {
        $storage = $this->makeStorage();
        /** @var \PHPUnit\Framework\MockObject\MockObject|StorableContract  $storable */
        $storable = $this->createMock(AbstractStorable::class);
        $storable
            ->method("getExtractionParameter")
            ->willReturn(new ValueExtractionParameter("bar"));
        $storable
            ->method("getExtractorClassName")
            ->willReturn(ValueExtractor::class);
        $storable
            ->expects($this->once())
            ->method("getExtractionParameter");

        $storage->store("foo", $storable);
        $storage->get("foo");
        $this->assertEquals("bar", $storage->get("foo"));
        $this->expectException(NotFoundException::class);
        $storage->get("baz");
    }
}

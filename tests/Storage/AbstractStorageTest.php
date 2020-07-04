<?php

namespace Oxygen\DI\Test\Storage;

use Oxygen\DI\Contracts\ExtractorContract;
use Oxygen\DI\DIC;
use Oxygen\DI\Exceptions\ContainerException;
use Oxygen\DI\Extraction\MethodExtractor;
use Oxygen\DI\Storage\AbstractStorage;
use Oxygen\DI\Test\BaseTestCase;

class AbstractStorageTest extends BaseTestCase
{
    public function testAddSuppotForExtractor()
    {
        $container = $this->getContainer();
        $storage = $this->getMockForAbstractClass(AbstractStorage::class, [$container]);
        $this->assertFalse($storage->supportExtractor($this->getMockClass(ExtractorContract::class)));
        $storage->addSupportForExtractor(MethodExtractor::class);
        $this->assertTrue($storage->supportExtractor(MethodExtractor::class));
        $this->expectException(ContainerException::class);
        $storage->addSupportForExtractor("foo");
    }
}

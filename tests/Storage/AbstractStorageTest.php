<?php

namespace Oxygen\DI\Test\Storage;

use Oxygen\DI\Contracts\ExtractorContract;
use Oxygen\DI\Exceptions\ContainerException;
use Oxygen\DI\Exceptions\NotFoundException;
use Oxygen\DI\Extraction\ExtractionParameters\ValueExtractionParameter;
use Oxygen\DI\Extraction\FunctionExtractor;
use Oxygen\DI\Storage\AbstractStorage;
use Oxygen\DI\Test\BaseTestCase;
use Oxygen\DI\Definitions\Value;
use TypeError;

class AbstractDefinitionTest extends BaseTestCase
{
    private function makeStorage(): AbstractStorage
    {
        $container = $this->getContainer();
        return $this->getMockForAbstractClass(AbstractStorage::class, [$container]);
    }

    /**
     * @throws ContainerException
     */
    public function testAddSupportForExtractor()
    {
        $storage = $this->makeStorage();
        /** @var ExtractorContract $extractor  */
        $extractor = $this->createMock(ExtractorContract::class);
        $storage->getContainer()->addExtractor($extractor);

        $this->assertFalse($storage->supportExtractor(get_class($extractor)));

        $storage->addSupportForExtractor(get_class($extractor));
        $this->assertTrue($storage->supportExtractor(get_class($extractor)));

        $this->expectException(ContainerException::class);
        $storage->addSupportForExtractor("foo");
    }

    /**
     * @throws ContainerException
     */
    public function testSupportExtractor()
    {
        $storage = $this->makeStorage();
        /** @var ExtractorContract $extractor  */
        $extractor = $this->createMock(ExtractorContract::class);
        $storage->getContainer()->addExtractor($extractor);

        $this->assertTrue($storage->supportExtractor(FunctionExtractor::class));
        $this->assertFalse($storage->supportExtractor(get_class($extractor)));
        $storage->addSupportForExtractor(get_class($extractor));
        $this->assertTrue($storage->supportExtractor(get_class($extractor)));
    }

    public function testHasAndContains()
    {
        $storage = $this->makeStorage();

        $storage->store("foo", new Value("bar"));
        $this->assertTrue($storage->has("foo"));
        $this->assertTrue($storage->contains("foo"));

        $this->assertFalse($storage->has("bar"));
        $this->assertFalse($storage->contains("bar"));
    }

    /**
     * @covers AbstractStorage::toGet()
     */
    public function testStore()
    {
        $storage = $this->makeStorage();
        $this->assertFalse($storage->has("foo"));
        $storage->store("foo", new Value("bar"));
        $this->assertTrue($storage->has("foo"));
    }

    /**
     * @throws NotFoundException
     */
    public function testResolve()
    {
        $storage = $this->makeStorage();
        $storage->store("foo", new Value("bar"));
        $this->assertInstanceOf(Value::class, $storage->resolve("foo"));
        /** @var ValueExtractionParameter $extractionParameter */
        $extractionParameter = $storage->resolve("foo")->getExtractionParameter();
        $this->assertEquals("bar", $extractionParameter->getValue());

        $this->expectException(NotFoundException::class);
        $storage->resolve("bar");
    }

    /**
     * @throws ContainerException
     * @throws NotFoundException
     */
    public function testGet()
    {
        $storage = $this->makeStorage();
        $storage->store("foo", new Value("bar"));
        $this->assertEquals("bar", $storage->get("foo"));

        $this->expectException(NotFoundException::class);
        $storage->resolve("bar");
    }

    /**
     * @throws ContainerException
     * @throws NotFoundException
     */
    public function testExtends()
    {
        $storage = $this->makeStorage();
        $storage->store("foo", new Value("bar"));

        $storage->extends("foo", function (Value $definition) {
            $definition->setValue("baz");
            return $definition;
        });
        $this->assertEquals("baz", $storage->get("foo"));

        $this->expectException(TypeError::class);
        $storage->extends("foo", function (Value $definition) {
            $definition->setValue("baz");
            return "baz";
        });
    }

    public function testGetDescriptions()
    {
        $storage = $this->makeStorage();
        $this->assertEquals($storage->getDescriptions(), []);
        $storage->store("foo", $value = new Value("bar"));
        $this->assertEquals($storage->getDescriptions(), ["foo" => $value]);
    }

    public function testRemove()
    {
        $storage = $this->makeStorage();
        $storage->store("foo", $value = new Value("bar"));
        $storage->store("bar", $value = new Value("baz"));

        $this->assertTrue($storage->has("foo"));
        $this->assertTrue($storage->has("bar"));

        $storage->remove("foo");
        $this->assertFalse($storage->has("foo"));
        $this->assertTrue($storage->has("bar"));
    }
}

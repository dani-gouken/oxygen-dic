<?php


use Oxygen\DI\DIC;
use Oxygen\DI\Extraction\ContainerExtractor;
use Oxygen\DI\Extraction\FunctionExtractor;
use Oxygen\DI\Extraction\MethodExtractor;
use Oxygen\DI\Extraction\ObjectExtractor;
use Oxygen\DI\Extraction\ValueExtractor;
use Oxygen\DI\Test\BaseTestCase;

class DICTest extends BaseTestCase
{
    /**
     * @group  DICTest
     */
    public function testTheContainerCanBeInstantiated()
    {
        $container = $this->getContainer();
        $this->assertInstanceOf(DIC::class, $container,);
    }

    public function testItImplementPsr4()
    {
        $container = $this->getContainer();
        $this->assertInstanceOf(Psr\Container\ContainerInterface::class, $container);
    }

    public function testItIsASingleton()
    {
        $container = $this->getContainer();
        $instance = DIC::getInstance();
        $this->assertEquals($container, $instance);
    }

    public function testDefaultStorageAndExtractorsAreLoaded()
    {
        $container = $this->getContainer();
        $extractors = array_keys($container->getExtractors());
        $expectedExtractors = [
            MethodExtractor::class,
            ObjectExtractor::class,
            FunctionExtractor::class,
            ValueExtractor::class,
            ContainerExtractor::class
        ];
        foreach ($expectedExtractors as $extractor){
            $this->assertContains($extractor,$extractors);
        }
    }

}
<?php


namespace Atom\DI\Test\Extraction;


use Atom\DI\Definitions\BuildObject;
use Atom\DI\Exceptions\CircularDependencyException;
use Atom\DI\Exceptions\ContainerException;
use Atom\DI\Exceptions\NotFoundException;
use Atom\DI\Extraction\ExtractionParameters\ValueExtractionParameter;
use Atom\DI\Extraction\ParameterResolverTrait;
use Atom\DI\Mapping\MappingItem;
use Atom\DI\Test\BaseTestCase;
use Atom\DI\Test\Misc\Dummy1;
use Atom\DI\Test\Misc\Dummy2;
use Atom\DI\Definitions\Value;
use ReflectionException;
use ReflectionFunction;

class ParameterResolverTraitTest extends BaseTestCase
{
    /**
     * @return ParameterResolverTrait
     */
    public function makeParameterResolver()
    {
        /**
         * @var $trait ParameterResolverTrait
         */
        $trait = $this->getObjectForTrait(ParameterResolverTrait::class);
        return $trait;
    }

    /**
     * @throws CircularDependencyException
     * @throws ContainerException
     * @throws NotFoundException
     * @throws ReflectionException
     */
    public function testSearchParameterValueWithDefaultValue()
    {
        $resolver = $this->makeParameterResolver();
        $reflectedFunction = new ReflectionFunction("Atom\\DI\\Test\\Misc\\returnDefaultValue");
        $parameter = $reflectedFunction->getParameters()[0];
        $value = $resolver->searchParameterValue($reflectedFunction, $parameter, $this->getContainer(),
            new ValueExtractionParameter("test"));
        $this->assertEquals("DefaultValue", $value);
    }

    /**
     * @throws CircularDependencyException
     * @throws ContainerException
     * @throws NotFoundException
     * @throws ReflectionException
     */
    public function testSearchParameterValueUsingParameterMapping()
    {
        $resolver = $this->makeParameterResolver();
        $reflectedFunction = new ReflectionFunction("Atom\\DI\\Test\\Misc\\returnValue");
        $parameter = $reflectedFunction->getParameters()[0];
        $extractionParameter = new ValueExtractionParameter("foo");
        $extractionParameter->getParameterMapping()->add(new MappingItem("value", new Value("bar")));
        $value = $resolver->searchParameterValue(
            $reflectedFunction,
            $parameter,
            $this->getContainer(),
            $extractionParameter
        );
        $this->assertEquals("bar", $value);
    }

    /**
     * @throws CircularDependencyException
     * @throws ContainerException
     * @throws NotFoundException
     * @throws ReflectionException
     */
    public function testSearchParameterValueUsingObjectMapping()
    {
        $resolver = $this->makeParameterResolver();
        $reflectedFunction = new ReflectionFunction("Atom\\DI\\Test\\Misc\\returnDummy2");
        $parameter = $reflectedFunction->getParameters()[0];
        $extractionParameter = new ValueExtractionParameter("foo");
        $extractionParameter->getObjectMapping()->add(new MappingItem(Dummy2::class, new Value(new Dummy2("bar"))));
        $value = $resolver->searchParameterValue(
            $reflectedFunction,
            $parameter,
            $this->getContainer(),
            $extractionParameter
        );
        $this->assertInstanceOf(Dummy2::class, $value);
        $this->assertEquals("bar", $value->getFoo());
    }

    /**
     * @throws CircularDependencyException
     * @throws ContainerException
     * @throws NotFoundException
     * @throws ReflectionException
     */
    public function testSearchParameterValueUsingAutoWiring()
    {
        $resolver = $this->makeParameterResolver();
        $reflectedFunction = new ReflectionFunction("Atom\\DI\\Test\\Misc\\returnDummy1");
        $parameter = $reflectedFunction->getParameters()[0];
        $extractionParameter = new ValueExtractionParameter("foo");
        $value = $resolver->searchParameterValue(
            $reflectedFunction,
            $parameter,
            $this->getContainer(),
            $extractionParameter
        );
        $this->assertInstanceOf(Dummy1::class, $value);
    }

    /**
     * @throws CircularDependencyException
     * @throws ContainerException
     * @throws NotFoundException
     * @throws ReflectionException
     */
    public function testSearchParameterValueUsingTheContainer()
    {
        $resolver = $this->makeParameterResolver();
        $reflectedFunction = new ReflectionFunction("Atom\\DI\\Test\\Misc\\returnDummy2");
        $container = $this->getContainer();
        $container->singletons()->store(
            Dummy2::class,
            (new BuildObject(Dummy2::class))
                ->withParameter("foo", new Value("bar"))
        );
        $parameter = $reflectedFunction->getParameters()[0];
        $extractionParameter = new ValueExtractionParameter("foo");
        $value = $resolver->searchParameterValue(
            $reflectedFunction,
            $parameter,
            $container,
            $extractionParameter
        );
        $this->assertInstanceOf(Dummy2::class, $value);
        $this->assertEquals($value->getFoo(), "bar");
    }

    /**
     * @throws CircularDependencyException
     * @throws ContainerException
     * @throws NotFoundException
     * @throws ReflectionException
     */
    public function testItThrowsIfItCantResolveTheParameter()
    {
        $resolver = $this->makeParameterResolver();
        $reflectedFunction = new ReflectionFunction("Atom\\DI\\Test\\Misc\\returnValue");
        $parameter = $reflectedFunction->getParameters()[0];
        $extractionParameter = new ValueExtractionParameter("foo");
        $this->expectException(ContainerException::class);
        $value = $resolver->searchParameterValue(
            $reflectedFunction,
            $parameter,
            $this->getContainer(),
            $extractionParameter
        );
    }


}
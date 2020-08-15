<?php


namespace Atom\DI\Extraction;

use Atom\DI\Contracts\ExtractionParameterContract;
use Atom\DI\Contracts\ExtractorContract;
use Atom\DI\Contracts\DefinitionContract;
use Atom\DI\DIC;
use Atom\DI\Exceptions\ContainerException;
use Atom\DI\Exceptions\NotFoundException;
use Atom\DI\Exceptions\CircularDependencyException;
use Atom\DI\Exceptions\StorageNotFoundException;
use Atom\DI\Extraction\ExtractionParameters\MethodExtractionParameter;
use ReflectionException;
use ReflectionMethod;

class MethodExtractor implements ExtractorContract
{
    use ParameterResolverTrait;

    /**
     * @param ExtractionParameterContract $params
     * @param DIC $container
     * @return mixed
     * @throws ContainerException
     * @throws NotFoundException
     * @throws ReflectionException
     * @throws StorageNotFoundException
     * @throws CircularDependencyException
     */
    public function extract(ExtractionParameterContract $params, DIC $container)
    {
        /**
         * @var MethodExtractionParameter $params
         */
        $object = $this->getObject($params, $container);
        $reflectedMethod = new ReflectionMethod($object, $params->getMethod());
        $methodParams = $this->getFunctionParameters($reflectedMethod, $container, $params, $params->getParameters());
        return $reflectedMethod->invokeArgs($object, $methodParams);
    }

    /**
     * @param MethodExtractionParameter $parameter
     * @param DIC $container
     * @return object
     * @throws CircularDependencyException
     * @throws ContainerException
     * @throws NotFoundException
     * @throws StorageNotFoundException
     */
    public function getObject(MethodExtractionParameter $parameter, DIC $container):object
    {
        if ($parameter->getClass() instanceof  DefinitionContract) {
            /**
             * @var DefinitionContract $definition
             */
            $definition = $parameter->getClass();
            return $container->extract($definition);
        }
        if ($parameter->classIsString()) {
            return $container->getDependency($parameter->getClass());
        }
        return $parameter->getClass();
    }

    public function isValidExtractionParameter(ExtractionParameterContract $params)
    {
        return $params instanceof MethodExtractionParameter;
    }
}

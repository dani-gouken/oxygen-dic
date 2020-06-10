<?php


namespace Oxygen\DI\Extraction;


use Oxygen\DI\Contracts\ExtractionParameterContract;
use Oxygen\DI\Contracts\ExtractorContract;
use Oxygen\DI\Contracts\StorableContract;
use Oxygen\DI\DIC;
use Oxygen\DI\Exceptions\ContainerException;
use Oxygen\DI\Exceptions\NotFoundException;
use Oxygen\DI\Exceptions\CircularDependencyException;
use Oxygen\DI\Exceptions\StorageNotFoundException;
use Oxygen\DI\Extraction\ExtractionParameters\MethodExtractionParameter;
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
        $object = $this->getObject($params,$container);
        /**
         * @var $params MethodExtractionParameter
         */
        $reflectedMethod = new ReflectionMethod($object, $params->getMethod());
        $methodParams = $this->getFunctionParameters($reflectedMethod, $container,$params,$params->getParameters());
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
    public function getObject(MethodExtractionParameter $parameter,DIC $container):object {
        if($parameter->getClass() instanceof  StorableContract){
            /**
             * @var $storable StorableContract
             */
            $storable = $parameter->getClass();
            return $container->extractDependency($storable, $parameter->getClassName());
        }
        if($parameter->classIsString()){
            return $container->getDependency($parameter->getClass());
        }
        return $parameter->getClass();
    }

    public function isValidExtractionParameter(ExtractionParameterContract $params)
    {
        return $params instanceof MethodExtractionParameter;
    }
}
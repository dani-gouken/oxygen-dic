<?php


namespace Oxygen\DI\Extraction;

use Exception;
use Oxygen\DI\Contracts\ExtractionParameterContract;
use Oxygen\DI\Contracts\ExtractorContract;
use Oxygen\DI\Contracts\StorageContract;
use Oxygen\DI\DIC;
use Oxygen\DI\Exceptions\CircularDependencyException;
use Oxygen\DI\Exceptions\ContainerException;
use Oxygen\DI\Exceptions\NotFoundException;
use Oxygen\DI\Exceptions\StorageNotFoundException;
use Oxygen\DI\Extraction\ExtractionParameters\ObjectExtractionParameter;
use Oxygen\DI\Value;
use ReflectionClass;

class ObjectExtractor implements ExtractorContract
{
    use ParameterResolverTrait;

    /**
     * @param ExtractionParameterContract $params
     * @param DIC $container
     * @return object
     * @throws ContainerException
     * @throws StorageNotFoundException
     * @throws CircularDependencyException
     * @throws NotFoundException
     */
    public function extract(ExtractionParameterContract $params, DIC $container)
    {
        /**
         * @var ObjectExtractionParameter $params
         */
        $className = $params->getClassName();
        try {
            $reflectedClass = new ReflectionClass($params->getClassName());
        } catch (Exception $e) {
            throw new ContainerException("Unable to resolve the class [$className]");
        }
        if (!$reflectedClass->isInstantiable()) {
            throw new ContainerException("The class [$className] is not instantiable");
        }
        $constructor = $reflectedClass->getConstructor();
        if (is_null($constructor)) {
            return $this->cacheAndReturn($params, $container->values(), $reflectedClass->newInstance());
        }
        $constructor->getParameters();
        $resolvedParameters = $this->getFunctionParameters(
            $constructor,
            $container,
            $params,
            $params->getConstructorArgs()
        );
        $result = $reflectedClass->newInstanceArgs($resolvedParameters);
        return $this->cacheAndReturn($params, $container->values(), $result);
    }

    /**
     * @param ObjectExtractionParameter $parameter
     * @param StorageContract $storage
     * @param $concrete
     * @return mixed
     */
    private function cacheAndReturn(ObjectExtractionParameter $parameter, StorageContract $storage, $concrete)
    {
        if ($parameter->canCacheResult()) {
            $storage->store($parameter->getClassName(), new Value($concrete));
        }
        return $concrete;
    }
    public function isValidExtractionParameter(ExtractionParameterContract $params)
    {
        return $params instanceof ObjectExtractionParameter;
    }
}

<?php


namespace Atom\DI\Extraction;

use Exception;
use Atom\DI\Contracts\ExtractionParameterContract;
use Atom\DI\Contracts\ExtractorContract;
use Atom\DI\Contracts\StorageContract;
use Atom\DI\DIC;
use Atom\DI\Exceptions\CircularDependencyException;
use Atom\DI\Exceptions\ContainerException;
use Atom\DI\Exceptions\NotFoundException;
use Atom\DI\Exceptions\StorageNotFoundException;
use Atom\DI\Extraction\ExtractionParameters\ObjectExtractionParameter;
use Atom\DI\Definitions\Value;
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

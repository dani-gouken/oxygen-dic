<?php


namespace Atom\DI\Extraction;

use InvalidArgumentException;
use Atom\DI\Contracts\ExtractionParameterContract;
use Atom\DI\DIC;
use Atom\DI\Exceptions\ContainerException;
use Atom\DI\Exceptions\CircularDependencyException;
use Atom\DI\Exceptions\NotFoundException;
use ReflectionException;
use ReflectionFunctionAbstract;
use ReflectionMethod;
use ReflectionParameter;

trait ParameterResolverTrait
{
    /**
     * @param ReflectionFunctionAbstract $method
     * @param ReflectionParameter $parameter
     * @param DIC $container
     * @param ExtractionParameterContract $extractionParameter
     * @return mixed
     * @throws ContainerException
     * @throws CircularDependencyException
     * @throws NotFoundException
     * @throws ReflectionException
     */
    public function searchParameterValue(
        ReflectionFunctionAbstract $method,
        ReflectionParameter $parameter,
        DIC $container,
        ExtractionParameterContract $extractionParameter
    ) {

        $paramName = $parameter->name;
        if ($parameter->isDefaultValueAvailable() || $parameter->isOptional()) {
            return $this->getParameterDefaultValue($parameter);
        }
        if ($extractionParameter->getParameterMapping()->hasMappingFor($paramName)) {
            $mapping = $extractionParameter->getParameterMapping()->getMappingFor($paramName);
            return $container->extractDependency($mapping->getDefinition(), $mapping->getMappedEntityKey());
        }
        $paramClass = $this->getParameterClassName($parameter);
        if (is_null($paramClass)) {
            if ($method instanceof ReflectionMethod) {
                throw new ContainerException("Cannot resolve argument [{$parameter->name}] when trying to call 
                the method [$method->name] on [$method->class]");
            }
            throw new ContainerException("Cannot resolve argument [{$parameter->name}] 
                when trying to call the method [$method->name]");
        }
        if ($extractionParameter->getObjectMapping()->hasMappingFor($paramClass)) {
            $mapping = $extractionParameter->getObjectMapping()->getMappingFor($paramClass);
            return $container->extractDependency($mapping->getDefinition(), $mapping->getMappedEntityKey());
        }
        return $container->getDependency($paramClass);
    }


    /**
     * Returns the default value of a function parameter.
     *
     * @param ReflectionParameter $parameter
     * @param ReflectionFunctionAbstract $function
     * @return mixed
     * @throws InvalidArgumentException
     * @throws ReflectionException
     */
    private function getParameterDefaultValue(ReflectionParameter $parameter)
    {
        return $parameter->getDefaultValue();
    }

    /**
     * return ReflectedFunction parameters as array
     *
     * @param ReflectionFunctionAbstract $method
     * @param DIC $container
     * @param ExtractionParameterContract $extractionParameter
     * @param array $args
     * @return array
     * @throws ContainerException
     * @throws CircularDependencyException
     * @throws NotFoundException
     */
    private function getFunctionParameters(
        ReflectionFunctionAbstract $method,
        DIC $container,
        ExtractionParameterContract $extractionParameter,
        $args = []
    ): array {
        $params = [];
        foreach ($method->getParameters() as $index => $parameter) {
            $paramName = $parameter->getName();
            if (array_key_exists($parameter->getName(), $args)) {
                $params[$paramName] = $args[$paramName];
                continue;
            }
            $params[$paramName] = $this->searchParameterValue($method, $parameter, $container, $extractionParameter);
        }
        return $params;
    }

    /**
     * return ReflectionParameter ClassName
     *
     * @param ReflectionParameter $param
     * @return String|null
     */
    private function getParameterClassName(ReflectionParameter $param): ?String
    {
        $paramClass = $param->getClass();
        if (is_null($paramClass)) {
            return null;
        }
        return $paramClass->getName();
    }
}

<?php
namespace ExpressiveDIC;

use Closure;
use ExpressiveDIC\Exceptions\InvalidArgumentException;
use ExpressiveDIC\Exceptions\NotFoundException;
use ExpressiveDIC\Exceptions\ContainerException;
use Psr\Container\ContainerInterface;

class DIC implements ContainerInterface, \ArrayAccess
{
    /**
     * @var array Store description
     */
    private $descriptions = [];

    public const SINGLETON = 'singleton';
    public const VALUE = 'value';
    public const DESCRIPTION = 'description';
    public const ALL = 'all';


    /**
     * @var array store available singletons
     */
    private $singletons = [];

    /**
     * @var array store values or instances
     */
    private $values = [];

    private static $instance;

    /**
     * describe description
     * @param String|int $alias
     * @param callable $description
     * @param string $type
     * @return DIC
     * @throws InvalidArgumentException
     */
    public function describe($alias, $description, $type = self::DESCRIPTION):self
    {
        switch ($type) {
            case (self::SINGLETON):
                $this->describeSingleton($alias, $description);
                break;
            case (self::VALUE):
                $this->describeValue($alias, $description);
                break;
            default:
                $this->descriptions[$alias] = $description;
        }
        return $this;
    }

    public function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    /**
     * describe a singleton
     * @param String|int $alias
     * @param callable $description
     * @return DIC
     */
    public function describeSingleton($alias, callable $description):self
    {
        $this->singletons[$alias] = $description;
        return $this;
    }

    /**
     * store an instance
     * @param String|int $alias
     * @param $instance
     * @return DIC
     * @throws InvalidArgumentException
     */
    public function describeValue($alias, $instance):self
    {
        if (is_callable($instance)) {
            throw new InvalidArgumentException('Argument of [describeInstance] should not be a callable');
        }

        $this->values[$alias] = $instance;
        return $this;
    }

    /**
     * describe descriptions
     * @param array $descriptions
     * @return DIC
     * @throws InvalidArgumentException
     */
    public function describeMany(array $descriptions):self
    {
        foreach ($descriptions as $alias => $description) {
            $this->describe($alias, $description);
        }
        return $this;
    }

    /**
     * describe singletons
     * @param array $descriptions
     * @return DIC
     */
    public function describeSingletons(array $descriptions):self
    {
        foreach ($descriptions as $alias => $description) {
            $this->describeSingleton($alias, $description);
        }
        return $this;
    }

    /**
     * describe instances
     * @param array $instances
     * @return DIC
     * @throws InvalidArgumentException
     */
    public function describeValues(array $instances):self
    {
        foreach ($instances as $alias => $instance) {
            $this->describeValue($alias, $instance);
        }
        return $this;
    }

    /**
     * return all the available descriptions as a key value array
     * @return array
     */
    public function getDescriptions():array
    {
        return $this->descriptions;
    }

    /**
     * return all the available descriptions as a key value array
     * @return array
     */
    public function getValues():array
    {
        return $this->values;
    }

    /**
     * return all the available singletons as a key value(closure) array
     * @return array
     */
    public function getSingletons():array
    {
        return $this->singletons;
    }


    /**
     * Return a value store inside de container
     * @param string $alias
     * @param string $container
     * @return mixed|void
     * @throws InvalidArgumentException
     * @throws NotFoundException
     */
    public function get($alias, $container = self::ALL)
    {
        if (!$this->has($alias)) {
            throw new NotFoundException("Cannot resolve [$alias]");
        }
        switch ($container) {
            case self::SINGLETON:
                return $this->getSingleton($alias);
            case self::DESCRIPTION:
                return $this->getDescription($alias);
            case self::VALUE:
                return $this->getValue($alias);
            default:
                if ($this->hasValue($alias)) {
                    return $this->getValue($alias);
                }
                if ($this->hasSingleton($alias)) {
                    return $this->getSingleton($alias);
                }
                if ($this->hasDescription($alias)) {
                    return $this->getDescription($alias);
                }
        }
    }

    /**
     * check if the an instance with the given alias exists
     * @param $alias
     * @return bool
     */
    public function hasValue($alias)
    {
        return array_key_exists($alias, $this->values);
    }

    /**
     * check if the a description with the given alias exists
     * @param $alias
     * @return bool
     */
    public function hasDescription($alias)
    {
        return array_key_exists($alias, $this->descriptions);
    }

    /**
     * check if the a factory with the given alias exists
     * @param $alias
     * @return bool
     */
    public function hasSingleton($alias)
    {
        return array_key_exists($alias, $this->singletons);
    }

    /**check if the container can build the object that has the given alias
     * @param string $alias
     * @param string $container
     * @return bool
     */
    public function has($alias, $container = self::ALL)
    {
        switch ($container) {
            case self::SINGLETON:
                return $this->hasSingleton($alias);
            case self::DESCRIPTION:
                return $this->hasDescription($alias);
            case self::VALUE:
                return $this->hasValue($alias);
            default:
                return $this->hasSingleton($alias) || $this->hasDescription($alias) || $this->hasValue($alias);
        }
    }

    /**
     * @param string $alias
     * @return mixed
     * @throws NotFoundException
     */
    public function getValue(string $alias)
    {
        if (!$this->hasValue($alias)) {
            throw new NotFoundException("Cannot resolve an instance with alias [$alias]");
        }
        return $this->getValues()[$alias];
    }

    /**
     * @param string $alias
     * @return mixed
     * @throws NotFoundException
     * @throws InvalidArgumentException
     */
    public function getSingleton(string $alias)
    {
        if (!$this->hasSingleton($alias)) {
            throw new NotFoundException("Cannot resolve a singleton with alias [$alias]");
        }
        if ($this->hasValue($alias)) {
            return $this->getValue($alias);
        }
        $instance = $this->getSingletons()[$alias]();
        $this->describeValue($alias, $instance);
        return $instance;
    }

    /**
     * @param string $alias
     * @return mixed
     * @throws NotFoundException
     */
    public function getDescription(string $alias)
    {
        if (!$this->hasDescription($alias)) {
            throw new NotFoundException("Cannot resolve a description with alias [$alias]");
        }
        $description = $this->getDescriptions()[$alias];
        if (is_callable($description)) {
            return $description();
        }
        return $description[$alias];
    }

    /**
     * @return array
     */
    private function getContainerTypes()
    {
        return [
          self::DESCRIPTION,
          self::VALUE,
          self::SINGLETON
        ];
    }

    /**
     * @param $offset
     * @return array
     */
    private function parseOffset($offset)
    {
        $result = explode('::', $offset);
        if (count($result)<=1) {
            return [
               'container' => self::ALL,
               'value'=>$offset
            ];
        }
        $container = $result[0];
        unset($result[0]);
        $value = implode('::', $result);
        if (!in_array($container, $this->getContainerTypes())) {
            return [
                'container' => self::ALL,
                'value'=>$offset
            ];
        }
        return [
            'container'=>$container,
            'value'=>$value
        ];
    }
    public function offsetExists($offset)
    {
        $data = $this->parseOffset($offset);
        return $this->has($data['value'], $data['container']);
    }

    /**
     * @param mixed $offset
     * @return mixed|void
     * @throws InvalidArgumentException
     * @throws NotFoundException
     */
    public function offsetGet($offset)
    {
        $data = $this->parseOffset($offset);
        return $this->get($data['value'], $data['container']);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @return DIC|void
     * @throws InvalidArgumentException
     */
    public function offsetSet($offset, $value)
    {
        $data = $this->parseOffset($offset);
        if (!is_callable($value)) {
            $data['container'] = self::VALUE;
        }
        return $this->describe($data['value'], $value, $data['container']);
    }

    public function offsetUnset($offset)
    {
        if ($this->hasSingleton($offset)) {
            unset($this->singletons[$offset]);
        }
        if ($this->hasDescription($offset)) {
            unset($this->descriptions[$offset]);
        }
        if ($this->hasValue($offset)) {
            unset($this->values[$offset]);
        }
        return;
    }


    /**
     * @param string $className
     * @param array $args
     * @param bool $cache
     * @return mixed|object|void
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws \ReflectionException
     * @throws ContainerException
     */
    public function make(string $className, array $args = [], bool $cache = false)
    {
        if ($className instanceof Closure) {
            return $className($this, $args);
        }
        if ($this->has($className)) {
            return $this->get($className);
        }
        $reflectedClass = new \ReflectionClass($className);
        if (!$reflectedClass->isInstantiable()) {
            throw new ContainerException("Unable to resolve the class [$className]");
        }
        $constructor = $reflectedClass->getConstructor();
        if (is_null($constructor)) {
            return $reflectedClass->newInstance();
        }
        $constructor->getParameters();
        $params = $this->getFunctionParameters($constructor,$args);

        $result =  $reflectedClass->newInstanceArgs($params);
        if ($cache) {
            try {
                $this->describeValue($className, $result);
            } catch (InvalidArgumentException $e) {
                throw $e;
            }
        }
        return $result;
    }

    /**
     * Call a methods or a function
     * @param   String $function
     * @param   array $args
     * @return  mixed
     * @throws  ContainerException
     * @throws  InvalidArgumentException
     * @throws  NotFoundException
     */
    public function call(String $function,$args=[]){
        try {
            $result = $this->parseClassMethodNotation($function);
            $function = $result['function'];
            $class = $result['class'];
            if (is_null($class)) {
                return $this->callFunction($function,$args);
            }
            return $this->callMethod($class,$function,$args);
        } catch (\ReflectionException $e) {
            throw new NotFoundException("Cannot generate reflection for ['$function']");
        }
    }

    /**
     * Call a function
     * @param  Strng function aname
     * @param  array Arggumets
     * @return mixed
     */
    public function callFunction(String $function,$args=[]){
        $reflectedFunction = new \ReflectionFunction($function);
        $closure = $reflectedFunction->getClosure();
        $params = $this->getFunctionParameters($reflectedFunction,$args);
        return call_user_func_array($closure,$params);
    }

    /**
     * call a method inside a class that the container can build 
     * @param  String the class that has the method
     * @param  string
     * @param  array
     * @return mixed
     */
    public function callMethod($class,$method='__invoke',$args=[]){
        $reflectedMethod = new \ReflectionMethod($class,$method);
        $params = $this->getFunctionParameters($reflectedMethod,$args);

        $instance = $this->make($class);
        return $reflectedMethod->invokeArgs($instance,$args);
    }

    private function parseClassMethodNotation($string){
        $result['class'] = null;
        $explodedString = explode('::', $string);
        if (count($explodedString)<=1) {
            $result['function'] = $string;
            return $result;
        }
        list($class, $method) = $explodedString;
        $result['class'] = $class;
        $result['function'] = $method;
        return $result;

    }

    /**
     * @param \ReflectionParameter $param
     * @return String|null
     */
    private function getParameterClassName(\ReflectionParameter $param):?String{
        $paramClass = $param->getClass();
        if(is_null($paramClass)){
            return null;
        }
        return $paramClass->getName();
    }

    /**
     * Returns the default value of a function parameter.
     *
     * @param \ReflectionParameter $parameter
     * @param \ReflectionFunctionAbstract $function
     * @return mixed
     * @throws InvalidArgumentException
     */
    private function getParameterDefaultValue(\ReflectionParameter $parameter, \ReflectionFunctionAbstract $function)
    {
        try {
            return $parameter->getDefaultValue();
        } catch (\ReflectionException $e) {
            throw new InvalidArgumentException(sprintf(
                'The parameter "%s" of %s has no type defined or guessable. It has a default value, '
                . 'but the default value can\'t be read through Reflection because it is a PHP internal class.',
                $parameter->getName(),
                $function->getName()
            ));
        }
    }

    /**
     * @param \ReflectionFunctionAbstract $method
     * @param array $args
     * @return array
     * @throws ContainerException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws \ReflectionException
     */
    private function getFunctionParameters(\ReflectionFunctionAbstract $method, $args=[]):array{
        $params = [];
        foreach ($method->getParameters() as $index => $parameter) {
            $paramName = $parameter->getName();
            if (array_key_exists($parameter->getName(), $args)) {
                $params[$paramName] = $args[$paramName];
                continue;
            }
            if ($parameter->isDefaultValueAvailable() || $parameter->isOptional()) {
                $params[$paramName] = $this->getParameterDefaultValue($parameter, $method);
            }
            $paramClass = $this->getParameterClassName($parameter);
            if (is_null($paramClass)) {
                throw new NotFoundException("Cannot resolve argument [{$paramName}]");
            }
            if ($this->has($paramClass)) {
                $params[$paramName] = $this->get($paramClass);
                continue;
            }
            $params[$paramName] = $this->make($paramClass);
        }
        return $params;
    }
}

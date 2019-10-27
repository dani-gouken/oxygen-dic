
| Visibility | Function |
|:-----------|:---------|
| public | <strong>call(</strong><em>\string</em> <strong>$function</strong>, <em>array</em> <strong>$args=array()</strong>)</strong> : <em>void</em><br /><em>Call a methods or a function
    @param   String $function
    @param   array $args
    @return  mixed
    @throws  ContainerException
    @throws  InvalidArgumentException
    @throws  NotFoundException</em> |
| public | <strong>callFunction(</strong><em>\string</em> <strong>$function</strong>, <em>array</em> <strong>$args=array()</strong>)</strong> : <em>void</em><br /><em>Call a function
    @param  Strng function aname
    @param  array Arggumets
    @return mixed</em> |
| public | <strong>callMethod(</strong><em>mixed</em> <strong>$class</strong>, <em>string</em> <strong>$method=`'__invoke'`</strong>, <em>array</em> <strong>$args=array()</strong>)</strong> : <em>void</em><br /><em>call a method inside a class that the container can build 
    @param  String the class that has the method
    @param  string
    @param  array
    @return mixed</em> |
| public | <strong>describe(</strong><em>mixed</em> <strong>$alias</strong>, <em>mixed</em> <strong>$description</strong>, <em>string</em> <strong>$type=`'description'`</strong>)</strong> : <em>void</em><br /><em>describe description
    @param String|int $alias
    @param callable $description
    @param string $type
    @return DIC
    @throws InvalidArgumentException</em> |
| public | <strong>describeMany(</strong><em>array</em> <strong>$descriptions</strong>)</strong> : <em>void</em><br /><em>describe descriptions
    @param array $descriptions
    @return DIC
    @throws InvalidArgumentException</em> |
| public | <strong>describeSingleton(</strong><em>mixed</em> <strong>$alias</strong>, <em>\callable</em> <strong>$description</strong>)</strong> : <em>void</em><br /><em>describe a singleton
    @param String|int $alias
    @param callable $description
    @return DIC</em> |
| public | <strong>describeSingletons(</strong><em>array</em> <strong>$descriptions</strong>)</strong> : <em>void</em><br /><em>describe singletons
    @param array $descriptions
    @return DIC</em> |
| public | <strong>describeValue(</strong><em>mixed</em> <strong>$alias</strong>, <em>mixed</em> <strong>$instance</strong>)</strong> : <em>void</em><br /><em>store an instance
    @param String|int $alias
    @param $instance
    @return DIC
    @throws InvalidArgumentException</em> |
| public | <strong>describeValues(</strong><em>array</em> <strong>$instances</strong>)</strong> : <em>void</em><br /><em>describe instances
    @param array $instances
    @return DIC
    @throws InvalidArgumentException</em> |
| public | <strong>get(</strong><em>mixed</em> <strong>$alias</strong>, <em>string</em> <strong>$container=`'all'`</strong>)</strong> : <em>mixed</em><br /><em>Return a value store inside de container
    @param string $alias
    @param string $container
    @return mixed|void
    @throws InvalidArgumentException
    @throws NotFoundException</em> |
| public | <strong>getDescription(</strong><em>\string</em> <strong>$alias</strong>)</strong> : <em>mixed</em> |
| public | <strong>getDescriptions()</strong> : <em>mixed</em><br /><em>return all the available descriptions as a key value array
    @return array</em> |
| public | <strong>getInstance()</strong> : <em>mixed</em> |
| public | <strong>getSingleton(</strong><em>\string</em> <strong>$alias</strong>)</strong> : <em>mixed</em> |
| public | <strong>getSingletons()</strong> : <em>mixed</em><br /><em>return all the available singletons as a key value(closure) array
    @return array</em> |
| public | <strong>getValue(</strong><em>\string</em> <strong>$alias</strong>)</strong> : <em>mixed</em> |
| public | <strong>getValues()</strong> : <em>mixed</em><br /><em>return all the available descriptions as a key value array
    @return array</em> |
| public | <strong>has(</strong><em>mixed</em> <strong>$alias</strong>, <em>string</em> <strong>$container=`'all'`</strong>)</strong> : <em>bool</em> |
| public | <strong>hasDescription(</strong><em>mixed</em> <strong>$alias</strong>)</strong> : <em>bool</em><br /><em>check if the a description with the given alias exists
    @param $alias
    @return bool</em> |
| public | <strong>hasSingleton(</strong><em>mixed</em> <strong>$alias</strong>)</strong> : <em>bool</em><br /><em>check if the a factory with the given alias exists
    @param $alias
    @return bool</em> |
| public | <strong>hasValue(</strong><em>mixed</em> <strong>$alias</strong>)</strong> : <em>bool</em><br /><em>check if the an instance with the given alias exists
    @param $alias
    @return bool</em> |
| public | <strong>make(</strong><em>\string</em> <strong>$className</strong>, <em>array</em> <strong>$args=array()</strong>, <em>\bool</em> <strong>$cache=false</strong>)</strong> : <em>void</em> |
| public | <strong>offsetExists(</strong><em>mixed</em> <strong>$offset</strong>)</strong> : <em>void</em> |
| public | <strong>offsetGet(</strong><em>mixed</em> <strong>$offset</strong>)</strong> : <em>void</em> |
| public | <strong>offsetSet(</strong><em>mixed</em> <strong>$offset</strong>, <em>mixed</em> <strong>$value</strong>)</strong> : <em>void</em> |
| public | <strong>offsetUnset(</strong><em>mixed</em> <strong>$offset</strong>)</strong> : <em>void</em> |

*This class implements \Psr\Container\ContainerInterface, \ArrayAccess*


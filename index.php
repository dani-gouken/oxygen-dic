<?php

use Oxygen\DI\BuildObject;
use Oxygen\DI\Exceptions\CircularDependencyException;
use Oxygen\DI\Exceptions\ContainerException;
use Oxygen\DI\Exceptions\NotFoundException;
use Oxygen\DI\Exceptions\StorageNotFoundException;
use Oxygen\DI\Exceptions\UnsupportedInvokerException;
use Oxygen\DI\Get;
use Oxygen\DI\Test\Misc\Dummy1;

require "vendor/autoload.php";

class DbConnection
{
    private $host;
    private $password;
    private $username;

    public function __construct($host, $password, $username)
    {
        $this->host = $host;
        $this->password = $password;
        $this->username = $username;
    }
}

class Database
{
    private $connection;

    public function __construct(DbConnection $connection)
    {
        $this->connection = $connection;
    }
}

class UserRepository
{
    /**
     * @var Database
     */
    private $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }
}

class User
{
    /**
     * @var string
     */
    private $username;
    /**
     * @var string
     */
    private $password;
    /**
     * @var UserRepository
     */
    private $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }
}

/**
 * @throws CircularDependencyException
 * @throws ContainerException
 * @throws NotFoundException
 * @throws StorageNotFoundException
 * @throws UnsupportedInvokerException
 */
function run()
{
    $dic = new Oxygen\DI\DIC();
    $dic->values()->store("db.username", $dic->as()->value("root"));
    $dic->values()->store("db.password", $dic->as()->value(""));
    $dic->values()->store("db.host", $dic->as()->value("localhost"));
    $dic->singletons()->store(
        DbConnection::class,
        $dic->as()->instantiateOf(DbConnection::class)
        ->with(User::class, $dic->as()->instantiateOf(User::class))
        ->withParameter("host", $dic->as()->storedValue("db.host"))
        ->withParameter("password", $dic->as()->storedValue("db.password"))
        ->withparameter("username", $dic->as()->storedValue("db.username"))
    );
    $dic->singletons()->store(
        DbConnection::class,
        $dic->as()->callTo("__invoke")->method()
            ->on($dic->lazy()->instantiateOf(User::class))
            ->withParameter("foo", $dic->as()->value("bar"))
            ->with(
                User::class,
                $dic->as()->value(UserRepository::class)
            )->resolved(function ($val) {
                log($val);
            })
    );
    function configuration(string $key)
    {
        return "secret" . $key;
    }

    $dic->factories()->store("auth.secret", $dic->callFunction("configuration", ["key" => "yelp"]));
    var_dump($dic->make(User::class));
    var_dump($dic->factories()->get("auth.secret"));
    $dic->factories()
    ->toGet(
        UserRepository::class,
        $dic->instantiate(UserRepository::class)
            ->bind(User::class, $dic->resolve(User::class))
            ->withParameter("test", $dic->value("2"))
            ->withConstructorParameters(["foo" => "bar"])
            ->resolved(function (UserRepository $repository) {
                log("resolved");
            })
    );
}

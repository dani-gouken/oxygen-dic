<?php

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


$dic = new Oxygen\DI\DIC();
$dic->value()->store("db.username", value("root"));
$dic->value()->store("db.password", value(""));
$dic->value()->store("db.host", value("localhost"));
$dic->singleton()->toGet(
    DbConnection::class,
    buildObject(DbConnection::class)
        ->giveParameter("host", get("db.host"))
        ->giveParameter("password", get("db.password"))
        ->giveParameter("username", get("db.username"))
);
function configuration(string $key)
{
    return "secret" . $key;
}
$dic->factory()->store("auth.secret", callFunction("configuration", ["key"=>"yelp"]));
var_dump($dic->make(User::class));
var_dump($dic->factory()->get("auth.secret"));

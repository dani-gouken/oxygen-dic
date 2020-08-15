<?php

use Oxygen\DI\Exceptions\CircularDependencyException;
use Oxygen\DI\Exceptions\ContainerException;
use Oxygen\DI\Exceptions\NotFoundException;
use Oxygen\DI\Exceptions\StorageNotFoundException;
use Oxygen\DI\Exceptions\UnsupportedInvokerException;

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
 * @throws ContainerException
 * @throws StorageNotFoundException
 * @throws UnsupportedInvokerException
 * @throws NotFoundException
 * @throws CircularDependencyException
 */
function run()
{
    $dic = new Oxygen\DI\DIC();
    // $host, $password, $username
    $dic->values()->store("db.host", $dic->as()->value('myhost'));
    $dic->values()->store("db.password", $dic->as()->value('mypassword'));
    $dic->values()->store("db.username", $dic->as()->value('myusername'));
    $dic->singletons()->store(
        DbConnection::class,
        $dic->as()->instanceOf(DbConnection::class)
            ->withParameter("host", $dic->as()->get("db.host"))
            ->withParameter("password", $dic->as()->get("db.password"))
            ->withParameter("username", $dic->as()->get("db.username"))
    );
    $dic->resolved("foo", function ($value, $container) {
    });
}
?>
<pre>
<?php  run(); ?>
<pre>

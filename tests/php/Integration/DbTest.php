<?php

namespace adamcameron\symfonythefasttrack\tests\Integration;

use adamcameron\symfonythefasttrack\Kernel;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\DependencyInjection\ContainerInterface;

/** @testdox DB tests */
class DbTest extends TestCase
{

    /** @testdox It can connect to the DB using DBAL */
    public function testDbalConnection()
    {
        $connection = $this->getDbalConnection();
        $result = $connection->executeQuery("SELECT version() AS version");

        $this->assertStringStartsWith("PostgreSQL 15", $result->fetchOne());
    }

    /** @testdox It has configured the Connection in the container with the correct DATABASE_URL */
    public function testContainerConnection()
    {
        $container = $this->getContainer();
        $connection = $container->get("doctrine.dbal.default_connection");
        $result = $connection->executeQuery("SELECT version() AS version");

        $this->assertStringStartsWith("PostgreSQL 15", $result->fetchOne());
    }

    private function getConnectionParameters() : stdClass
    {
        return (object) [
            "host" => getenv("POSTGRES_PRIMARY_HOST"),
            "port" => getenv("POSTGRES_PRIMARY_PORT"),
            "database" => getenv("POSTGRES_PRIMARY_DB"),
            "username" => getenv("POSTGRES_PRIMARY_USER"),
            "password" => getenv("POSTGRES_PRIMARY_PASSWORD")
        ];
    }

    private function getDbalConnection() : Connection
    {
        $parameters = $this->getConnectionParameters();
        return DriverManager::getConnection([
            "dbname" => $parameters->database,
            "user" => $parameters->username,
            "password" => $parameters->password,
            "host" => $parameters->host,
            "port" => $parameters->port,
            "driver" => "pdo_pgsql"
        ]);
    }

    private function getContainer(): ContainerInterface
    {
        $kernel = new Kernel("test", false);
        $kernel->boot();

        return $kernel->getContainer();
    }
}

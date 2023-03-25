<?php

namespace adamcameron\symfonythefasttrack\tests\Integration;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use PHPUnit\Framework\TestCase;
use stdClass;

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

    private function getConnectionParameters() : stdClass
    {
        return (object) [
            "host" => "database.backend",
            "port" => "5432",
            "database" => getenv("POSTGRES_DB"),
            "username" => getenv("POSTGRES_USER"),
            "password" => getenv("POSTGRES_PASSWORD")
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
}
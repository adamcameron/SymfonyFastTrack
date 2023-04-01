<?php

namespace adamcameron\symfonythefasttrack\tests\Functional\Doctrine;

use adamcameron\symfonythefasttrack\Kernel;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Connections\PrimaryReadReplicaConnection;
use Doctrine\DBAL\DriverManager;
use PHPUnit\Framework\TestCase;

/** @testdox Tests the default connection */
class DefaultConnectionTest extends TestCase
{
    /** @testdoxs It starts with a replica when reading */
    public function testReadReplica()
    {
        $connection = $this->getDefaultConnection();
        $result = $connection->executeQuery("SELECT version() AS version");

        $this->assertStringStartsWith("PostgreSQL 15", $result->fetchOne());
        $this->assertFalse($connection->isConnectedToPrimary(), "Should still be connected to the replica");
    }

    /** @testdox It can switch to the primary when writing */
    public function testPrimary()
    {
        $connection = $this->getDefaultConnection();
        $this->assertFalse($connection->isConnectedToPrimary(), "Should have started on the replica");

        $connection->beginTransaction();
        $connection->executeStatement("INSERT INTO test (value) VALUES (?)", ["TEST_VALUE"]);
        $result = $connection->executeQuery("SELECT value FROM test ORDER BY id DESC LIMIT 1");
        $connection->rollback();

        $this->assertEquals("TEST_VALUE", $result->fetchOne());
        $this->assertTrue(
            $connection->isConnectedToPrimary(),
            "Should have been switched to the primary after writing"
        );
    }

    /** @testdox Writing to primary definitely does not impact the replica */
    public function testWritingToPrimaryDoesNotImpactReplica()
    {
        $sqlForCount = "SELECT COUNT(1) AS count FROM test";

        $primaryConnection = $this->getPrimaryConnection();
        $replicaConnection = $this->getReplicaConnection();

        $initialPrimaryCount = $primaryConnection->executeQuery($sqlForCount)->fetchOne();
        $initialReplicaCount = $replicaConnection->executeQuery($sqlForCount)->fetchOne();
        $this->assertNotEquals(
            $initialPrimaryCount,
            $initialReplicaCount,
            "Test aborted as the test requires the DBs to NOT be in sync (and they are)"
        );

        $defaultConnection = $this->getDefaultConnection();
        $this->assertFalse($defaultConnection->isConnectedToPrimary(), "Connection did not start on replica");

        $initialDefaultCount = $defaultConnection->executeQuery($sqlForCount)->fetchOne();
        $this->assertEquals($initialDefaultCount, $initialReplicaCount, "Row count from default should match replica");

        $defaultConnection->executeStatement("INSERT INTO test (value) VALUES (?)", ["TEST_VALUE"]);
        $this->assertTrue(
            $defaultConnection->isConnectedToPrimary(),
            "Connection did not switch to primary after INSERT"
        );

        $countFromDefault = $defaultConnection->executeQuery($sqlForCount)->fetchOne();
        $countFromPrimary = $primaryConnection->executeQuery($sqlForCount)->fetchOne();
        $countFromReplica = $replicaConnection->executeQuery($sqlForCount)->fetchOne();

        $this->assertEquals($countFromDefault, $countFromPrimary, "Row count from default should match primary");
        $this->assertEquals($initialReplicaCount, $countFromReplica, "Row count from replica should not have changed");
    }

    private function getPrimaryConnection(): Connection
    {
        return DriverManager::getConnection([
            "dbname" => getenv("POSTGRES_PRIMARY_DB"),
            "user" => getenv("POSTGRES_PRIMARY_USER"),
            "password" => getenv("POSTGRES_PRIMARY_PASSWORD"),
            "host" => getenv("POSTGRES_PRIMARY_HOST"),
            "port" => getenv("POSTGRES_PRIMARY_PORT"),
            "driver" => "pdo_pgsql"
        ]);
    }

    private function getReplicaConnection(): Connection
    {
        return DriverManager::getConnection([
            "dbname" => getenv("POSTGRES_REPLICA_DB"),
            "user" => getenv("POSTGRES_REPLICA_USER"),
            "password" => getenv("POSTGRES_REPLICA_PASSWORD"),
            "host" => getenv("POSTGRES_REPLICA_HOST"),
            "port" => getenv("POSTGRES_REPLICA_PORT"),
            "driver" => "pdo_pgsql"
        ]);
    }

    public function getDefaultConnection(): PrimaryReadReplicaConnection
    {
        $kernel = new Kernel("test", true);
        $kernel->boot();

        $container = $kernel->getContainer();

        return $container->get("doctrine.dbal.default_connection");
    }
}

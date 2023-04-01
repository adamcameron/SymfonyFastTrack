<?php

namespace adamcameron\symfonythefasttrack\tests\Functional\Doctrine;

use adamcameron\symfonythefasttrack\Kernel;
use Doctrine\DBAL\Connections\PrimaryReadReplicaConnection;
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

    public function getDefaultConnection(): PrimaryReadReplicaConnection
    {
        $kernel = new Kernel("test", true);
        $kernel->boot();

        $container = $kernel->getContainer();

        return $container->get("doctrine.dbal.default_connection");
    }
}

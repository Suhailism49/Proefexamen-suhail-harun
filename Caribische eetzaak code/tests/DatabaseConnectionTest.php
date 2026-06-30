<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../src/DatabaseConnection.php';

final class DatabaseConnectionTest extends TestCase
{
    public function testCreatesASqliteConnectionWithProvidedConfiguration(): void
    {
        $pdo = createDatabaseConnection([
            'driver' => 'sqlite',
            'path' => ':memory:',
            'username' => '',
            'password' => '',
        ]);

        $this->assertInstanceOf(PDO::class, $pdo);
        $this->assertSame('sqlite', $pdo->getAttribute(PDO::ATTR_DRIVER_NAME));
    }
}

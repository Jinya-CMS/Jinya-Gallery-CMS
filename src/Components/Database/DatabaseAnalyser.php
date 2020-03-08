<?php

namespace Jinya\Components\Database;

use Doctrine\DBAL\Connection;

class DatabaseAnalyser implements DatabaseAnalyserInterface
{
    private Connection $connection;

    /**
     * DatabaseAnalyser constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @inheritDoc
     */
    public function getGlobalVariables(): array
    {
        return $this->connection->fetchAll('SHOW GLOBAL VARIABLES');
    }

    /**
     * @inheritDoc
     */
    public function getLocalVariables(): array
    {
        return $this->connection->fetchAll('SHOW VARIABLES');
    }
}
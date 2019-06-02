<?php

namespace Jinya\Command;

use Jinya\Components\Database\DatabaseMigratorInterface;
use Jinya\Components\Database\SchemaToolInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class CreateDbCommand extends Command
{
    /** @var DatabaseMigratorInterface */
    private $databaseMigrator;

    /** @var SchemaToolInterface */
    private $schemaTool;

    /** @var string */
    private $kernelProjectDir;

    /**
     * InstallCommand constructor.
     * @param DatabaseMigratorInterface $databaseMigrator
     * @param SchemaToolInterface $schemaTool
     * @param string $kernelProjectDir
     */
    public function __construct(
        DatabaseMigratorInterface $databaseMigrator,
        SchemaToolInterface $schemaTool,
        string $kernelProjectDir
    ) {
        parent::__construct();
        $this->databaseMigrator = $databaseMigrator;
        $this->schemaTool = $schemaTool;
        $this->kernelProjectDir = $kernelProjectDir;
    }

    protected function configure()
    {
        $this
            ->setName('jinya:first-run:create-db')
            ->setDescription('Creates the database');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->schemaTool->createSchema();
        $this->databaseMigrator->activateAllMigrations();
        $fs = new Filesystem();
        $fs->touch($this->kernelProjectDir . '/config/admin.lock');
    }
}
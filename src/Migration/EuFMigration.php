<?php

namespace Plenta\TooltipBundle\Migration;

use Composer\InstalledVersions;
use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;
use Plenta\TooltipBundle\Controller\OverlayController;

class EuFMigration extends AbstractMigration
{
    public function __construct(protected Connection $connection)
    {
    }

    public function shouldRun(): bool
    {
        if (class_exists('EuF_Overlay\ModuleOverlay')) {
            return false;
        }

        $schemaManager = $this->connection->createSchemaManager();

        if (!$schemaManager->tablesExist(['tl_module'])) {
            return false;
        }

        if (!array_key_exists('type', $schemaManager->listTableColumns('tl_module'))) {
            return false;
        }

        return (bool) $this->connection->fetchOne('SELECT true FROM tl_module WHERE type = :overlay LIMIT 1', ['overlay' => 'euf_overlay']);
    }

    public function run(): MigrationResult
    {
        $this->connection->prepare('UPDATE tl_module SET type = :type WHERE type = :overlay')->executeQuery([
            'overlay' => 'euf_overlay',
            'type' => OverlayController::TYPE,
        ]);

        return $this->createResult(true);
    }
}

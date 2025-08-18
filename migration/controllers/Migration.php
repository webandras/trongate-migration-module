<?php

declare(strict_types=1);

/**
 * A simple migration runner module for Trongate framework
 */
final class Migration extends Trongate
{
    private MigrationService $migration_service;

    public function __construct(?string $module_name = null)
    {
        if (strtolower(ENV) !== 'dev') {
            http_response_code(403); // forbidden
            echo "Migrations disabled since not in 'dev' mode.";
            die();
        }
        parent::__construct($module_name);

        // initialize migration service
        require_once dirname(__DIR__) . '/services/MigrationService.php';
        $this->migration_service = new MigrationService();
    }

    /**
     * Run or revert all migrations
     *
     * @param string $direction = 'up'|'down'
     *
     * @return void
     *
     * @throws Exception
     */
    public function run(string $direction): void
    {
        settype($direction, 'string');

        // Create the migrations table to store migration records
        if ($direction === 'up' && !$this->migration_service->_is_table_exists()) {
            $this->migration_service->_create_migrations_table();
        }

        // Migration file list
        // Migrations should be stored in the migrations folder (create it) in the project root
        $migrations = array_filter(glob(dirname(__DIR__, 3) . '/migrations/*.php'));

        if (sizeof($migrations) > 0) {
            foreach ($migrations as $migration) {
                // Get the filename part
                $parts = explode('/', $migration);

                if (isset($parts)) {
                    $filename = $parts[count($parts) - 1];
                    $table_parts = explode('_', $filename);
                    $table = $table_parts[count($table_parts) - 2];

                    // Get the migration class
                    $run = require_once (dirname(__DIR__, 3) . '/migrations/' . $filename);

                    try {
                        if ($direction === 'up') {
                            $record = $this->migration_service->get_where_custom(
                                'migration',
                                $filename,
                                '=',
                                'id',
                                $this->migration_service::MIGRATIONS,
                                1
                            );

                            // Run migration if it hasn't been run yet
                            if (empty($record)) {
                                $run->up();
                                // Insert migration record
                                $this->migration_service->_insert_migration($filename, 1);
                            } else if ($record[0]->processed == 0) {
                                // Run migration if its state is unprocessed
                                $run->up();
                                // Update migration record
                                $this->migration_service->_update_migration($filename, 1);
                            }
                        } else if ($direction === 'down') {
                            // Revert migration
                            $run->down();

                            // Update migration record
                            $this->migration_service->_update_migration($filename, 0);
                        }
                    } catch (\Exception $ex) {
                        var_dump($ex);
                        exit;
                    }
                }
            }
        }
        echo ucfirst($direction) . '...OK!';
        exit;
    }
}

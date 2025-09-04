<?php

declare(strict_types=1);

final class Migration_service extends Model
{
    /**
     * Table name where to store migration records
     */
    const string MIGRATIONS = 'migrations';

    /**
     * Check if table needs to be created
     */
    public function is_table_exists(): bool
    {
        $sql = 'SHOW TABLES LIKE :table';
        $rows = $this->query_bind($sql, ['table' => self::MIGRATIONS], 'array');

        return !empty($rows);
    }

    /**
     * Insert migration record
     */
    public function insert_migration(string $filename, int $is_processed): void
    {
        $this->insert([
            'migration' => $filename,
            'processed' => $is_processed,
        ], self::MIGRATIONS);
    }

    /**
     * Update migration record
     */
    public function update_migration(string $filename, int $is_processed): void
    {
        $this->update_where('migration', $filename, [
            'processed' => $is_processed,
        ], self::MIGRATIONS);
    }

    /**
     * Creates the migrations table if it exists
     *
     * @throws Exception
     */
    public function create_migrations_table(): void
    {
        $this->exec(
            'CREATE TABLE IF NOT EXISTS `' . self::MIGRATIONS . "` (
                    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                    `migration` varchar(255) NOT NULL,
                    `processed` TINYINT DEFAULT 0,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE 'utf8mb4_general_ci' AUTO_INCREMENT=1;"
        );
    }
}

<?php

/* Create posts table */
return new class extends Model {
    public function up(): void
    {
        $this->exec(
            "CREATE TABLE IF NOT EXISTS `posts` (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                url_string VARCHAR(255) NOT NULL UNIQUE,
                title VARCHAR(255) NOT NULL,
                content TEXT NOT NULL,
                created_at DATETIME DEFAULT NOW(),
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE 'utf8mb4_general_ci' AUTO_INCREMENT=1;"
        );

    }

    public function down(): void
    {
        $this->exec("DROP TABLE `posts`;");
    }
};

# Trongate Migration Runner module

Author: András Gulácsi
Licence: (Copyright) MIT 2023-2025
Version: 1.0.0

## Install

1. Copy `migration` folder into the ` modules` folder of your Trongate project's root folder
2. Create the `migrations` folder in the root of your Trongate project. Copy the example (`2023_11_04_001_create_posts_table.php`) there.

## Usage

1. Go to this url to run all (not-yet-run) migrations: http://localhost/{your_project_folder}/migration/run/up
2. Revert all migrations:  http://localhost/{your_project_folder}/migration/run/down

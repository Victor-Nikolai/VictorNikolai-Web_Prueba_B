<?php

require_once './vendor/autoload.php';

// --- CORRECCIÓN DE ERROR "Class BaseMigration not found" ---
// Cargamos manualmente la clase BaseMigration que está en la carpeta generator
// Usamos __DIR__ para asegurar que la ruta sea absoluta desde la raíz
$baseMigrationPath = __DIR__ . '/database/generator/BaseMigration.php';

if (file_exists($baseMigrationPath)) {
    require_once $baseMigrationPath;
}

// --- CARGA DE DEPENDENCIAS DE SLIM ---
$settings = require './src/settings.php';
$app = new \Slim\App($settings);
$container = $app->getContainer();
$container->register(new \Conduit\Services\Database\EloquentServiceProvider());
$config = $container['settings']['database'];

return
[
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/db/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'production',
        'production' => [
            'adapter' => getenv('DB_ADAPTER') ?: 'mysql',
            'host'    => getenv('DB_HOST') ?: '127.0.0.1',
            'name'    => getenv('DB_NAME') ?: 'conduit',
            'user'    => getenv('DB_USER') ?: 'conduit_user',
            'pass'    => getenv('DB_PASS') ?: 'secret',
            'port'    => getenv('DB_PORT') ?: '3306',
            'charset' => 'utf8',
        ]
    ]
];
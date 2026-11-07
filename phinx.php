<?php

require_once __DIR__ . '/vendor/autoload.php';

// --- CARGA DE CLASE BASE DE MIGRACIONES ---
$baseMigrationPath = __DIR__ . '/database/generator/BaseMigration.php';
if (file_exists($baseMigrationPath)) {
    require_once $baseMigrationPath;
}

// --- RESOLUCIÓN ROBUSTA DE VARIABLES DE ENTORNO ---
$dbAdapter = getenv('DB_ADAPTER') ?: ($_ENV['DB_ADAPTER'] ?? ($_SERVER['DB_ADAPTER'] ?? 'mysql'));
$dbHost    = getenv('DB_HOST')    ?: ($_ENV['DB_HOST']    ?? ($_SERVER['DB_HOST']    ?? '127.0.0.1'));
$dbName    = getenv('DB_NAME')    ?: ($_ENV['DB_NAME']    ?? ($_SERVER['DB_NAME']    ?? 'conduit'));
$dbUser    = getenv('DB_USER')    ?: ($_ENV['DB_USER']    ?? ($_SERVER['DB_USER']    ?? 'conduit_user'));
$dbPass    = getenv('DB_PASS')    ?: ($_ENV['DB_PASS']    ?? ($_SERVER['DB_PASS']    ?? 'secret'));
$dbPort    = getenv('DB_PORT')    ?: ($_ENV['DB_PORT']    ?? ($_SERVER['DB_PORT']    ?? '4000'));

if (empty($dbAdapter)) {
    $dbAdapter = 'mysql';
}

return [
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/db/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'production',
        'production' => [
            'adapter' => $dbAdapter,
            'host'    => $dbHost,
            'name'    => $dbName,
            'user'    => $dbUser,
            'pass'    => $dbPass,
            'port'    => $dbPort,
            'charset' => 'utf8',
            'mysql_attr_init_command' => 'SET NAMES utf8',
            'options' => [
                PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
                PDO::MYSQL_ATTR_SSL_CA => ''
            ]
        ]
    ]
];
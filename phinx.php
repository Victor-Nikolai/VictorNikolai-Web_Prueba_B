<?php

require_once __DIR__ . '/vendor/autoload.php';

// --- CARGA DE CLASE BASE DE MIGRACIONES ---
$baseMigrationPath = __DIR__ . '/database/generator/BaseMigration.php';
if (file_exists($baseMigrationPath)) {
    require_once $baseMigrationPath;
}

// --- RESOLUCIÓN DE VARIABLES DE ENTORNO ---
// Acepta los nombres usados en Render/settings.php (DB_DATABASE, DB_USERNAME, DB_PASSWORD)
// y los nombres antiguos (DB_NAME, DB_USER, DB_PASS).
$env = function (array $names, $default) {
    foreach ($names as $n) {
        $v = getenv($n);
        if ($v !== false && $v !== '') {
            return $v;
        }
    }
    return $default;
};

$dbAdapter = $env(['DB_ADAPTER'], 'mysql');
$dbHost    = $env(['DB_HOST'], '127.0.0.1');
$dbName    = $env(['DB_DATABASE', 'DB_NAME'], 'conduit');
$dbUser    = $env(['DB_USERNAME', 'DB_USER'], 'conduit_user');
$dbPass    = $env(['DB_PASSWORD', 'DB_PASS'], 'secret');
$dbPort    = $env(['DB_PORT'], '4000');

$isProd = getenv('APP_ENV') === 'production' || strpos($dbHost, 'tidbcloud') !== false;
$sslCa  = '/etc/ssl/certs/ca-certificates.crt';

// --- Conexión global de Eloquent (la usa BaseMigration::init) ---
$capsule = new \Illuminate\Database\Capsule\Manager();
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => $dbHost,
    'port'      => $dbPort,
    'database'  => $dbName,
    'username'  => $dbUser,
    'password'  => $dbPass,
    'charset'   => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix'    => '',
    'options'   => $isProd ? [\PDO::MYSQL_ATTR_SSL_CA => $sslCa] : [],
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$production = [
    'adapter' => $dbAdapter,
    'host'    => $dbHost,
    'name'    => $dbName,
    'user'    => $dbUser,
    'pass'    => $dbPass,
    'port'    => $dbPort,
    'charset' => 'utf8',
    'mysql_attr_init_command' => 'SET NAMES utf8',
];
if ($isProd) {
    // Phinx solo lee las claves con prefijo mysql_attr_ en el nivel del entorno
    $production['mysql_attr_ssl_ca'] = $sslCa;
}

return [
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/database/migrations',
        'seeds'      => '%%PHINX_CONFIG_DIR%%/database/seeds',
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment'     => 'production',
        'production'              => $production,
    ],
];
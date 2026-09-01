<?php

// Real credentials live in db-local.php, which is gitignored and never
// committed. These are safe, generic dev-only defaults used only when
// that file doesn't exist (e.g. a fresh clone before first setup).
$local = [
    'dsn' => 'mysql:host=localhost;dbname=pmp_db_1',
    'username' => 'root',
    'password' => '',
];
$localFile = __DIR__ . '/db-local.php';
if (is_file($localFile)) {
    $local = array_merge($local, require $localFile);
}

return [
    'class' => 'yii\db\Connection',
    'dsn' => $local['dsn'],
    'username' => $local['username'],
    'password' => $local['password'],
    'charset' => 'utf8',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];

<?php
// firebase_db.php

declare(strict_types=1);

// 1) Composer autoload (Firebase SDK via Kreait)
require_once __DIR__ . '/../vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\Database;

/**
 * Get a singleton Firebase Realtime Database client.
 *
 * @return Database
 * @throws RuntimeException
 */
function firebase_db(): Database
{
    static $db = null;

    // Return existing instance
    if ($db instanceof Database) {
        return $db;
    }

    // 2) Service account JSON path (override with env if desired)
    $serviceAccount = getenv('FIREBASE_CREDENTIALS')
        ?: __DIR__ . '/dots-56dac-firebase-adminsdk-fbsvc-5b3075a7b1.json';


    // 3) Realtime Database URL
    //    MUST match your Firebase project's database URL.
    $databaseUrl = getenv('FIREBASE_RTDB_URL')
        ?: 'https://dots-56dac-default-rtdb.firebaseio.com';

    // 4) Validate service account file
    if (!is_file($serviceAccount) || !is_readable($serviceAccount)) {
        throw new RuntimeException(
            'Firebase credentials JSON missing or not readable at: ' . $serviceAccount
        );
    }

    // 5) Build factory and create Database instance
    $factory = (new Factory())
        ->withServiceAccount($serviceAccount)
        ->withDatabaseUri($databaseUrl);

    $db = $factory->createDatabase();

    return $db;
}

// ---------------- Example test code (optional) ----------------
// Uncomment this block to quickly test the connection.
// Put this file in browser or run via CLI: php firebase_db.php
/*
try {
    $db = firebase_db();

    // Write test data
    $db->getReference('test_connection')->set([
        'status' => 'ok',
        'time'   => date('Y-m-d H:i:s'),
    ]);

    // Read it back
    $snapshot = $db->getReference('test_connection')->getValue();

    echo '<pre>';
    print_r($snapshot);
    echo '</pre>';
} catch (Throwable $e) {
    echo 'Firebase error: ' . $e->getMessage();
}
*/

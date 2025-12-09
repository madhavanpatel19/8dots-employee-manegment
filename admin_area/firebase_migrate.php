<?php
// Migrate all MySQL tables to Firebase Realtime Database.
// Run via CLI: php firebase_migrate.php
// Or via browser (restricted output).

require __DIR__ . '/connection.php';       // provides $con (MySQL)
require __DIR__ . '/includes/firebase.php'; // provides firebase_db()

header('Content-Type: text/plain');

if (!$con) {
    http_response_code(500);
    exit("MySQL connection missing.\n");
}

try {
    $db = firebase_db();
} catch (Throwable $e) {
    http_response_code(500);
    exit("Firebase init failed: " . $e->getMessage() . "\n");
}

/**
 * Choose a key for a row so Firebase data is stable and overwrite-safe.
 */
function choose_key(array $row): ?string
{
    foreach ($row as $k => $v) {
        if ($k === 'id' || substr($k, -3) === '_id') {
            return (string)$v;
        }
    }
    return null;
}

$tables = [];
$res = mysqli_query($con, 'SHOW TABLES');
if (!$res) {
    http_response_code(500);
    exit("Failed to list tables: " . mysqli_error($con) . "\n");
}
while ($row = mysqli_fetch_array($res)) {
    $tables[] = $row[0];
}

echo "Migrating tables to Firebase Realtime DB...\n";
$root = $db->getReference('tables');

foreach ($tables as $table) {
    $result = mysqli_query($con, "SELECT * FROM `{$table}`");
    if (!$result) {
        echo "Skipping {$table}: " . mysqli_error($con) . "\n";
        continue;
    }

    $payload = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $key = choose_key($row);
        if ($key === null) {
            $payload[] = $row; // fallback to numeric list if no obvious key
        } else {
            $payload[$key] = $row;
        }
    }

    $root->getChild($table)->set($payload);
    echo " - {$table}: " . count($payload) . " rows migrated\n";
}

echo "Done.\n";

<?php

require_once __DIR__ . '/firebase.php';

/**
 * Write a single row to Firebase under tables/{table}/{key}.
 */
function firebase_sync_row(\Kreait\Firebase\Database $db, string $table, string $key, array $row): void
{
    $db->getReference("tables/{$table}/{$key}")->set($row);
}

/**
 * Remove a single row from Firebase tables/{table}/{key}.
 */
function firebase_delete_row(\Kreait\Firebase\Database $db, string $table, string $key): void
{
    $db->getReference("tables/{$table}/{$key}")->remove();
}

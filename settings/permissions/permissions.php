<?php
// permissions.php - Professional Permission Management System

require_once __DIR__ . '/../../admin_area/connection.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
|--------------------------------------------------------------------------
| Get All Available System Permissions
|--------------------------------------------------------------------------
*/
if (!function_exists('getAllPermissions')) {
    function getAllPermissions() {
        return [
            'employee_insert',
            'employee_update',
            'employee_delete',
            'employee_view',
            'employee_no_view',

            'attendance_insert',
            'attendance_update',
            'attendance_delete',
            'attendance_view',
            'attendance_no_view',
            'attendance_edit',

            'salary_insert',
            'salary_update',
            'salary_delete',
            'salary_view',
            'salary_no_view',

            'user_insert',
            'user_update',
            'user_view',
            'worksheet_view',
            'leave_view',
        ];
    }
}

/*
|--------------------------------------------------------------------------
| Get permissions that actually control admin area access (used in pages)
| Use this in Insert/Edit User forms so only "used" permissions are shown.
|--------------------------------------------------------------------------
*/
if (!function_exists('getUsedAdminPermissions')) {
    function getUsedAdminPermissions() {
        return [
            'employee_insert',
            'employee_update',
            'employee_delete',
            'employee_view',

            'attendance_view',
            'attendance_edit',

            'salary_view',

            'user_insert',
            'user_update',
            'user_view',
            'worksheet_view',
            'leave_view',
        ];
    /*
    |--------------------------------------------------------------------------
    | Check If User Has Attendance Edit Permission
    |--------------------------------------------------------------------------
    */
    if (!function_exists('userCanEditAttendance')) {
        function userCanEditAttendance($userId) {
            return userHasPermission($userId, 'attendance_edit');
        }
    }
    }
}

/*
|--------------------------------------------------------------------------
| Get human-readable label for a permission key
|--------------------------------------------------------------------------
*/
if (!function_exists('getPermissionLabel')) {
    function getPermissionLabel($permissionKey) {
        return ucwords(str_replace('_', ' ', $permissionKey));
    }
}

/*
|--------------------------------------------------------------------------
| Get User Role ID
|--------------------------------------------------------------------------
*/
function getUserRole($userId) {
    global $con;

    $roleId = null;
    $stmt = $con->prepare("SELECT role_id FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($roleId);
    $stmt->fetch();
    $stmt->close();

    return $roleId ?? null;
}

/*
|--------------------------------------------------------------------------
| Get Role Permissions (Cached in Session)
|--------------------------------------------------------------------------
*/
function getRolePermissions($roleId) {
    global $con;

    if (!$roleId) return [];

    // Cache permissions in session to reduce DB load
    if (isset($_SESSION['role_permissions'][$roleId])) {
        return $_SESSION['role_permissions'][$roleId];
    }

    $stmt = $con->prepare("
        SELECT p.name 
        FROM permissions p 
        JOIN role_permissions rp ON p.id = rp.permission_id 
        WHERE rp.role_id = ?
    ");
    $stmt->bind_param("i", $roleId);
    $stmt->execute();
    $result = $stmt->get_result();

    $permissions = [];
    while ($row = $result->fetch_assoc()) {
        $permissions[] = $row['name'];
    }

    $stmt->close();

    // Store in session cache
    $_SESSION['role_permissions'][$roleId] = $permissions;

    return $permissions;
}

/*
|--------------------------------------------------------------------------
| Check If User Has Permission
|--------------------------------------------------------------------------
*/
function userHasPermission($userId, $permission) {

    if (!$userId) return false;

    $roleId = getUserRole($userId);
    $permissions = getRolePermissions($roleId);

    // If user is admin, allow attendance_edit, but only attendance_view if not editing
    if (isAdmin($userId)) {
        if ($permission === 'attendance_edit') {
            return true;
        }
        if ($permission === 'attendance_view') {
            // Only allow view if not editing
            return !isset($_GET['edit']) || !$_GET['edit'];
        }
    }
    return in_array($permission, $permissions);
}

/*
|--------------------------------------------------------------------------
| Check If User Is Admin
|--------------------------------------------------------------------------
*/
function isAdmin($userId) {
    global $con;

    if (!$userId) return false;

    $stmt = $con->prepare("
        SELECT r.name 
        FROM roles r
        JOIN users u ON r.id = u.role_id
        WHERE u.id = ?
    ");

    $roleName = null;
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($roleName);
    $stmt->fetch();
    $stmt->close();

    return strtolower((string)$roleName) === 'admin';
}

/*
|--------------------------------------------------------------------------
| Protect Page By Permission
|--------------------------------------------------------------------------
*/
function requirePermission($permission) {

    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    $userId = $_SESSION['user_id'];

    if (!userHasPermission($userId, $permission)) {
        die("Access Denied. You do not have permission.");
    }
}

<?php
/**
 * Admin permission helper for admin_area.
 * - Super admin (is_super_admin=1): full access to all pages.
 * - Other admins: only pages allowed by their "permissions" column (comma-separated).
 * Requires: $con and $_SESSION['admin_email'] (include db.php and ensure admin is logged in first).
 */

if (!isset($con) || !isset($_SESSION['admin_email'])) {
    return;
}

// Ensure admins table has permission columns (one-time migration)
$check = @mysqli_query($con, "SHOW COLUMNS FROM admins LIKE 'is_super_admin'");
if (!$check || mysqli_num_rows($check) === 0) {
    @mysqli_query($con, "ALTER TABLE admins ADD COLUMN is_super_admin TINYINT(1) NOT NULL DEFAULT 0");
    @mysqli_query($con, "ALTER TABLE admins ADD COLUMN permissions TEXT NULL");
    @mysqli_query($con, "UPDATE admins SET is_super_admin = 1 WHERE admin_id = 1 LIMIT 1");
}
$check_perm = @mysqli_query($con, "SHOW COLUMNS FROM admins LIKE 'permissions'");
if (!$check_perm || mysqli_num_rows($check_perm) === 0) {
    @mysqli_query($con, "ALTER TABLE admins ADD COLUMN permissions TEXT NULL");
}

// Load current admin permission state (cached in session for this request)
if (!isset($_SESSION['_admin_super']) || !isset($_SESSION['_admin_perms'])) {
    $email = mysqli_real_escape_string($con, $_SESSION['admin_email']);
    $r = mysqli_query($con, "SELECT is_super_admin, permissions FROM admins WHERE admin_email='$email' LIMIT 1");
    if ($r && $row = mysqli_fetch_assoc($r)) {
        $_SESSION['_admin_super'] = !empty($row['is_super_admin']);
        $p = isset($row['permissions']) ? trim($row['permissions']) : '';
        $_SESSION['_admin_perms'] = $p === '' ? [] : array_map('trim', explode(',', $p));
    } else {
        $_SESSION['_admin_super'] = false;
        $_SESSION['_admin_perms'] = [];
    }
}

function isSuperAdmin() {
    return !empty($_SESSION['_admin_super']);
}

function getCurrentAdminPermissions() {
    return isset($_SESSION['_admin_perms']) ? (array)$_SESSION['_admin_perms'] : [];
}

// Map new permission names to legacy names (for backward compatibility)
function _adminPermissionAliases($permission) {
    $map = [
        'employee_insert' => ['add_employee'],
        'employee_update' => ['edit_employee'],
        'employee_delete' => ['delete_employee'],
        'employee_view'   => ['show_employee'],
        'attendance_view' => ['show_attendance'],
        'salary_view'     => ['show_salary'],
        'user_insert'     => ['add_permission'],
        'user_update'     => ['edit_user'],
        'user_view'       => ['show_user'],
        'leave_view'      => ['show_leave'],
    ];
    $perms = [$permission];
    if (isset($map[$permission])) {
        $perms = array_merge($perms, $map[$permission]);
    }
    return $perms;
}

function canAdminAccess($permission) {
    if (isSuperAdmin()) {
        return true;
    }
    $allowed = getCurrentAdminPermissions();
    foreach (_adminPermissionAliases($permission) as $p) {
        if (in_array($p, $allowed, true)) {
            return true;
        }
    }
    return false;
}

function requireAdminPermission($permission, $attendanceDate = null) {
    // Special case: allow editing today's attendance for all admins
    if ($permission === 'attendance_edit' && $attendanceDate !== null) {
        $today = date('Y-m-d');
        if ($attendanceDate === $today) {
            return;
        }
    }
    if (canAdminAccess($permission)) {
        return;
    }
    $redirect = 'index.php?dashboard&access_denied=1';
    if (headers_sent()) {
        echo "<script>window.location.href='" . htmlspecialchars($redirect) . "';</script>";
        exit;
    }
    header('Location: ' . $redirect);
    exit;
}

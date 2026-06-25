<?php
// permissions.php - Permission Definition and Logic Bridge

if (!isset($con)) { include(__DIR__ . '/../../includes/db.php'); }

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('getAllPermissions')) {
    function getAllPermissions() {
        return [
            'employee_insert',
            'employee_update',
            'employee_delete',
            'employee_view',
            'attendance_insert',
            'attendance_update',
            'attendance_delete',
            'attendance_view',
            'attendance_edit',
            'salary_insert',
            'salary_update',
            'salary_delete',
            'salary_view',
            'user_insert',
            'user_update',
            'user_view',
            'worksheet_view',
            'leave_view',
            'announcement_view'
        ];
    }
}

/**
 * Get permissions used in admin forms (checkboxes)
 */
if (!function_exists('getUsedAdminPermissions')) {
    function getUsedAdminPermissions() {
        return [
            'employee_insert', 'employee_update', 'employee_delete', 'employee_view',
            'attendance_view', 'attendance_edit',
            'salary_view',
            'user_insert', 'user_update', 'user_view',
            'worksheet_view', 'leave_view', 'announcement_view'
        ];
    }
}

/**
 * Human-readable labels for permission keys
 */
if (!function_exists('getPermissionLabel')) {
    function getPermissionLabel($permissionKey) {
        return ucwords(str_replace(['_', 'table'], [' ', ''], $permissionKey));
    }
}

/**
 * Check if current logged-in user (admin) has a permission.
 * Bridges to admin_permissions.php logic or falls back to direct DB check.
 */
if (!function_exists('userHasPermission')) {
    function userHasPermission($userId, $permission) {
        // If admin_permissions helper is loaded, prioritize its logic
        if (function_exists('canAdminAccess')) {
            return canAdminAccess($permission);
        }
        
        // Fallback: Check admins table directly using session
        global $con;
        if (isset($_SESSION['admin_email'])) {
            $email = mysqli_real_escape_string($con, $_SESSION['admin_email']);
            $res = mysqli_query($con, "SELECT is_super_admin, permissions FROM admins WHERE admin_email='$email' LIMIT 1");
            if ($res && $row = mysqli_fetch_assoc($res)) {
                if (!empty($row['is_super_admin'])) return true;
                $perms = isset($row['permissions']) ? trim($row['permissions']) : '';
                $perms_arr = $perms === '' ? [] : array_map('trim', explode(',', $perms));
                return in_array($permission, $perms_arr, true);
            }
        }
        return false;
    }
}

/**
 * Check if user is admin
 */
if (!function_exists('isAdmin')) {
    function isAdmin($userId) {
        if (isset($_SESSION['admin_email'])) return true;
        
        global $con;
        $id_esc = mysqli_real_escape_string($con, $userId);
        $res = mysqli_query($con, "SELECT admin_id FROM admins WHERE admin_id='$id_esc' LIMIT 1");
        return ($res && mysqli_num_rows($res) > 0);
    }
}

/**
 * Required permission check for pages (internal bridge)
 */
if (!function_exists('requirePermission')) {
    function requirePermission($permission) {
        // Bridge to requireAdminPermission if it exists
        if (function_exists('requireAdminPermission')) {
            requireAdminPermission($permission);
            return;
        }

        if (!userHasPermission(null, $permission)) {
            $redirect = "index.php?dashboard&access_denied=1";
            if (!headers_sent()) {
                header("Location: $redirect");
            } else {
                echo "<script>window.location.href='$redirect';</script>";
            }
            exit();
        }
    }
}

/**
 * Attendance edit helper
 */
if (!function_exists('userCanEditAttendance')) {
    function userCanEditAttendance($userId) {
        return userHasPermission($userId, 'attendance_edit');
    }
}

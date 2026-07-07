<?php
if (!isset($_SESSION['admin_email'])) {
    echo "<script>window.open('../../pages/auth/login.php','_self')</script>";
    exit;
}
?>

<style>
    .btn-icon-premium {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #fff;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        width: 38px;
        height: 38px;
        transition: 0.3s;
        cursor: pointer;
        color: #64748b;
        text-decoration: none !important;
    }

    .btn-icon-premium:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    /* Edit (Blue) */
    .btn-icon-edit {
        color: #0ea5e9 !important;
        background: #f0f9ff !important;
        border-color: #bae6fd !important;
    }

    .btn-icon-edit:hover {
        background: #e0f2fe !important;
        border-color: #7dd3fc !important;
        color: #0284c7 !important;
    }

    /* Delete (Red) */
    .btn-icon-delete {
        color: #ef4444 !important;
        background: #fef2f2 !important;
        border-color: #fecaca !important;
    }

    .btn-icon-delete:hover {
        background: #fee2e2 !important;
        border-color: #fca5a5 !important;
        color: #dc2626 !important;
    }
</style>

<div class="page-wrapper premium-ui-enabled">
    <div class="page-header-premium">
        <h1></h1>
        <?php if (canAdminAccess('user_insert')): ?>
        <a href="index.php?insert_user" class="btn-premium-add">
            <i class="fa fa-user-plus"></i> Add new User
        </a>
        <?php endif; ?>
    </div>

    <div class="premium-card">
        <div class="card-hdr">
            <i class="fa fa-list"></i>
            <h3>All Administrative Users</h3>
        </div>
        <div style="overflow-x: auto;">
            <table class="table-premium">
                <thead>
                    <tr>
                        <th>User Identity</th>
                        <th style="text-align: center;">Email Contact</th>
                        <th style="text-align: center;">Country</th>
                        <th style="text-align: center;">Job Title</th>
                        <th style="text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $get_admin = "select * from admins";
                    $run_admin = mysqli_query($con, $get_admin);
                    while ($row_admin = mysqli_fetch_array($run_admin)) {
                        $admin_id = $row_admin['admin_id'];
                        $admin_name = $row_admin['admin_name'];
                        $admin_email = $row_admin['admin_email'];
                        $admin_image = $row_admin['admin_image'];
                        $admin_country = $row_admin['admin_country'];
                        $admin_job = $row_admin['admin_job'];
                    ?>
                        <tr>
                            <td style="text-align: center;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <img src="admin_images/<?php echo !empty($admin_image) ? $admin_image : 'default.png'; ?>" style="width: 48px; height: 48px; border-radius: 50%; object-fit: cover; border: 2px solid #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                                    <span style="font-weight: 700; color: var(--p-text); font-size: 15px;"><?php echo htmlspecialchars($admin_name); ?></span>
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <div style="color: var(--p-secondary); font-size: 13px;">
                                    <i class="fa fa-envelope-o" style="margin-right: 5px;"></i> <?php echo htmlspecialchars($admin_email); ?>
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <span style="font-weight: 600; color: #475569;"><i class="fa fa-globe" style="margin-right: 5px; color: #94a3b8;"></i> <?php echo htmlspecialchars($admin_country); ?></span>
                            </td>
                            <td style="text-align: center;">
                                <span class="p-badge p-badge-primary"><?php echo htmlspecialchars($admin_job); ?></span>
                            </td>
                            <td style="text-align: center;">
                                <div style="display: flex; justify-content: center; gap: 8px;">
                                    <?php if (canAdminAccess('user_update')): ?>
                                    <a href="index.php?edit_user=<?php echo $admin_id; ?>" class="btn-icon-premium btn-icon-edit" title="Edit User">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                    <?php endif; ?>
                                    <?php if (canAdminAccess('user_delete')): ?>
                                    <a href="index.php?user_delete=<?php echo $admin_id; ?>" class="btn-icon-premium btn-icon-delete" title="Delete User">
                                        <i class="fa fa-trash-o"></i>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php }
                    if (mysqli_num_rows($run_admin) == 0) {
                        echo "<tr>
                                <td colspan='5' style='padding: 0; border-bottom: none;'>
                                    <div style='display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 60px 20px; width: 100%;'>
                                        <div style='width: 64px; height: 64px; background: #f8fafc; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 16px;'>
                                            <i class='fa fa-folder-open-o' style='font-size: 28px; color: #cbd5e1;'></i>
                                        </div>
                                        <div style='font-size: 15px; font-weight: 700; color: #64748b; margin-bottom: 4px;'>No Users Found</div>
                                        <div style='font-size: 13px; color: #94a3b8;'>There are no users to display at this time.</div>
                                    </div>
                                </td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
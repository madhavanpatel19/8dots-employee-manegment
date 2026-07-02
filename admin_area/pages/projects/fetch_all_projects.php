<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($con)) {
    include(__DIR__ . '/../../includes/db.php');
}

// Always read admin status fresh from DB (bypass session cache)
$current_admin_id_proj = 0;
$is_super_admin_proj = false;
if (isset($_SESSION['admin_email'])) {
    $email_esc = mysqli_real_escape_string($con, $_SESSION['admin_email']);
    $r = mysqli_query($con, "SELECT admin_id, is_super_admin FROM admins WHERE admin_email='$email_esc' LIMIT 1");
    if ($r && $row_a = mysqli_fetch_assoc($r)) {
        $current_admin_id_proj = (int)$row_a['admin_id'];
        $is_super_admin_proj   = !empty($row_a['is_super_admin']);
    }
}

// If NOT super admin, restrict to projects assigned to this admin
$admin_project_filter = '';
if (!$is_super_admin_proj && $current_admin_id_proj > 0) {
    $admin_project_filter = " AND (FIND_IN_SET('$current_admin_id_proj', REPLACE(cp.assigned_admins, ' ', '')) > 0) ";
}

$status_filter = isset($_GET['status']) ? mysqli_real_escape_string($con, $_GET['status']) : '';
$source_filter = isset($_GET['source']) ? mysqli_real_escape_string($con, $_GET['source']) : '';

$where_clause = " WHERE 1=1 $admin_project_filter ";
if ($status_filter !== "") {
    $where_clause .= " AND cp.status='$status_filter' ";
}
if ($source_filter !== "") {
    $where_clause .= " AND cp.source LIKE '%$source_filter%' ";
}

$page = isset($_GET['page']) && intval($_GET['page']) > 0 ? intval($_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$get_projects = "SELECT cp.*, c.name as client_name, c.image FROM client_projects cp JOIN clients c ON cp.client_id = c.id $where_clause ORDER BY cp.id DESC LIMIT $offset, $limit";
$run_projects = mysqli_query($con, $get_projects);


if (!$run_projects) {
    die('<div class="alert alert-danger" style="margin: 20px; border-radius: 12px; border: none; background: #fee2e2; color: #991b1b; font-weight: 600;">
            <i class="fa fa-exclamation-triangle"></i> Database Error: ' . mysqli_error($con) . '
        </div>');
}

if (mysqli_num_rows($run_projects) > 0) {
    while ($p = mysqli_fetch_assoc($run_projects)) {
        $project_id = $p['id'];
        $project_date = !empty($p['project_date']) ? date('M d, Y', strtotime($p['project_date'])) : 'NA';
        $source = isset($p['source']) ? $p['source'] : '';
        //employee names
        $empIds = explode(",", $p['assigned_employees']);
        $budget = floatval($p['budget']);
        $currency = !empty($p['currency']) ? $p['currency'] : 'INR';
        $symbols = ['INR' => '₹', 'USD' => '$', 'EUR' => '€', 'GBP' => '£', 'AED' => 'د.إ'];
        $sym = isset($symbols[$currency]) ? $symbols[$currency] : '₹';
?>
        <tr style="transition: 0.3s;">
            <td style="text-align: center;">
                <span class="id-badge-premium">#<?php echo str_pad($project_id, 3, '0', STR_PAD_LEFT); ?></span>
            </td>
            <td>
                <div style="display:flex; align-items:center; gap:12px;">
                    <?php if (!empty($p['project_image']) && file_exists('../../uploads/project_images/' . $p['project_image'])) { ?>
                        <img src="uploads/project_images/<?php echo htmlspecialchars($p['project_image']); ?>"
                            style="width:40px;height:40px;border-radius:50%;object-fit:cover;border:1px solid #e2e8f0;">
                    <?php } else if (!empty($p['image']) && file_exists('../../uploads/client_images/' . $p['image'])) { ?>
                        <img src="uploads/client_images/<?php echo htmlspecialchars($p['image']); ?>"
                            style="width:40px;height:40px;border-radius:50%;object-fit:cover;border:1px solid #e2e8f0;">
                    <?php } else { ?>
                        <div style="width:40px;height:40px;border-radius:50%;background:#e2e8f0;display:flex;align-items:center;justify-content:center;font-weight:600;color:#64748b;">
                            <?php echo strtoupper(substr($p['project_name'], 0, 1)); ?>
                        </div>
                    <?php } ?>

                    <!-- Project Details -->
                    <div>
                        <div style="font-weight:800;color:#0f172a;font-size:15px;letter-spacing:-0.3px;">
                            <?php echo htmlspecialchars($p['project_name']); ?>
                        </div>
                        <div style="font-weight:600;color:#64748b;font-size:12px;margin-top:3px;">
                            <i class="fa fa-user"></i>
                            <?php echo htmlspecialchars($p['client_name']); ?>
                        </div>
                    </div>
                </div>
            </td>
            <td>
                <div class="employee-wrap">
                    <?php
                    $limit = 3;

                    $validEmpIds = array_filter($empIds, function ($id) {
                        return !empty(trim($id));
                    });

                    echo '<div class="employee-group">';

                    $i = 0;
                    foreach ($validEmpIds as $empId) {
                        $query = mysqli_query($con, "SELECT employee_image,name FROM emp_list WHERE id='" . intval($empId) . "'");
                        if ($query) {
                            $emp = mysqli_fetch_assoc($query);
                            if ($emp) {
                                $isHidden = $i >= $limit ? 'display: none;' : '';
                                $hiddenClass = $i >= $limit ? 'hidden-employee' : '';

                                if (!empty($emp['employee_image'])) {
                                    echo '<img src="uploads/' . htmlspecialchars($emp['employee_image']) . '" title="' . htmlspecialchars($emp['name'] ?? '') . '" style="' . $isHidden . '" class="' . $hiddenClass . '">';
                                } else {
                                    $initial = strtoupper(substr($emp['name'] ?? 'U', 0, 1));
                                    echo '<div class="emp-initial ' . $hiddenClass . '" title="' . htmlspecialchars($emp['name'] ?? '') . '" style="' . $isHidden . '">' . $initial . '</div>';
                                }
                                $i++;
                            }
                        }
                    }

                    if ($i > $limit) {
                        echo '<span class="more" onclick="this.parentElement.querySelectorAll(\'.hidden-employee\').forEach(el => el.style.display = \'flex\'); this.style.display = \'none\';" title="Show all">+' . ($i - $limit) . '</span>';
                    }

                    echo '</div>';
                    ?>
                </div>
            </td>
            <td>
                <span style="font-size: 12px; color: #475569; background: #f1f5f9; padding: 4px 10px; border-radius: 6px; width: 90px; display: inline-block; white-space: normal; word-wrap: break-word;"><?php echo htmlspecialchars($source ?: '-'); ?></span>
            </td>
            <td style="color: #64748b; font-size: 13px; font-weight: 700;">
                <i class="fa fa-calendar-o" style="margin-right: 5px;"></i> <?php echo $project_date; ?>
            </td>
            <td style="text-align: center;">
                <span class="budget-badge-trigger" onclick="openBudgetModal(<?php echo $project_id; ?>, '<?php echo addslashes($p['project_name']); ?>', <?php echo $budget; ?>, '<?php echo $currency; ?>')"
                    style="font-weight: 900; color: #16a34a; font-size: 13px; letter-spacing: -0.2px; cursor: pointer; background: #f0fdf4; padding: 7px 14px; border-radius: 12px; border: 1px solid #dcfce7; display: inline-flex; align-items: center; justify-content: center; min-width: 115px; transition: 0.2s; box-shadow: 0 2px 4px rgba(22, 163, 74, 0.05);">
                    <span style="opacity: 0.6; margin-right: 4px;"><?php echo $sym; ?></span> <?php echo number_format($budget, 0); ?>
                </span>
            </td>
            <td style="text-align: center;">
                <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                    <button class="btn-icon-premium" onclick="viewDocs(<?php echo $project_id; ?>, 'documents')"
                        style="width: 38px; height: 38px; background: #FFEAEB;" title="Artifact Repository">
                        <i class="fa fa-folder-open" style="color: #DF2127; font-size: 13px;"></i>
                    </button>
                    <button class="btn-icon-premium" onclick="viewDocs(<?php echo $project_id; ?>, 'links')"
                        style="width: 38px; height: 38px; background: #FFEAEB;" title="Link Hub">
                        <i class="fa fa-link" style="color: #DF2127; font-size: 13px;"></i>
                    </button>
                </div>
            </td>
            <td style="text-align: center;">
                <?php
                $status = !empty($p['status']) ? $p['status'] : 'Active';
                $color_map = ['Active' => '#16a34a', 'Pending' => '#ca8a04', 'Completed' => '#2563eb'];
                $bg_map = ['Active' => '#f0fdf4', 'Pending' => '#fefce8', 'Completed' => '#eff6ff'];
                $border_map = ['Active' => '#dcfce7', 'Pending' => '#fef9c3', 'Completed' => '#dbeafe'];
                $current_color = isset($color_map[$status]) ? $color_map[$status] : '#475569';
                $current_bg = isset($bg_map[$status]) ? $bg_map[$status] : '#f8fafc';
                $current_border = isset($border_map[$status]) ? $border_map[$status] : '#e2e8f0';
                ?>
                <select class="project-status-select" data-project-id="<?php echo $project_id; ?>"
                    style="appearance: none; -webkit-appearance: none; background: <?php echo $current_bg; ?> url('data:image/svg+xml;charset=UTF-8,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2212%22 height=%2212%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22<?php echo urlencode($current_color); ?>%22 stroke-width=%223%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22%3E%3Cpolyline points=%226 9 12 15 18 9%22%3E%3C/polyline%3E%3C/svg%3E') no-repeat right 12px center; color: <?php echo $current_color; ?>; border: 1px solid <?php echo $current_border; ?>; font-size: 10px; font-weight: 900; text-transform: uppercase; padding: 7px 32px 7px 15px; border-radius: 20px; letter-spacing: 0.8px; cursor: pointer; outline: none; transition: all 0.3s ease; width: auto; min-width: 125px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                    <option value="Active" <?php if ($status == 'Active') echo 'selected'; ?>>Active</option>
                    <option value="Pending" <?php if ($status == 'Pending') echo 'selected'; ?>>Pending</option>
                    <option value="Completed" <?php if ($status == 'Completed') echo 'selected'; ?>>Completed</option>
                </select>
            </td>
            <td style="text-align: center;">
                <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                    <button class="btn-icon-premium" onclick="window.location.href='index.php?team_todo&project_id=<?php echo $project_id; ?>'"
                        style="width: 32px; height: 32px; font-size: 12px; background: #fffbeb; border-color: #fef3c7;" title="Team To-Do">
                        <i class="fa fa-list-alt" style="color: #f59e0b;"></i>
                    </button>
                    <button class="btn-icon-premium btn-toggle-history" style="width: 32px; height: 32px; font-size: 12px; background: #f5f3ff; border-color: #ede9fe;" title="View History">
                        <i class="fa fa-history history-toggle-icon" style="color: #7c3aed;"></i>
                    </button>
                    <button class="btn-icon-premium" onclick="window.location.href='index.php?edit_project=<?php echo $project_id; ?>'"
                        style="width: 32px; height: 32px; font-size: 12px; background: #f0f9ff; border-color: #e0f2fe;" title="Edit Project">
                        <i class="fa fa-pencil" style="color: #0284c7;"></i>
                    </button>
                    <button class="btn-icon-premium" onclick="deleteProject(<?php echo $project_id; ?>, '<?php echo addslashes($p['project_name']); ?>')"
                        style="width: 32px; height: 32px; font-size: 12px; background: #fef2f2; border-color: #fee2e2;" title="Delete Project">
                        <i class="fa fa-trash-o" style="color: #ef4444;"></i>
                    </button>
                </div>
            </td>
        </tr>
        <tr class="project-detail-row" style="display: none; background: #fff;">
            <td colspan="7" style="padding: 0; border: none;">
                <div style="padding: 35px 50px; border-top: 1px solid #f1f5f9; background: #fcfdfe;">
                    <div class="row">
                        <div class="col-md-7">
                            <div class="timeline-container-premium" style="background: transparent; border: none; padding: 0; margin-bottom: 0;">
                                <div class="timeline-header-premium" style="margin-bottom: 25px; display: flex; align-items: center; gap: 10px; font-size: 11px; font-weight: 900; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px;">
                                    <i class="fa fa-history" style="color: #6366f1; font-size: 14px;"></i>
                                    <span>Project Activity Timeline</span>
                                </div>
                                <div class="timeline-visual-wrapper" style="max-height: 250px; overflow-y: auto; overflow-x: hidden; padding-right: 15px; scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent;">
                                    <div class="timeline-vertical-line" style="left: 4px;"></div>
                                    <div class="remarks-history-premium" style="position: relative; padding-left: 0;">
                                        <?php
                                        $get_remarks = "SELECT * FROM client_project_remarks WHERE project_id = $project_id ORDER BY created_at DESC";
                                        $run_remarks = mysqli_query($con, $get_remarks);
                                        if (mysqli_num_rows($run_remarks) > 0) {
                                            while ($r = mysqli_fetch_assoc($run_remarks)) {
                                        ?>
                                                <div class="timeline-remark-item" style="margin-bottom: 25px; position: relative; padding-left: 32px; width: 100%;">
                                                    <div class="timeline-dot" style="left: 0;"></div>
                                                    <div class="remark-content-box" style="padding-left: 20px;">
                                                        <div class="remark-time-premium" style="margin-bottom: 8px;">
                                                            <i class="fa fa-clock-o"></i> <?php echo date('d M Y • h:i A', strtotime($r['created_at'])); ?>
                                                        </div>
                                                        <div class="remark-text-premium"><?php echo nl2br(htmlspecialchars($r['remark'])); ?></div>
                                                    </div>
                                                </div>
                                        <?php
                                            }
                                        } else {
                                            echo '<div class="no-remarks-placeholder" style="padding: 40px 0; text-align: center; color: #94a3b8;">
                                                    <i class="fa fa-commenting-o" style="font-size: 32px; opacity: 0.4; margin-bottom: 10px; display: block;"></i>
                                                    <p style="font-size: 13px; font-weight: 700;">No activity recorded yet.</p>
                                                  </div>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="remark-action-premium glass-card-premium" style="padding: 30px; border-radius: 24px; box-shadow: 0 10px 30px -10px rgba(0,0,0,0.08);">
                                <h4 style="font-size: 11px; font-weight: 950; color: #64748b; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                                    <div style="width: 8px; height: 8px; background: #6366f1; border-radius: 50%;"></div>
                                    Post Progress Update
                                </h4>
                                <div class="action-input-wrapper" style="flex-direction: column; gap: 20px;">
                                    <textarea class="remark-textarea p-input-premium" style="width: 100%; height: 120px; resize: none; font-size: 14px;" placeholder="What milestone was achieved today?"></textarea>
                                    <button type="button" class="add-remark-btn-premium add-remark-btn" data-project-id="<?php echo $project_id; ?>" style="width: 100%; height: 50px; font-size: 14px; background: #0f172a; color: #fff; border: none; border-radius: 14px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.3s; font-weight: 700; gap: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                                        <i class="fa fa-send"></i> Post Update
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
<?php
    }
}
?>
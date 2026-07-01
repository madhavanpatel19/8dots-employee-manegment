<?php
if (!isset($con)) {
    include(__DIR__ . '/../../includes/db.php');
}

// Only allow access if logged in as employee
if (!isset($_SESSION['emp_id']) || !isset($_SESSION['emp_name'])) {
    header('Location: ../../pages/auth/login.php');
    exit();
}

$emp_id = $_SESSION['emp_id'];

$filter_date = isset($_GET['date']) ? mysqli_real_escape_string($con, $_GET['date']) : date('Y-m-d');

// Fetch todos assigned to this employee
// Pending tasks (status=0) show up every day
// Completed tasks (status=1) only show up on the selected filter_date (based on due_date or created_at)
$query = "SELECT t.*, p.project_name, c.name as client_name 
          FROM project_team_todos t 
          LEFT JOIN client_projects p ON t.project_id = p.id 
          LEFT JOIN clients c ON p.client_id = c.id
          WHERE t.emp_id = $emp_id 
          AND (t.status = 0 OR (t.status = 1 AND DATE(COALESCE(t.due_date, t.created_at)) = '$filter_date'))
          ORDER BY t.status ASC, t.due_date ASC, t.id DESC";
$result = mysqli_query($con, $query);

?>

<div class="premium-ui-enabled">
    <div class="row">
        <div class="page-header-premium" style="display: flex; justify-content: space-between; align-items: center; padding: 20px 25px; margin-bottom: 0px;">
            <h1></h1>
            <div class="header-actions-premium" style="display: flex; gap: 16px; align-items: center;">
                <div style="position: relative; display: flex; align-items: center; gap: 8px;">
                    <label style="font-size: 13px; font-weight: 700; color: #64748b; margin: 0;">Completed On:</label>
                    <input type="date" value="<?php echo htmlspecialchars($filter_date); ?>" onchange="window.location.href='index.php?todo&date=' + this.value" style="padding: 10px 15px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 13px; color: #334155; font-weight: 600; outline: none; transition: 0.3s; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="premium-card" style="border: none; border-radius: 20px; overflow: hidden; box-shadow: 0 4px 25px -5px rgba(0,0,0,0.08); background: #fff;">
                <div class="card-hdr" style="background: var(--p-bg-header); color: #fff; padding: 18px 25px; display: flex; align-items: center; gap: 12px; border: none;">
                    <i class="fa fa-list-ol" style="font-size: 16px; color: #fff;"></i>
                    <h3 style="margin: 0; font-size: 14px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #fff;">Assigned Tasks</h3>
                </div>
                <div style="overflow-x: auto;">
                    <table class="table-premium" style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #fcfdfe; border-bottom: 1.5px solid #f1f5f9;">
                                <th style="width: 60px; text-align: center; padding: 18px 15px; color: #64748b; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">ID</th>
                                <th style="padding: 18px 15px; color: #64748b; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">Task Details</th>
                                <th style="padding: 18px 15px; color: #64748b; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">Project</th>
                                <th style="padding: 18px 15px; color: #64748b; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">Due Date</th>
                                <th style="padding: 18px 15px; color: #64748b; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">Priority</th>
                                <th style="text-align: center; padding: 18px 15px; color: #64748b; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result && mysqli_num_rows($result) > 0) : ?>
                                <?php while ($row = mysqli_fetch_assoc($result)) :
                                    // Status
                                    $is_completed = intval($row['status']) === 1;
                                    $status_label = $is_completed ? 'Completed' : 'Pending';
                                    $status_badge = $is_completed ? 'background: #ecfdf5; color: #059669;' : 'background: #fff7ed; color: #ea580c;';

                                    // Priority badge
                                    $priority = strtolower($row['priority'] ?? 'medium');
                                    $priority_badge = 'background: #f1f5f9; color: #64748b;';
                                    if ($priority == 'high') $priority_badge = 'background: #fef2f2; color: #dc2626;';
                                    elseif ($priority == 'medium') $priority_badge = 'background: #eff6ff; color: #2563eb;';
                                    elseif ($priority == 'low') $priority_badge = 'background: #ecfdf5; color: #059669;';

                                    $proj_name = !empty($row['project_name']) ? htmlspecialchars($row['project_name']) : 'Global Task';
                                ?>
                                    <tr style="border-bottom: 1px solid #f1f5f9; <?php echo $is_completed ? 'opacity: 0.7;' : ''; ?>">
                                        <td style="text-align: center; font-weight: 700; color: #64748b;">
                                            <span style="background:#f1f5f9; padding:4px 8px; border-radius:6px; font-size:12px;">#<?php echo str_pad($row['id'], 3, '0', STR_PAD_LEFT); ?></span>
                                        </td>
                                        <td>
                                            <div style="font-weight: 700; color: #1e293b; font-size: 14px; <?php echo $is_completed ? 'text-decoration: line-through; color: #94a3b8;' : ''; ?>">
                                                <?php echo htmlspecialchars($row['task_name']); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="font-weight: 700; color: #334155; font-size: 13px;">
                                                <?php echo $proj_name; ?>
                                            </div>
                                            <?php if (!empty($row['client_name'])) : ?>
                                                <div style="font-size: 11px; color: #94a3b8; font-weight: 600; margin-top: 2px;">
                                                    <i class="fa fa-user"></i> <?php echo htmlspecialchars($row['client_name']); ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td style="font-weight: 600; color: #475569; font-size: 13px;">
                                            <?php echo !empty($row['due_date']) ? date('d M Y', strtotime($row['due_date'])) : '--'; ?>
                                        </td>
                                        <td>
                                            <span style="padding: 4px 10px; border-radius: 6px; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; <?php echo $priority_badge; ?> display: inline-block;">
                                                <?php echo ucfirst($priority); ?>
                                            </span>
                                        </td>
                                        <td style="text-align: center; padding: 15px;">
                                            <span style="padding: 6px 14px; border-radius: 12px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; <?php echo $status_badge; ?> display: inline-block; min-width: 90px;">
                                                <?php echo $status_label; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 60px 40px; color: #94a3b8;">
                                        <i class="fa fa-check-square-o" style="font-size: 42px; display: block; margin-bottom: 15px; opacity: 0.5;"></i>
                                        <h4 style="color: #64748b; font-weight: 700; margin-bottom: 5px;">No Tasks Assigned</h4>
                                        <p style="font-size: 13px; font-weight: 500;">You're all caught up! There are no pending tasks.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
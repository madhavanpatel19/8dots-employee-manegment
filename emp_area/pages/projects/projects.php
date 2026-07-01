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

$status_filter = isset($_GET['status']) ? mysqli_real_escape_string($con, $_GET['status']) : '';
$where_clause = " WHERE FIND_IN_SET('$emp_id', cp.assigned_employees) > 0 ";
if ($status_filter) {
    $where_clause .= " AND cp.status='$status_filter' ";
}

$limit = 10;
$page = isset($_GET['page']) && intval($_GET['page']) > 0 ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Total records for pagination
$countSql = "SELECT COUNT(*) as total FROM client_projects cp " . $where_clause;
$countResult = mysqli_query($con, $countSql);
$totalRecords = 0;
if ($countResult) {
    $row = mysqli_fetch_assoc($countResult);
    $totalRecords = $row['total'];
}
$totalPages = ceil($totalRecords / $limit);

// Fetch projects assigned to this employee
$query = "SELECT cp.*, c.name as client_name 
          FROM client_projects cp 
          LEFT JOIN clients c ON cp.client_id = c.id 
          $where_clause 
          ORDER BY cp.id DESC LIMIT $offset, $limit";
$result = mysqli_query($con, $query);

// Count projects for Cards (Assigned to this employee)
$total_projects = mysqli_num_rows(mysqli_query($con, "SELECT id FROM client_projects WHERE FIND_IN_SET('$emp_id', assigned_employees) > 0"));
$active_projects = mysqli_num_rows(mysqli_query($con, "SELECT id FROM client_projects WHERE status='Active' AND FIND_IN_SET('$emp_id', assigned_employees) > 0"));
$pending_projects = mysqli_num_rows(mysqli_query($con, "SELECT id FROM client_projects WHERE status='Pending' AND FIND_IN_SET('$emp_id', assigned_employees) > 0"));
$completed_projects = mysqli_num_rows(mysqli_query($con, "SELECT id FROM client_projects WHERE status='Completed' AND FIND_IN_SET('$emp_id', assigned_employees) > 0"));

?>

<div class="premium-ui-enabled">
    <div class="stat-cards-row">
        <!-- Total Projects -->
        <div class="stat-card" style="cursor: pointer; transition: 0.3s;" onclick="window.location.href='index.php?projects'">
            <div class="stat-card-icon sc-purple">
                <i class="fa fa-briefcase"></i>
            </div>
            <div class="stat-card-body">
                <div class="stat-card-title">Total Projects</div>
                <div class="stat-card-value"><?php echo $total_projects; ?></div>
            </div>
        </div>

        <!-- Active Projects -->
        <div class="stat-card" style="cursor: pointer; transition: 0.3s;" onclick="window.location.href='index.php?projects&status=Active'">
            <div class="stat-card-icon sc-green">
                <i class="fa fa-folder-open"></i>
            </div>
            <div class="stat-card-body">
                <div class="stat-card-title">Active Projects</div>
                <div class="stat-card-value"><?php echo $active_projects; ?></div>
            </div>
        </div>

        <!-- Completed Projects -->
        <div class="stat-card" style="cursor: pointer; transition: 0.3s;" onclick="window.location.href='index.php?projects&status=Completed'">
            <div class="stat-card-icon sc-orange">
                <i class="fa fa-check-circle"></i>
            </div>
            <div class="stat-card-body">
                <div class="stat-card-title">Completed Projects</div>
                <div class="stat-card-value"><?php echo $completed_projects; ?></div>
            </div>
        </div>

        <!-- Pending Projects -->
        <div class="stat-card" style="cursor: pointer; transition: 0.3s;" onclick="window.location.href='index.php?projects&status=Pending'">
            <div class="stat-card-icon sc-blue">
                <i class="fa fa-clock-o"></i>
            </div>
            <div class="stat-card-body">
                <div class="stat-card-title">Pending Projects</div>
                <div class="stat-card-value"><?php echo $pending_projects; ?></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="premium-card" style="border: none; border-radius: 20px; overflow: hidden; box-shadow: 0 4px 25px -5px rgba(0,0,0,0.08); background: #fff;">
                <div class="card-hdr" style="background: var(--p-bg-header); color: #fff; padding: 18px 25px; display: flex; align-items: center; gap: 12px; border: none;">
                    <i class="fa fa-list-ul" style="font-size: 16px; color: #fff;"></i>
                    <h3 style="margin: 0; font-size: 14px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #fff;">Project Assignments</h3>
                </div>
                <div style="overflow-x: auto;">
                    <table class="table-premium" style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #fcfdfe; border-bottom: 1.5px solid #f1f5f9;">
                                <th style="width: 60px; text-align: center; padding: 18px 15px; color: #64748b; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">ID</th>
                                <th style="padding: 18px 15px; color: #64748b; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">Project Name</th>
                                <th style="padding: 18px 15px; color: #64748b; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">Client</th>
                                <th style="padding: 18px 15px; color: #64748b; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">Start Date</th>
                                <th style="padding: 18px 15px; color: #64748b; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">Deadline</th>
                                <th style="text-align: center; padding: 18px 15px; color: #64748b; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result && mysqli_num_rows($result) > 0) : ?>
                                <?php while ($row = mysqli_fetch_assoc($result)) :
                                    $st = strtolower($row['status']);
                                    $badge_style = 'background: #f1f5f9; color: #64748b;';
                                    if ($st == 'completed') $badge_style = 'background: #ecfdf5; color: #059669;';
                                    elseif ($st == 'active' || $st == 'in progress') $badge_style = 'background: #eff6ff; color: #2563eb;';
                                    elseif ($st == 'pending') $badge_style = 'background: #fff7ed; color: #ea580c;';
                                    elseif ($st == 'cancelled') $badge_style = 'background: #fef2f2; color: #dc2626;';
                                ?>
                                    <tr style="border-bottom: 1px solid #f1f5f9;">
                                        <td style="text-align: center; font-weight: 700; color: #64748b;">
                                            <span style="background:#f1f5f9; padding:4px 8px; border-radius:6px; font-size:12px;">#<?php echo str_pad($row['id'], 3, '0', STR_PAD_LEFT); ?></span>
                                        </td>
                                        <td>
                                            <div style="font-weight: 700; color: #1e293b; font-size: 14px;">
                                                <?php echo htmlspecialchars($row['project_name']); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="font-weight: 600; color: #64748b; font-size: 13px;">
                                                <i class="fa fa-user" style="margin-right: 5px; opacity: 0.6;"></i>
                                                <?php echo htmlspecialchars($row['client_name'] ?? 'N/A'); ?>
                                            </div>
                                        </td>
                                        <td style="font-weight: 600; color: #475569; font-size: 13px;">
                                            <?php echo !empty($row['project_date']) ? date('d M Y', strtotime($row['project_date'])) : '--'; ?>
                                        </td>
                                        <td style="font-weight: 600; color: #475569; font-size: 13px;">
                                            <?php echo !empty($row['deadline']) ? date('d M Y', strtotime($row['deadline'])) : '--'; ?>
                                        </td>
                                        <td style="text-align: center; padding: 15px;">
                                            <span style="padding: 6px 14px; border-radius: 12px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; <?php echo $badge_style; ?> display: inline-block; min-width: 90px;">
                                                <?php echo htmlspecialchars($row['status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 60px 40px; color: #94a3b8;">
                                        <i class="fa fa-folder-open-o" style="font-size: 42px; display: block; margin-bottom: 15px; opacity: 0.5;"></i>
                                        <h4 style="color: #64748b; font-weight: 700; margin-bottom: 5px;">No Projects Assigned</h4>
                                        <p style="font-size: 13px; font-weight: 500;">You are currently not assigned to any projects.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <!-- Pagination UI -->
                <?php if ($totalPages > 1): ?>
                    <div class="pagination-premium">
                        <?php
                        $queryParams = $_GET;
                        unset($queryParams['page']);
                        $qString = http_build_query($queryParams);
                        $baseUrl = "index.php";
                        if (!empty($qString)) {
                            $baseUrl .= "?" . $qString . "&";
                        } else {
                            $baseUrl .= "?";
                        }
                        ?>
                        <a href="<?php echo $baseUrl; ?>page=<?php echo max(1, $page - 1); ?>" class="page-link <?php echo $page <= 1 ? 'disabled' : ''; ?>"><i class="fa fa-angle-left"></i> Prev</a>

                        <?php
                        $startPage = max(1, $page - 2);
                        $endPage = min($totalPages, $startPage + 4);
                        if ($endPage - $startPage < 4) {
                            $startPage = max(1, $endPage - 4);
                        }

                        for ($p = $startPage; $p <= $endPage; $p++):
                        ?>
                            <a href="<?php echo $baseUrl; ?>page=<?php echo $p; ?>" class="page-link <?php echo $page == $p ? 'active' : ''; ?>"><?php echo $p; ?></a>
                        <?php endfor; ?>

                        <a href="<?php echo $baseUrl; ?>page=<?php echo min($totalPages, $page + 1); ?>" class="page-link <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">Next <i class="fa fa-angle-right"></i></a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
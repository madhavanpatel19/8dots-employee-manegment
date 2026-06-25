<?php
if (!isset($_SESSION['admin_email'])) {
    echo "<script>window.open('../../pages/auth/login.php','_self')</script>";
    exit();
}

/* ==============================
   FETCH EMPLOYEE LIST
============================== */
$empList = [];
$empQ = mysqli_query($con, "SELECT id, name FROM emp_list ORDER BY name ASC");
while ($erow = mysqli_fetch_assoc($empQ)) $empList[] = $erow;

/* ==============================
   HANDLE FILTERS SAFELY
============================== */
$filter_emp    = isset($_GET['emp_id']) ? intval($_GET['emp_id']) : '';
$filter_status = isset($_GET['status']) ? trim($_GET['status']) : '';
$filter_from   = isset($_GET['from']) ? trim($_GET['from']) : '';
$filter_to     = isset($_GET['to']) ? trim($_GET['to']) : '';

$where = [];
if (!empty($filter_emp)) $where[] = "a.emp_id = $filter_emp";
if (!empty($filter_status) && in_array($filter_status, ['present', 'absent', 'leave'])) {
    $st_esc = mysqli_real_escape_string($con, $filter_status);
    $where[] = "a.status = '$st_esc'";
}
if (!empty($filter_from)) $where[] = "DATE(a.attendance_date) >= '" . mysqli_real_escape_string($con, $filter_from) . "'";
if (!empty($filter_to))   $where[] = "DATE(a.attendance_date) <= '" . mysqli_real_escape_string($con, $filter_to) . "'";

$whereSql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

/* ==============================
   PAGINATION SETUP
============================== */
$limit = 10; // Number of records per page
$page = isset($_GET['page']) && intval($_GET['page']) > 0 ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Count total records
$countSql = "SELECT COUNT(*) as total 
             FROM attendance a 
             LEFT JOIN emp_list e ON a.emp_id = e.id 
             $whereSql";
$countResult = mysqli_query($con, $countSql);
$totalRecords = 0;
if ($countResult) {
    $countRow = mysqli_fetch_assoc($countResult);
    $totalRecords = $countRow['total'];
}
$totalPages = ceil($totalRecords / $limit);

$sql = "SELECT a.*, e.name AS emp_name, e.employee_image 
        FROM attendance a 
        LEFT JOIN emp_list e ON a.emp_id = e.id 
        $whereSql
        ORDER BY a.attendance_date DESC, a.id DESC
        LIMIT $limit OFFSET $offset";
$result = mysqli_query($con, $sql);
?>

<div class="page-wrapper premium-ui-enabled">
    <div class="page-header-premium">
        <h1></h1>
        <div class="header-actions">
            <!-- Optional: Filter Toggle or Export Button -->
            <button class="btn-premium-add" onclick="window.filter()">
                <i class="fa fa-filter"></i> filter
            </button>
            <button class="btn-premium-add" onclick="openWorkGallery()">
                <i class="fa fa-photo"></i> Photo Gallery
            </button>
            <button class="btn-premium-add" onclick="window.print()">
                <i class="fa fa-print"></i> Print Report
            </button>
        </div>
    </div>

    <!-- Filter Section -->
    <div id="filter-section" class="premium-card filter-card" style="display: <?php echo (!empty($filter_emp) || !empty($filter_status) || !empty($filter_from) || !empty($filter_to)) ? 'block' : 'none'; ?>; ">
        <div class="card-hdr">
            <i class="fa fa-sliders"></i>
            <h3>Filter</h3>
            <button class="btn-close-filter" onclick="window.filter()" style="margin-left: auto; background: none; border: none; color: #94a3b8; cursor: pointer;">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <form method="GET" action="index.php" style="padding: 20px;">
            <input type="hidden" name="worksheettable" value="">
            <div class="filter-grid">
                <div class="filter-group">
                    <label>Employee</label>
                    <select name="emp_id" class="p-input-premium">
                        <option value="">All Employees</option>
                        <?php foreach ($empList as $e): ?>
                            <option value="<?php echo $e['id']; ?>" <?php if ($filter_emp == $e['id']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($e['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Status</label>
                    <select name="status" class="p-input-premium">
                        <option value="">All Statuses</option>
                        <option value="present" <?php if ($filter_status == 'present') echo 'selected'; ?>>Present</option>
                        <option value="absent" <?php if ($filter_status == 'absent') echo 'selected'; ?>>Absent</option>
                        <option value="leave" <?php if ($filter_status == 'leave') echo 'selected'; ?>>Leave</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>From Date</label>
                    <input type="date" name="from" class="p-input-premium" value="<?php echo $filter_from; ?>">
                </div>
                <div class="filter-group">
                    <label>To Date</label>
                    <input type="date" name="to" class="p-input-premium" value="<?php echo $filter_to; ?>">
                </div>
            </div>
            <div style="display: flex; gap: 10px; margin-top: 20px; justify-content: flex-end;">
                <a href="index.php?worksheettable" class="btn-clear-filter">
                    <i class="fa fa-refresh"></i> Clear
                </a>
                <button type="submit" class="btn-premium-add">
                    <i class="fa fa-check"></i> Apply Filters
                </button>
            </div>
        </form>
    </div>

    <div class="premium-card">
        <div class="card-hdr">
            <i class="fa fa-table"></i>
            <h3>Detailed Worksheet Activity</h3>
        </div>
        <div style="overflow-x: auto;">
            <table class="table-premium">
                <thead>
                    <tr>
                        <th style="width: 60px; text-align: center;">#</th>
                        <th>Employee</th>
                        <th>Date & Time</th>
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: center;">Performance</th>
                        <th>Remarks</th>
                        <th>Recorded On</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && mysqli_num_rows($result) > 0): $i = $offset + 1; ?>
                        <?php while ($row = mysqli_fetch_assoc($result)):
                            $st = $row['status'];
                            $badge_class = 'p-badge-secondary';
                            if ($st == 'present') $badge_class = 'p-badge-success';
                            elseif ($st == 'absent') $badge_class = 'p-badge-danger';
                            elseif ($st == 'leave') $badge_class = 'p-badge-primary';

                            $img = !empty($row['employee_image']) ? 'uploads/' . $row['employee_image'] : '../admin_area/admin_images/default.png';
                        ?>
                            <tr>
                                <td style="text-align: center; color: var(--p-secondary); font-weight: 700;"><?php echo $i++; ?></td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <img src="<?php echo $img; ?>" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 1px solid #e2e8f0;">
                                        <div style="font-weight: 700; color: var(--p-text);"><?php echo htmlspecialchars($row['emp_name']); ?></div>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: var(--p-text);"><?php echo date('d M Y', strtotime($row['attendance_date'])); ?></div>
                                    <div style="font-size: 11px; color: var(--p-secondary);">
                                        <i class="fa fa-clock-o"></i> <?php echo $row['check_in_time'] ?: '--:--'; ?> - <?php echo $row['check_out_time'] ?: '--:--'; ?>
                                    </div>
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    <span class="p-badge <?php echo $badge_class; ?>" style="display: inline-flex; justify-content: center; min-width: 80px; white-space: nowrap;">
                                        <?php echo ucfirst($st); ?>
                                    </span>
                                </td>
                                <td style="text-align: center;">
                                    <?php if (isset($row['performance'])): ?>
                                        <div style="font-weight: 800; color: #4f46e5;"><?php echo $row['performance']; ?>%</div>
                                    <?php else: ?>
                                        <span style="color: #cbd5e1;">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div style="max-width: 350px; min-width: 200px; font-size: 13px; color: var(--p-secondary); line-height: 1.6; white-space: normal; word-wrap: break-word;">
                                        <?php echo nl2br(htmlspecialchars($row['remarks'] ?: '-')); ?>

                                        <?php
                                        if (!empty($row['work_photos'])) {
                                            $photos = json_decode($row['work_photos'], true);
                                            if (!empty($photos)) {
                                                echo '<div class="work-photo-container-premium">';
                                                foreach ($photos as $p) {
                                                    echo '<div class="work-photo-item-mini" onclick="window.open(\'' . htmlspecialchars($p) . '\')" title="Click to view full image">
                                                            <img src="' . htmlspecialchars($p) . '">
                                                            <div class="work-photo-overlay-mini"><i class="fa fa-search-plus"></i></div>
                                                          </div>';
                                                }
                                                echo '</div>';
                                            }
                                        }
                                        ?>
                                    </div>
                                </td>
                                <td style="font-size: 11px; color: var(--p-secondary);"><?php echo date('d-m-Y H:i', strtotime($row['created_at'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 50px; color: #94a3b8;">
                                <i class="fa fa-folder-open-o" style="font-size: 40px; display: block; margin-bottom: 10px;"></i>
                                No worksheet records found for the selected criteria.
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
                // Display up to 5 page numbers
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

<div id="workGalleryModal" class="modal fade" role="dialog" style="z-index: 99999;">
    <div class="modal-dialog modal-lg" style="margin-top: 40px; max-width: 900px;">
        <div class="modal-content premium-modal-content-v2" style="border: none; border-radius: 32px; box-shadow: 0 40px 100px -20px rgba(111, 50, 50, 0.4); overflow: hidden;">
            <div class="modal-header" style="background: #ffeaeb; color:black; padding: 25px 35px; border: none; position: relative;">
                <button type="button" class="close" data-dismiss="modal" style="color: black; opacity: 0.5; position: absolute; right: 25px; top: 25px; font-size: 24px;">&times;</button>
                <div style="display: flex; align-items: center; justify-content: space-between; width: 100%; padding-right: 40px;">
                    <div style="display: flex; align-items: center; gap: 18px;">
                        <div style="width: 48px; height: 48px; background: #dd2127; color:white;border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 20px; box-shadow: 0 8px 16px rgba(185, 81, 81, 0.3);">
                            <i class="fa fa-th-large"></i>
                        </div>
                        <div>
                            <h4 class="modal-title" style="font-weight: 800; font-size: 20px; margin: 0; letter-spacing: -0.5px;">Work Submission Archive</h4>
                            <p style="margin: 4px 0 0 0; font-size: 11px; color: #94a3b8; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Live Visual Insights</p>
                        </div>
                    </div>

                    <!-- Employee Filter Inside Modal -->
                    <div class="modal-header-filter">
                        <i class="fa fa-user-circle"></i>
                        <select id="modal_emp_filter" onchange="openWorkGallery(this.value)" class="modal-select-premium">
                            <option value="">All Employees</option>
                            <?php foreach ($empList as $e): ?>
                                <option value="<?php echo $e['id']; ?>"><?php echo htmlspecialchars($e['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-body" style="padding: 0; background: #fff; min-height: 450px; max-height: 75vh; overflow-y: auto;">
                <div id="gallery-content-container">
                    <!-- Gallery Content -->
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .p-badge {
        padding: 4px 12px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .p-badge-success {
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
    }

    .p-badge-primary {
        background: rgba(37, 99, 235, 0.1);
        color: #2563eb;
    }

    .p-badge-danger {
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
    }

    .p-badge-secondary {
        background: #f1f5f9;
        color: #64748b;
    }

    /* Ensure table row height is consistent even with long remarks */
    .table-premium td {
        vertical-align: middle !important;
        padding: 12px 15px !important;
    }

    /* Pagination Styles */
    .pagination-premium {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        padding: 20px;
        gap: 8px;
        border-top: 1px solid #f1f5f9;
    }

    .page-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        padding: 0 12px;
        border-radius: 8px;
        background: #fff;
        border: 1px solid #e2e8f0;
        color: #64748b;
        font-weight: 600;
        font-size: 13px;
        text-decoration: none !important;
        transition: all 0.2s;
        gap: 6px;
    }

    .page-link:hover:not(.disabled) {
        background: #dd2127;
        color: #FFF;
        border-color: #dd212d;
        text-decoration: none !important;
    }

    .page-link.active {
        background: #ffeaeb;
        color: #dd2127;
        border-color: #dd2127;
        text-decoration: none !important;
    }

    .page-link.disabled {
        opacity: 0.5;
        pointer-events: none;
        background: #f8fafc;
    }

    .page-link:focus,
    .page-link:active,
    .page-link:focus-visible {
        outline: none !important;
        box-shadow: none !important;
        text-decoration: none !important;
        -webkit-tap-highlight-color: transparent;
    }

    /* Filter Styles */
    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }

    .filter-group label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 8px;
    }

    .btn-apply-filter {
        background: #4f46e5;
        color: white;
        border: none;
        padding: 10px 25px;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-apply-filter:hover {
        background: #dd2127;
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(221, 33, 39, 0.4);
    }

    .btn-clear-filter {
        background: #f1f5f9;
        color: #dd2127;
        padding: 10px 25px;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        text-decoration: none !important;
    }

    .btn-clear-filter:hover {
        background: #e2e8f0;
        color: #1e293b;
    }

    .p-input-premium {
        width: 100%;
        padding: 10px 15px;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        background-color: #fff;
        color: #1e293b;
        font-weight: 500;
        outline: none;
        transition: all 0.2s;
    }

    .p-input-premium:focus {
        border-color: #dd2127;
        box-shadow: 0 0 0 4px rgba(221, 33, 39, 0.1);
    }

    /* Inline Mini Thumbnails */
    .work-photo-container-premium {
        display: flex;
        gap: 10px;
        margin-top: 15px;
        flex-wrap: wrap;
        align-items: center;
    }

    .work-photo-item-mini {
        width: 54px;
        height: 54px;
        border-radius: 12px;
        overflow: hidden;
        position: relative;
        cursor: pointer;
        border: 2.5px solid #fff;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .work-photo-item-mini:hover {
        transform: translateY(-5px) scale(1.1);
        box-shadow: 0 12px 20px rgba(0, 0, 0, 0.15);
        z-index: 10;
    }

    .work-photo-item-mini img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .work-photo-overlay-mini {
        position: absolute;
        inset: 0;
        background: rgba(221, 33, 39, 0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        opacity: 0;
        transition: 0.3s;
        font-size: 14px;
    }

    .work-photo-item-mini:hover .work-photo-overlay-mini {
        opacity: 1;
    }

    /* Premium Modal V2 */
    .premium-modal-content-v2 {
        animation: modalReveal 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        backdrop-filter: blur(25px);
    }

    @keyframes modalReveal {
        from {
            opacity: 0;
            transform: scale(0.95) translateY(30px);
        }

        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    /* Enhance Modal Backdrop */
    .modal-backdrop.in {
        opacity: 0.7 !important;
        background-color: #0f172a !important;
        backdrop-filter: blur(8px);
    }

    /* Advanced Pulse Loader */
    .premium-loader-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 100px 0;
    }

    .premium-pulse-loader {
        width: 60px;
        height: 60px;
        background: #6366f1;
        border-radius: 20px;
        animation: pulseAndRotate 2s infinite ease-in-out;
        box-shadow: 0 0 0 0 rgba(99, 102, 241, 0.4);
    }

    @keyframes pulseAndRotate {
        0% {
            transform: scale(0.8) rotate(0deg);
            border-radius: 20px;
        }

        50% {
            transform: scale(1.2) rotate(180deg);
            border-radius: 50%;
            box-shadow: 0 0 0 20px rgba(99, 102, 241, 0);
        }

        100% {
            transform: scale(0.8) rotate(360deg);
            border-radius: 20px;
        }
    }

    .loader-text-premium {
        margin-top: 25px;
        font-weight: 800;
        color: #1e293b;
        letter-spacing: 1px;
        text-transform: uppercase;
        font-size: 12px;
        animation: fadeInOut 1.5s infinite;
    }

    @keyframes fadeInOut {

        0%,
        100% {
            opacity: 0.4;
        }

        50% {
            opacity: 1;
        }
    }

    /* Modal Header Filter Styling */
    .modal-header-filter {
        display: flex;
        align-items: center;
        background: #dd2127;
        padding: 8px 15px;
        border-radius: 14px;
        border: 1px solid rgba(241, 99, 99, 0.3);
        gap: 10px;
        transition: all 0.3s;
    }

    .modal-header-filter:hover {
        background: #dd2127;
        border-color: rgba(241, 99, 99, 0.3);
    }

    .modal-header-filter i {
        color: white;
        font-size: 16px;
    }

    .modal-select-premium {
        background: transparent;
        border: none;
        color: #fff;
        font-weight: 700;
        font-size: 13px;
        outline: none;
        cursor: pointer;
        padding-right: 5px;
    }

    .modal-select-premium option {
        background: #0f172a;
        color: #fff;
    }
</style>

<script>
    window.filter = function() {
        const section = document.getElementById('filter-section');
        if (section.style.display === 'none') {
            section.style.display = 'block';
            section.style.animation = 'slideDown 0.3s ease-out forwards';
        } else {
            section.style.display = 'none';
        }
    };

    window.openWorkGallery = function(forceEmpId = null) {
        const modal = $('#workGalleryModal');
        const container = $('#gallery-content-container');

        // Get current filters
        let empId = forceEmpId !== null ? forceEmpId : $('select[name="emp_id"]').val();
        const status = $('select[name="status"]').val();
        const from = $('input[name="from"]').val();
        const to = $('input[name="to"]').val();

        // Sync modal select if it exists
        if ($('#modal_emp_filter').length > 0 && forceEmpId === null) {
            $('#modal_emp_filter').val(empId);
        }

        container.html(`
            <div class="premium-loader-wrapper">
                <div class="premium-pulse-loader"></div>
                <div class="loader-text-premium">Curating Gallery...</div>
            </div>
        `);

        // Only show modal if it's not already shown
        if (!modal.is(':visible')) {
            modal.modal('show');
        }

        $.ajax({
            url: 'ajax/gallery/ajax_view_work_gallery.php',
            method: 'GET',
            data: {
                emp_id: empId,
                status: status,
                from: from,
                to: to
            },
            success: function(response) {
                container.hide().html(response).fadeIn(600);
            },
            error: function() {
                container.html('<div style="padding: 100px; text-align: center; color: #ef4444; font-weight: 700;"><i class="fa fa-exclamation-triangle"></i> ARCHIVE TEMPORARILY OFFLINE</div>');
            }
        });
    }
</script>

<style>
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media print {

        /* Hide UI components not needed in the report */
        .modern-topbar,
        .modern-sidebar,
        #sidebar,
        .page-header-premium,
        #filter-section,
        .pagination-premium,
        .btn-premium-add,
        .card-hdr button,
        .card-hdr i {
            display: none !important;
        }

        /* Reset the wrapper and body layout to utilize the full page width */
        #page-wrapper,
        body,
        html {
            margin: 0 !important;
            padding: 0 !important;
            background: #fff !important;
        }

        .page-wrapper {
            padding: 0 !important;
        }

        .premium-card {
            border: none !important;
            box-shadow: none !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        /* For the detailed activity table header */
        .card-hdr h3 {
            display: block !important;
            text-align: center;
            font-size: 20px;
            margin-bottom: 20px;
            width: 100%;
        }
    }
</style>
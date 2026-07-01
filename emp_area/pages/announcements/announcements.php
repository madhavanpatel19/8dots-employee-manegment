<?php
// =============================================================
// emp_area/pages/announcements/announcements.php
// List all announcements
// =============================================================
if (!isset($_SESSION['emp_id'])) {
    echo "<script>window.open('../../pages/auth/login.php','_self')</script>";
    exit();
}

$emp_id = $_SESSION['emp_id'];

// Get all announcements
$query = "SELECT * FROM announcements WHERE is_active = 1 AND (publish_date IS NULL OR publish_date <= NOW()) AND (end_date IS NULL OR end_date >= NOW()) ORDER BY publish_date DESC";
$result = mysqli_query($con, $query);
?>
<div class="premium-ui-enabled">
    <div class="row">
        <div class="col-lg-12">
            <div class="premium-card">
                <div class="card-hdr">
                    <i class="fa fa-list"></i>
                    <h3>All Announcements</h3>
                </div>
                <div style="overflow-x: auto;">
                    <table class="table-premium">
                        <thead>
                            <tr>
                                <th style="width: 60px; text-align: center;">#</th>
                                <th>Title</th>
                                <th style="text-align: center;">Posted On</th>
                                <th style="text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) > 0) : $i = 1; ?>
                                <?php while ($row = mysqli_fetch_assoc($result)) :
                                    $ann_id = $row['id'];
                                    $check_read = "SELECT id FROM announcement_read WHERE announcement_id='$ann_id' AND emp_id='$emp_id'";
                                    $run_check = mysqli_query($con, $check_read);
                                    $is_unread = (mysqli_num_rows($run_check) == 0);
                                ?>
                                    <tr style="<?php echo $is_unread ? 'background: #fff8f8;' : ''; ?>">
                                        <td style="text-align: center; font-weight: 700; color: #64748b;"><?php echo $i++; ?></td>
                                        <td style="font-weight: 600; color: #1e293b;">
                                            <?php echo htmlspecialchars($row['title']); ?>
                                            <?php if ($is_unread) echo '<span class="p-badge p-badge-danger" style="margin-left:8px;">New</span>'; ?>
                                        </td>
                                        <td style="font-weight: 600; color: #64748b; text-align: center;"><?php echo date('d M Y, h:i A', strtotime($row['publish_date'] ?? $row['created_at'])); ?></td>
                                        <td style="text-align: center;">
                                            <a href="index.php?view_announcement=<?php echo $ann_id; ?>" class="btn btn-sm" style="background:#f1f5f9; color:#475569; font-weight:600; border-radius:8px;">
                                                <i class="fa fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="4" style="text-align: center; padding: 40px; color: #94a3b8;">
                                        <i class="fa fa-folder-open-o" style="font-size: 32px; display: block; margin-bottom: 10px;"></i>
                                        No announcements found.
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
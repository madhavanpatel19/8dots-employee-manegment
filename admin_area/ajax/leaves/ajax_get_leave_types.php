<?php
if (!isset($con)) { include(__DIR__ . '/../../includes/db.php'); }

$query = "SELECT * FROM leave_types ORDER BY created_at DESC";
$result = mysqli_query($con, $query);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        ?>
        <div class="leave-type-card">
            <div>
                <div style="font-weight: 800; color: #1e293b; font-size: 14px;"><?php echo htmlspecialchars($row['leave_name']); ?></div>
                <div style="font-size: 11px; color: #94a3b8; font-weight: 700; text-transform: uppercase;">Allowed: <?php echo $row['num_of_leave']; ?> Days / Year</div>
            </div>
            <button onclick="deleteLeaveType(<?php echo $row['id']; ?>)" style="background: #fef2f2; color: #ef4444; border: none; width: 32px; height: 32px; border-radius: 8px; cursor: pointer; transition: 0.2s;">
                <i class="fa fa-trash"></i>
            </button>
        </div>
        <?php
    }
} else {
    echo '<div style="text-align: center; padding: 30px; color: #94a3b8; font-weight: 600;">No leave types defined.</div>';
}
?>

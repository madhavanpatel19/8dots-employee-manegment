<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
if (!isset($con)) { include(__DIR__ . '/../../includes/db.php'); }

if (!isset($_SESSION['admin_email'])) {
    exit;
}

$filename = "leads_export_" . date('Y-m-d') . ".csv";

// Set headers for download
header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=\"$filename\"");

$output = fopen("php://output", "w");

// Header row
fputcsv($output, array('ID', 'Client Name', 'Phone', 'Email', 'Company', 'Source', 'Budget', 'Status', 'Follow-up Date', 'Created At'));

$get_leads = "SELECT * FROM leads ORDER BY id DESC";
$run_leads = mysqli_query($con, $get_leads);

while($row = mysqli_fetch_assoc($run_leads)) {
    fputcsv($output, array(
        $row['id'],
        $row['client_name'],
        $row['phone'],
        $row['email'],
        $row['company_name'],
        $row['lead_source'],
        $row['budget'],
        $row['status'],
        $row['followup_date'],
        $row['created_at']
    ));
}

fclose($output);
exit;
?>

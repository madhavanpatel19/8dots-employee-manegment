<?php
header('Content-Type: application/json');
if (!isset($con)) { include(__DIR__ . '/../../includes/db.php'); }

if (!isset($_GET['project_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing project ID']);
    exit;
}

$project_id = mysqli_real_escape_string($con, $_GET['project_id']);

$get_phases = "SELECT * FROM project_budget_phases WHERE project_id = '$project_id' ORDER BY id ASC";
$run_phases = mysqli_query($con, $get_phases);

$phases = [];
while ($row = mysqli_fetch_assoc($run_phases)) {
    $phases[] = [
        'phase_name' => $row['phase_name'],
        'description' => $row['description'],
        'cost' => $row['cost'],
        'received_amount' => $row['received_amount'],
        'received_date' => $row['received_date'],
        'remark' => $row['remark']
    ];
}

$get_project = "SELECT currency FROM client_projects WHERE id = '$project_id'";
$run_project = mysqli_query($con, $get_project);
$project_data = mysqli_fetch_assoc($run_project);
$currency = !empty($project_data['currency']) ? $project_data['currency'] : 'INR';

echo json_encode([
    'success' => true, 
    'data' => $phases,
    'currency' => $currency
]);
?>

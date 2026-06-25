    <?php
    if (!isset($con)) { include(__DIR__ . '/../../includes/db.php'); }

    if (!isset($_GET['id'])) {
        die("Invalid ID");
    }

    $id = intval($_GET['id']);
    $query = "SELECT offer_latter, NDA, Aadhar_card, Pan_card, Passportsize_photo, old_company_slary_slip FROM emp_list WHERE id = $id";
    $res = mysqli_query($con, $query);
    $emp = mysqli_fetch_assoc($res);

    $doc_labels = [
        'offer_latter' => ['label' => 'Offer Letter', 'icon' => 'fa-file-text-o'],
        'NDA' => ['label' => 'NDA', 'icon' => 'fa-shield'],
        'Aadhar_card' => ['label' => 'Aadhar Card', 'icon' => 'fa-id-card-o'],
        'Pan_card' => ['label' => 'PAN Card', 'icon' => 'fa-id-card'],
        'Passportsize_photo' => ['label' => 'Passport Size Photo', 'icon' => 'fa-image'],
        'old_company_slary_slip' => ['label' => 'Old Company Salary Slip', 'icon' => 'fa-money']
    ];

    echo '<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 15px;">';

    // Show specific documents
    foreach ($doc_labels as $key => $info) {
        if (!empty($emp[$key])) {
            $file = htmlspecialchars($emp[$key]);
            echo "
            <div style='background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 15px; display: flex; align-items: center; gap: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);'>
                <div style='width: 40px; height: 40px; background: #eef2ff; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #4f46e5;'>
                    <i class='fa {$info['icon']}' style='font-size: 18px;'></i>
                </div>
                <div style='flex: 1; min-width: 0;'>
                    <div style='font-size: 11px; color: #94a3b8; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;'>{$info['label']}</div>
                    <div style='font-size: 13px; color: #1e293b; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;'>$file</div>
                </div>
                <a href='uploads/$file' target='_blank' class='btn btn-xs btn-default' style='border-radius: 6px; padding: 5px 8px;' title='View'><i class='fa fa-eye'></i></a>
            </div>";
        }
    }

    // Show additional documents
    $result_extra = mysqli_query($con, "SELECT * FROM employee_documents WHERE emp_id='$id'");
    while ($doc = mysqli_fetch_assoc($result_extra)) {
        $file = htmlspecialchars($doc['file_name']);
        echo "
        <div style='background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 15px; display: flex; align-items: center; gap: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);'>
            <div style='width: 40px; height: 40px; background: #f0fdf4; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #059669;'>
                <i class='fa fa-file-o' style='font-size: 18px;'></i>
            </div>
            <div style='flex: 1; min-width: 0;'>
                <div style='font-size: 11px; color: #94a3b8; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;'>Additional Doc</div>
                <div style='font-size: 13px; color: #1e293b; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;'>$file</div>
            </div>
            <a href='uploads/$file' target='_blank' class='btn btn-xs btn-default' style='border-radius: 6px; padding: 5px 8px;' title='View'><i class='fa fa-eye'></i></a>
        </div>";
    }

    echo '</div>';

    if (mysqli_num_rows($res) == 0 && mysqli_num_rows($result_extra) == 0) {
        echo '<p style="text-align: center; color: #94a3b8; padding: 40px;">No documents found for this employee.</p>';
    }
    ?>

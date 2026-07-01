<?php
if (!isset($con)) {
    include(__DIR__ . '/../../includes/db.php');
}

// Only allow access if logged in as employee
if (!isset($_SESSION['emp_id']) || !isset($_SESSION['emp_name'])) {
    header('Location: ../../pages/auth/login.php');
    exit();
}
?>

<div class="premium-ui-enabled">
    <div class="page-header-premium" style="display: flex; justify-content: space-between; align-items: center; padding: 20px 0; margin-bottom: 15px;">
        <h1 style="font-size: 24px; font-weight: 800; color: #1e293b; margin: 0; display: flex; align-items: center; gap: 12px;">
            <i class="fa fa-link" style="color: #1e293b;"></i> Quick Links
        </h1>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div style="background: #f8fafc; border-radius: 14px; padding: 10px 20px; margin-bottom: 25px; display: flex; align-items: center; gap: 8px; border: 1px solid #f1f5f9; width: fit-content;">
                <i class="fa fa-dashboard" style="color: #94a3b8; font-size: 12px;"></i>
                <span style="color: #94a3b8; font-size: 13px;">Dashboard</span>
                <span style="color: #cbd5e1; font-size: 12px;">/</span>
                <span style="color: #1e293b; font-size: 13px; font-weight: 700;">Quick Links</span>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="premium-card" style="border: none; border-radius: 20px; overflow: hidden; box-shadow: 0 4px 25px -5px rgba(0,0,0,0.08); background: #fff; padding: 40px; text-align: center;">
                <i class="fa fa-bookmark-o" style="font-size: 48px; color: #94a3b8; margin-bottom: 20px; display: block;"></i>
                <h3 style="color: #1e293b; font-weight: 800; margin-bottom: 10px;">Quick Links</h3>
                <p style="color: #64748b; font-size: 15px;">This section is under construction. Important links and resources will be available here soon.</p>
            </div>
        </div>
    </div>
</div>
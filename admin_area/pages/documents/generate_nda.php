<?php
ob_start();
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($con)) {
    include(__DIR__ . '/../../includes/db.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name'])) {

    $name = mysqli_real_escape_string($con, $_POST['name']);
    $employee_id = mysqli_real_escape_string($con, $_POST['employee_id']);
    $department = mysqli_real_escape_string($con, $_POST['department']);
    $designation = mysqli_real_escape_string($con, $_POST['designation']);
    $start_date = mysqli_real_escape_string($con, $_POST['start_date']);

    $insert_query = "INSERT INTO nda_forms
    (name, employee_id,department,designation, start_date)
    VALUES
    ('$name','$employee_id','$department','$designation','$start_date')";

    mysqli_query($con, $insert_query);
} else if (isset($_GET['id'])) {

    $id = mysqli_real_escape_string($con, $_GET['id']);

    $get_nda = "SELECT * FROM nda_forms WHERE id='$id'";
    $run_nda = mysqli_query($con, $get_nda);

    $row_nda = mysqli_fetch_array($run_nda);

    if ($row_nda) {
        $name = $row_nda['name'];
        $employee_id = $row_nda['employee_id'];
        $department = $row_nda['department'];
        $designation = $row_nda['designation'];
        $start_date = $row_nda['start_date'];
    } else {
        die("NDA record not found.");
    }
} else {
    header("Location: ../../index.php?view_nda");
    exit();
}

$date_formatted = date("d F Y", strtotime($start_date));
$current_date = date("d F Y");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Handbook - <?php echo htmlspecialchars($name); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Georgia&display=swap" rel="stylesheet">
    <link href="../../css/style.css" rel="stylesheet">
    <style>
        /* ============================================
           RESET & BASE
        ============================================ */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            background: #ececec;
            font-family: 'Montserrat', sans-serif;
            color: #333;
        }

        @page {
            size: A4;
            margin: 0;
        }

        /* ============================================
           PAGE LAYOUT — shared across all pages
        ============================================ */
        .page-wrap {
            width: 100%;
            margin: 20px auto;
            display: flex;
            justify-content: center;
        }

        /* Base sheet — A4 fixed height */
        .letter-sheet {
            background: #fff;
            width: 210mm;
            height: 297mm;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .10);
        }

        /* Inner content wrapper used by pages 2-4 */
        .page-inner {
            /* leaves room at bottom for the absolute footer (24+8+line = ~48px) */
            padding: 38px 50px 72px;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        /* ============================================
           SHARED — PAGE HEADER (top bar)
        ============================================ */
        .page-header {
            font-family: 'Montserrat', sans-serif;
            display: flex;
            justify-content: space-between;
            font-size: 10.5px;
            color: #b0b7c3;
            border-bottom: 1px solid #eef1f6;
            padding-bottom: 8px;
            margin-bottom: 26px;
            flex-shrink: 0;
        }

        /* ============================================
           SHARED — PAGE FOOTER (absolute bottom)
        ============================================ */
        .page-footer {
            font-family: 'Montserrat', sans-serif;
            position: absolute;
            bottom: 22px;
            left: 50px;
            right: 50px;
            display: flex;
            justify-content: space-between;
            font-size: 10.5px;
            color: #b0b7c3;
            border-top: 1px solid #eef1f6;
            padding-top: 7px;
        }

        /* ============================================
           SHARED — SECTION BADGE & HEADING
        ============================================ */
        .section-badge {
            font-family: 'Montserrat', sans-serif;
            color: #4a7be0;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.8px;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .section-heading {
            font-family: 'Georgia', 'Times New Roman', serif;
            font-size: 28px;
            font-weight: 700;
            color: #0e1726;
            line-height: 1.15;
            margin-bottom: 12px;
        }

        .section-desc {
            font-family: 'Georgia', 'Times New Roman', serif;
            font-size: 12.5px;
            color: #445166;
            line-height: 1.55;
            margin-bottom: 18px;
        }

        /* ============================================
           SHARED — BODY TEXT / PARAGRAPHS
        ============================================ */
        .body-text {
            font-family: 'Georgia', 'Times New Roman', serif;
            font-size: 12.5px;
            color: #2c384e;
            line-height: 1.55;
        }

        .body-text p {
            margin-bottom: 8px;
        }

        /* ============================================
           SHARED — SUB-LABEL (OUR VISION / OUR MISSION)
        ============================================ */
        /* .sub-label {
            font-family: 'Montserrat', sans-serif;
            font-size: 12px;
            font-weight: 700;
            color: #0c0c0c;
            margin-bottom: 3px;
        } */
        .sub-label {
            font-family: "Times New Roman", Georgia, serif;
            font-size: 14px;
            font-weight: 700;
            color: #0b2347;
            line-height: 1.4;
            margin: 0 0 24px 0;
            letter-spacing: 0;
        }

        /* ============================================
           SHARED — BULLET LIST
        ============================================ */
        .bullet-list {
            list-style: none;
            padding: 0;
            margin: 3px 0 8px 0;
            font-family: 'Georgia', 'Times New Roman', serif;
            font-size: 12.5px;
            color: #445166;
            line-height: 1.5;
        }

        .bullet-list li {
            position: relative;
            padding-left: 14px;
            margin-bottom: 2px;
        }

        .bullet-list li::before {
            content: "•";
            color: #4a7be0;
            font-weight: bold;
            position: absolute;
            left: 0;
        }

        .italic-summary {
            font-family: 'Georgia', 'Times New Roman', serif;
            font-style: italic;
            color: #697a98;
            font-size: 12px;
            margin-bottom: 16px;
        }

        /* ============================================
           SHARED — TOC STYLES (Pages 2 & 3)
        ============================================ */
        .toc-part-block {
            margin-bottom: 18px;
        }

        .toc-part-header {
            font-family: 'Montserrat', sans-serif;
            color: #4a7be0;
            font-size: 12px;
            font-weight: 700;
            /* letter-spacing: 1.8px; */
            border-bottom: 1.5px solid #e5e9f0;
            padding-bottom: 4px;
            margin-bottom: 5px;
        }

        .toc-part-list {
            font-family: 'Georgia', 'Times New Roman', serif;
            font-size: 13.5px;
            color: #2c384e;
            line-height: 1.72;
            padding-left: 0;
        }

        .toc-part-list div {
            display: flex;
            align-items: baseline;
            gap: 10px;
        }

        .toc-part-list .toc-num {
            font-family: 'Georgia', 'Times New Roman', serif;
            font-weight: 400;
            color: #2c384e;
            min-width: 22px;
            display: inline-block;
        }

        /* legacy strong tag support */
        .toc-part-list strong {
            font-family: 'Georgia', 'Times New Roman', serif;
            font-weight: 400;
            color: #2c384e;
            margin-right: 8px;
        }


        /* ============================================
           New TOC Header (Pages 2 & 3)
        ============================================ */
        .toc-part-header {
            display: flex;
            align-items: center;
            gap: 18px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 4px;
            margin-bottom: 5px;
        }

        .toc-number {
            font-family: 'Georgia', 'Times New Roman', serif;
            font-size: 21px;
            font-weight: 700;
            color: #3F6FE5;
            line-height: 1;
        }

        .toc-title {
            font-family: "Times New Roman", Georgia, serif;
            font-size: 21px;
            letter-spacing: 0px;
            font-weight: 700;
            color: #111827;
            line-height: 1.2;
        }

        /* ============================================
           SHARED — DOCUMENT CONTROL TABLE
        ============================================ */
        .control-table {
            width: 100%;
            border-collapse: collapse;
            font-family: 'Georgia', 'Times New Roman', serif;
            font-size: 13px;
            border: 1px solid #e5e9f0;
            margin-bottom: 20px;
        }

        .control-table tr {
            border-bottom: 1px solid #e5e9f0;
        }

        .control-table tr:last-child {
            border-bottom: none;
        }

        .control-table td.lbl {
            padding: 7px 12px;
            font-weight: 700;
            color: #1a233a;
            width: 35%;
            border-right: 1px solid #e5e9f0;
            background: #f8fafc;
        }

        .control-table td.val {
            padding: 7px 12px;
            color: #445166;
        }

        /* ============================================
           SHARED — CORE VALUES LIST
        ============================================ */
        .cv-list {
            font-family: 'Georgia', 'Times New Roman', serif;
            font-size: 12.5px;
            color: #697a98;
            line-height: 1.55;
            margin-top: 4px;
        }

        .cv-list div {
            margin-bottom: 4px;
        }

        .cv-num {
            font-family: 'Georgia', 'Times New Roman', serif;
            color: #3b71e8;
            font-weight: 700;
            margin-right: 8px;
        }

        .cv-title {
            font-weight: 700;
            color: #1a233a;
        }

        /* ============================================
           SHARED — SIGNATURE LINE
        ============================================ */
        .signature-line {
            font-family: 'Georgia', 'Times New Roman', serif;
            font-size: 12.5px;
            margin-bottom: 18px;
        }

        .signature-line strong {
            font-weight: 700;
            color: #1a233a;
        }

        .signature-line span {
            color: #8fa0b8;
            margin-left: 6px;
        }

        /* ============================================
           PAGE 1 — COVER PAGE
        ============================================ */
        .cover-sheet {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 60px 45px;
            height: 100%;
        }

        .cover-logo-wrap {
            margin-bottom: 40px;
        }

        .cover-logo {
            height: 120px;
            object-fit: contain;
        }

        .cover-fy {
            font-family: emoji;
            color: #4a7be0;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 18px;
        }

        .cover-title-main {
            font-family: 'Georgia', 'Times New Roman', serif;
            font-weight: 700;
            font-size: 44px;
            color: #1a233a;
            margin-bottom: 6px;
            line-height: 1.2;
        }

        .cover-title-sub {
            font-family: 'Georgia', 'Times New Roman', serif;
            font-weight: 700;
            font-size: 44px;
            color: #4a7be0;
            margin-bottom: 35px;
            line-height: 1.2;
        }

        .cover-tagline {
            font-family: 'Georgia', 'Times New Roman', serif;
            font-style: italic;
            color: #778499;
            font-size: 15px;
            max-width: 520px;
            margin-bottom: 25px;
            line-height: 1.5;
        }

        .cover-quote {
            font-family: 'Georgia', 'Times New Roman', serif;
            font-weight: 700;
            font-size: 16px;
            color: #1a233a;
            margin-bottom: 45px;
        }

        .cover-meta-strip {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #778499;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 25px;
        }

        .cover-address {
            font-family: 'Georgia', 'Times New Roman', serif;
            font-size: 12px;
            color: #778499;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .cover-approval {
            font-family: 'Georgia', 'Times New Roman', serif;
            font-size: 14px;
            color: #778499;
        }

        .cover-approval strong {
            color: #1a233a;
            font-weight: 700;
        }

        /* ============================================
           PAGE 4 — WELCOME BANNER
        ============================================ */
        .welcome-banner {
            background-color: #0d1e36;
            padding: 14px 22px;
            margin-bottom: 16px;
            flex-shrink: 0;
        }

        .welcome-badge {
            font-family: 'Montserrat', sans-serif;
            color: #5c8ae6;
            font-size: 9.5px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .welcome-title {
            font-family: 'Georgia', 'Times New Roman', serif;
            font-size: 22px;
            font-weight: 700;
            color: #ffffff;
            line-height: 1.2;
            letter-spacing: 2.2px;
        }

        /* ============================================
           PAGE 4 — SCOPED OVERRIDES (fill A4 height)
        ============================================ */
        .page4 .welcome-banner {
            padding: 14px 22px;
            margin-bottom: 16px;
        }

        .page4 .welcome-title {
            font-size: 24px;
        }

        .page4 .body-text {
            font-size: 13.5px;
            line-height: 1.65;
        }

        .page4 .body-text p {
            margin-bottom: 10px;
        }

        .page4 .signature-line {
            font-size: 13.5px;
            margin-bottom: 18px;
        }

        .page4 .section-badge {
            margin-top: 4px;
            margin-bottom: 4px;
        }

        .page4 .section-heading {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .page4 .sub-label {
            margin-bottom: 4px;
            margin-top: 2px;
        }

        .page4 .bullet-list {
            font-size: 13.5px;
            line-height: 1.6;
            margin: 4px 0 8px 0;
        }

        .page4 .bullet-list li {
            margin-bottom: 2px;
        }

        .page4 .italic-summary {
            font-size: 12.5px;
            margin-bottom: 16px;
        }

        .page4 .cv-list {
            font-size: 13.5px;
            line-height: 1.65;
            margin-top: 4px;
        }

        .page4 .cv-list div {
            margin-bottom: 4px;
        }

        /* ============================================
           PAGE 2 — SCOPED OVERRIDES (fit A4 height)
        ============================================ */
        .page2 .section-desc {
            margin-bottom: 10px;
        }

        .page2 .control-table {
            margin-bottom: 14px;
        }

        .page2 .control-table td.lbl,
        .page2 .control-table td.val {
            padding: 5px 10px;
        }

        .page2 .toc-part-block {
            margin-bottom: 10px;
        }

        .page2 .toc-part-list {
            line-height: 1.55;
        }

        /* ============================================
           PRINT
        ============================================ */
        .actions {
            position: fixed;
            right: 24px;
            bottom: 24px;
            z-index: 999;
            display: flex;
            gap: 10px;
        }

        .actions .btn-premium-add,
        .actions .btn-premium-cancel {
            border-radius: 8px;
            padding: 10px 18px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-premium-add {
            background: #4a77e5;
            color: #fff;
            border: none;
        }

        .btn-premium-cancel {
            background: #fff;
            border: 1px solid #ccc;
            color: #333;
        }

        .company-highlight {
            background: #e9f1fc;
            color: black;
            font-family: "Times New Roman", Georgia, serif;
            font-size: 19px;
            font-weight: 700;
            line-height: 1.4;
            display: inline-block;
            width: 100%;
            box-sizing: border-box;
        }

        .approval-section {
            border-top: 2px solid #d6dee8;
            padding: 9px 0 13px;
            margin-top: 10px;
            width: 100%;
        }

        .approval-content {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .label {
            font-family: "Georgia", "Times New Roman", serif;
            font-size: 14px;
            font-weight: 400;
            color: #5b6f8a;
        }

        .name {
            font-family: "Georgia", "Times New Roman", serif;
            font-size: 14px;
            font-weight: 700;
            color: #0f1f38;
        }

        .divider {
            width: 1px;
            height: 22px;
            background: #d6dee8;
            margin: 0 10px;
        }

        .effective {
            font-family: "Georgia", "Times New Roman", serif;
            font-size: 14px;
            font-weight: 400;
            color: #5b6f8a;
        }

        .weightage-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
            font-family: "Times New Roman", Georgia, serif;
            border: 1px solid #d7dee8;
        }

        .weightage-head {
            background: #16263d;
            color: #ffffff;
            font-size: 18px;
            font-weight: 700;
            text-align: left;
            padding: 10px 14px;
            border: 1px solid #d7dee8;
        }

        .weightage-right {
            width: 170px;
            text-align: right;
        }

        .weightage-item {
            font-size: 15px;
            color: #243f63;
            padding: 8px 14px;
            border: 1px solid #d7dee8;
            line-height: 1.35;
        }

        .weightage-value {
            font-size: 17px;
            font-weight: 700;
            color: #111827;
            text-align: right;
            padding: 8px 14px;
            border: 1px solid #d7dee8;
        }

        .weightage-table tbody tr {
            background: #ffffff;
        }

        .weightage-table tbody tr:hover {
            background: #f7f9fc;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 0;
            }

            html,
            body {
                width: 210mm;
                height: 100%;
                margin: 0 !important;
                padding: 0 !important;
                background: #fff !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .page-wrap {
                margin: 0 !important;
                padding: 0 !important;
                width: 210mm !important;
                height: 297mm !important;
                max-height: 297mm !important;
                page-break-after: always !important;
                break-after: page !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                box-sizing: border-box !important;
                overflow: hidden !important;
            }

            .page-wrap:last-child,
            .page-wrap:last-of-type {
                page-break-after: avoid !important;
                break-after: avoid !important;
            }

            .letter-sheet {
                box-shadow: none !important;
                width: 210mm !important;
                height: 297mm !important;
                max-height: 297mm !important;
                margin: 0 !important;
                overflow: hidden !important;
            }

            .actions {
                display: none !important;
            }
        }
    </style>
</head>

<body>

    <div class="actions no-print">
        <button onclick="window.print()" class="btn-premium-add">
            <i class="fa fa-print"></i> Print / Save PDF
        </button>
        <a href="../../index.php?view_nda" class="btn-premium-cancel">Back</a>
    </div>

    <!-- ================================================
         PAGE 1: COVER PAGE
    ================================================ -->
    <div class="page-wrap">
        <div class="letter-sheet cover-sheet">
            <div class="cover-logo-wrap">
                <img src="../../images/8dots-logo.png" alt="8Dots Logo" class="cover-logo">
            </div>

            <div class="cover-fy">
                FINANCIAL YEAR <?php echo date('Y'); ?> &ndash; <?php echo date('Y') + 1; ?>
            </div>

            <div class="cover-title-main">Employee Handbook</div>
            <div class="cover-title-sub">&amp; HR Policy Manual</div>

            <div class="cover-tagline">
                The standards, expectations, policies and procedures that apply to every member of the 8DOTS team.
            </div>

            <div class="cover-quote">&ldquo; Think Digital. Grow Digital. &rdquo;</div>

            <div class="cover-meta-strip">
                EFFECTIVE <?php echo strtoupper(date('d F Y', strtotime($start_date))); ?> &nbsp;&nbsp;&nbsp;&nbsp; VERSION 1.0 &nbsp;&nbsp;&nbsp;&nbsp; INTERNAL CONFIDENTIAL
            </div>

            <div class="cover-address">
                516, Shivam Trade Centre (STC), SP Ring Road, Bopal&ndash;Ambali, Ahmedabad, Gujarat 380058<br>
                info@8dots.in &nbsp;&nbsp; www.8dots.in
            </div>

            <div class="cover-approval">
                Approved by <strong>Kamal Parmar &mdash; Director, 8DOTS</strong>
            </div>
        </div>
    </div>

    <!-- ================================================
         PAGE 2: DOCUMENT CONTROL & TOC (Parts A–C)
    ================================================ -->
    <div class="page-wrap">
        <div class="letter-sheet">
            <div class="page-inner page2">
                <!-- Header -->
                <div class="page-header">
                    <span>8DOTS &nbsp; Employee Handbook &amp; HR Policy Manual</span>
                    <span>FY <?php echo date('Y'); ?>&ndash;<?php echo date('Y') + 1; ?></span>
                </div>

                <!-- Document Control -->
                <div class="section-badge">DOCUMENT CONTROL</div>
                <div class="section-heading">Confidential Document</div>
                <p class="section-desc">
                    This handbook contains confidential and proprietary information of 8DOTS, intended solely for employees, consultants, trainees and authorized representatives of the company. Unauthorized copying, sharing, distribution, reproduction or disclosure of any part of this handbook is strictly prohibited.
                </p>

                <table class="control-table">
                    <tr>
                        <td class="lbl">Document Name</td>
                        <td class="val">Employee Handbook &amp; HR Policy Manual</td>
                    </tr>
                    <tr>
                        <td class="lbl">Version</td>
                        <td class="val">1.0</td>
                    </tr>
                    <tr>
                        <td class="lbl">Effective Date</td>
                        <td class="val"><?php echo date('d F Y', strtotime($start_date)); ?></td>
                    </tr>
                    <tr>
                        <td class="lbl">Review Date</td>
                        <td class="val"><?php echo date('d F Y', strtotime('+1 year -1 day', strtotime($start_date))); ?></td>
                    </tr>
                    <tr>
                        <td class="lbl">Owner</td>
                        <td class="val">Management</td>
                    </tr>
                    <tr>
                        <td class="lbl">Approved By</td>
                        <td class="val">Kamal Parmar &mdash; Director</td>
                    </tr>
                    <tr>
                        <td class="lbl">Classification</td>
                        <td class="val">Internal Confidential</td>
                    </tr>
                </table>

                <!-- Contents -->
                <div class="section-badge">CONTENTS</div>
                <div class="section-heading">What's Inside</div>

                <div class="toc-part-block">
                    <div class="toc-part-header">INTRODUCTION</div>
                    <div class="toc-part-list">
                        <div>Message from Management</div>
                        <div>Vision &amp; Mission</div>
                        <div>Core Values</div>
                        <div>Company Culture</div>
                    </div>
                </div>

                <div class="toc-part-block">
                    <div class="toc-part-header">PART A &middot; EMPLOYMENT &amp; TIME</div>
                    <div class="toc-part-list">
                        <div><span class="toc-num">01</span>Management Rights &amp; Policy Amendment</div>
                        <div><span class="toc-num">02</span>Employment Classification</div>
                        <div><span class="toc-num">03</span>Probation Policy</div>
                        <div><span class="toc-num">04</span>Working Hours</div>
                        <div><span class="toc-num">05</span>Alternate Saturday Working</div>
                        <div><span class="toc-num">06</span>Attendance &amp; Punctuality</div>
                    </div>
                </div>

                <div class="toc-part-block">
                    <div class="toc-part-header">PART B &middot; LEAVE</div>
                    <div class="toc-part-list">
                        <div><span class="toc-num">07</span>Leave Policy</div>
                        <div><span class="toc-num">08</span>Sandwich Leave Policy</div>
                    </div>
                </div>

                <div class="toc-part-block" style="margin-bottom:0;">
                    <div class="toc-part-header">PART C &middot; CONDUCT &amp; WORKPLACE USE</div>
                    <div class="toc-part-list">
                        <div><span class="toc-num">09</span>Mobile Phone Policy</div>
                        <div><span class="toc-num">10</span>Internet &amp; Digital Usage</div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="page-footer">
                <span>Confidential &nbsp;&nbsp; Internal Use Only &nbsp;&nbsp; Version 1.0</span>
                <span>Page 2</span>
            </div>
        </div>
    </div>

    <!-- ================================================
         PAGE 3: TOC continued (Parts C–G)
    ================================================ -->
    <div class="page-wrap">
        <div class="letter-sheet">
            <div class="page-inner">
                <!-- Header -->
                <div class="page-header">
                    <span>8DOTS &nbsp; Employee Handbook &amp; HR Policy Manual</span>
                    <span>FY <?php echo date('Y'); ?>&ndash;<?php echo date('Y') + 1; ?></span>
                </div>

                <div class="toc-part-block">
                    <div class="toc-part-list">
                        <div><span class="toc-num">11</span>Work From Home</div>
                        <div><span class="toc-num">12</span>Professional Conduct</div>
                    </div>
                </div>

                <div class="toc-part-block">
                    <div class="toc-part-header">PART D &middot; PERFORMANCE, GROWTH &amp; REWARDS</div>
                    <div class="toc-part-list">
                        <div><span class="toc-num">13</span>Employee Grading System</div>
                        <div><span class="toc-num">14</span>Monthly Self-Evaluation</div>
                        <div><span class="toc-num">15</span>Training &amp; Development</div>
                        <div><span class="toc-num">16</span>Performance Management</div>
                        <div><span class="toc-num">17</span>Salary Increment Point System</div>
                    </div>
                </div>

                <div class="toc-part-block">
                    <div class="toc-part-header">PART E &middot; COMMUNICATION, CONFIDENTIALITY &amp; SECURITY</div>
                    <div class="toc-part-list">
                        <div><span class="toc-num">18</span>Client Communication</div>
                        <div><span class="toc-num">19</span>Confidentiality</div>
                        <div><span class="toc-num">20</span>NDA &amp; Data Protection</div>
                        <div><span class="toc-num">21</span>IT Security</div>
                        <div><span class="toc-num">22</span>AI Usage Policy</div>
                        <div><span class="toc-num">23</span>Company Assets</div>
                        <div><span class="toc-num">24</span>CCTV &amp; Monitoring</div>
                    </div>
                </div>

                <div class="toc-part-block">
                    <div class="toc-part-header">PART F &middot; INTEGRITY &amp; ONLINE CONDUCT</div>
                    <div class="toc-part-list">
                        <div><span class="toc-num">25</span>Moonlighting Policy</div>
                        <div><span class="toc-num">26</span>Social Media Policy</div>
                        <div><span class="toc-num">27</span>Disciplinary Action</div>
                    </div>
                </div>

                <div class="toc-part-block" style="margin-bottom:0;">
                    <div class="toc-part-header">PART G &middot; SEPARATION &amp; FINAL AUTHORITY</div>
                    <div class="toc-part-list">
                        <div><span class="toc-num">28</span>Resignation &amp; Notice Period</div>
                        <div><span class="toc-num">29</span>Bond Agreement &amp; Security Cheque</div>
                        <div><span class="toc-num">30</span>Full &amp; Final Settlement</div>
                        <div><span class="toc-num">31</span>Termination Policy</div>
                        <div><span class="toc-num">32</span>Employee Code of Ethics</div>
                        <div><span class="toc-num">33</span>Final Authority</div>
                        <div>Employee Declaration &amp; Acknowledgement</div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="page-footer">
                <span>Confidential &nbsp;&nbsp; Internal Use Only &nbsp;&nbsp; Version 1.0</span>
                <span>Page 3</span>
            </div>
        </div>
    </div>

    <!-- ================================================
         PAGE 4: WELCOME MESSAGE, VISION & CORE VALUES
    ================================================ -->
    <div class="page-wrap">
        <div class="letter-sheet">
            <div class="page-inner page4">
                <!-- Header -->
                <div class="page-header">
                    <span>8DOTS &nbsp; Employee Handbook &amp; HR Policy Manual</span>
                    <span>FY <?php echo date('Y'); ?>&ndash;<?php echo date('Y') + 1; ?></span>
                </div>

                <!-- Welcome Banner -->
                <div class="welcome-banner">
                    <div class="welcome-badge">WELCOME</div>
                    <div class="welcome-title">A Message from Management</div>
                </div>

                <!-- Message -->
                <div class="body-text" style="margin-bottom:14px;">
                    <p>Welcome to 8DOTS.</p>
                    <p>Our success is built on discipline, commitment, continuous learning, teamwork, innovation and customer satisfaction. This handbook outlines the standards, expectations, policies and procedures applicable to every employee of the organization.</p>
                    <p style="margin-bottom:0;">Every employee is expected to read, understand and follow this handbook.</p>
                </div>

                <!-- Signature -->
                <div class="signature-line">
                    <strong>Kamal Parmar</strong> <span>Director, 8DOTS</span>
                </div>

                <!-- Vision & Mission -->
                <div class="section-badge">WHO WE ARE</div>
                <div class="section-heading">Vision &amp; Mission</div>

                <div class="sub-label">OUR VISION</div>
                <p class="body-text" style="margin-bottom:10px;">
                    To become a globally trusted digital growth partner by delivering innovative technology, marketing, branding and business solutions that create measurable success for our clients.
                </p>

                <div class="sub-label">OUR MISSION</div>
                <p class="body-text" style="margin-bottom:4px;">To help businesses grow through:</p>
                <ul class="bullet-list">
                    <li>Digital Marketing</li>
                    <li>Search Engine Optimization (SEO)</li>
                    <li>Google &amp; Meta Advertising</li>
                    <li>Website Development</li>
                    <li>Mobile Application Development</li>
                    <li>Branding &amp; Creative Solutions</li>
                    <li>Business Automation</li>
                </ul>

                <div class="italic-summary">
                    ....while maintaining excellence, integrity, innovation and long-term client relationships.
                </div>

                <!-- Core Values -->
                <div class="section-badge">WHAT GUIDES US</div>
                <div class="section-heading">Core Values</div>

                <div class="cv-list">
                    <div><span class="cv-num">01</span><span class="cv-title">Commitment</span> &mdash; We deliver what we promise.</div>
                    <div><span class="cv-num">02</span><span class="cv-title">Discipline</span> &mdash; Consistency creates success.</div>
                    <div><span class="cv-num">03</span><span class="cv-title">Accountability</span> &mdash; Own your responsibilities.</div>
                    <div><span class="cv-num">04</span><span class="cv-title">Learning</span> &mdash; Never stop improving.</div>
                    <div><span class="cv-num">05</span><span class="cv-title">Innovation</span> &mdash; Think differently and improve continuously.</div>
                    <div><span class="cv-num">06</span><span class="cv-title">Teamwork</span> &mdash; Together everyone achieves more.</div>
                    <div><span class="cv-num">07</span><span class="cv-title">Client Success</span> &mdash; Client growth is our growth.</div>
                </div>
            </div>

            <!-- Footer -->
            <div class="page-footer">
                <span>Confidential &nbsp;&nbsp; Internal Use Only &nbsp;&nbsp; Version 1.0</span>
                <span>Page 4</span>
            </div>
        </div>
    </div>

    <!-- ================================================
         PAGE 5: WELCOME MESSAGE, VISION & CORE VALUES
    ================================================ -->
    <div class="page-wrap">
        <div class="letter-sheet">
            <div class="page-inner page4">
                <!-- Header -->
                <div class="page-header">
                    <span>8DOTS &nbsp; Employee Handbook &amp; HR Policy Manual</span>
                    <span>FY <?php echo date('Y'); ?>&ndash;<?php echo date('Y') + 1; ?></span>
                </div>
                <div class="cv-list">
                    <div><span class="cv-num">08</span><span class="cv-title">Integrity</span> &mdash; Do the right thing even when nobody is watching.</div>
                </div>
                <div class="section-badge">HOW WE WORK</div>
                <div class="section-heading">Company Culture</div>
                <div class="company-highlight">“Performance Matters. Attitude Matters More.”</div>
                </br>
                <div class="sub-label">EVERY EMPLOYEE IS EXPECTED TO</div>
                <ul class="bullet-list">
                    <li>Be punctual.</li>
                    <li>Be responsible</li>
                    <li>Learn continuously.</li>
                    <li>Respect colleagues.</li>
                    <li>Respect company resources.</li>
                    <li>Support company growth.</li>
                    <li>Represent the company professionally.</li>
                </ul>
                <div class="sub-label">THE COMPANY REWARDS</div>
                <ul class="bullet-list">
                    <li>Ownership</li>
                    <li>Commitment</li>
                    <li>Innovation</li>
                    <li>Learning</li>
                    <li>Discipline</li>
                    <li>Results</li>
                </ul>

                <!-- Footer -->
                <div class="page-footer">
                    <span>Confidential &nbsp;&nbsp; Internal Use Only &nbsp;&nbsp; Version 1.0</span>
                    <span>Page 5</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ================================================
         PAGE 6: WELCOME MESSAGE, VISION & CORE VALUES
    ================================================ -->
    <div class="page-wrap">
        <div class="letter-sheet">
            <div class="page-inner page4">
                <!-- Header -->
                <div class="page-header">
                    <span>8DOTS &nbsp; Employee Handbook &amp; HR Policy Manual</span>
                    <span>FY <?php echo date('Y'); ?>&ndash;<?php echo date('Y') + 1; ?></span>
                </div>

                <!-- Welcome Banner -->
                <div class="welcome-banner">
                    <div class="welcome-badge">PART A</div>
                    <div class="welcome-title">Employment & Time</div>
                </div>
                <div class="toc-part-header">
                    <span class="toc-number">01 </span><span class="toc-title">Management Rights & Policy Amendment</span>
                </div>
                <p class="body-text">
                    The Company reserves the right to modify, revise, amend, suspend, replace or withdraw any policy, rule, benefit, process, working condition, compensation structure or procedure at any time without prior notice.
                    </br>
                    The final authority regarding all employment-related matters shall remain solely with the
                    Company Management. Management decisions shall be final and binding.
                </p>
                </br>
                <!-- <div class="toc-part-header">02&nbsp;&nbsp;Employment Classification</div> -->
                <div class="toc-part-header">
                    <span class="toc-number">02</span>
                    <span class="toc-title">Employment Classification</span>
                </div>
                <p class="body-text">Employees may be classified as:</br>
                <ul class="bullet-list">
                    <li>Probationary Employee</li>
                    <li>Confirmed Employee</li>
                    <li>Intern / Trainee</li>
                    <li>Contract Employee</li>
                    <li>Consultant / Freelancer</li>
                </ul>
                </p>
                <p class="body-text">
                    The Company reserves the right to determine employee classification.
                </p>
                </br>
                <div class="toc-part-header">
                    <span class="toc-number">03</span><span class="toc-title">Probation Policy</span>
                </div>
                <p class="body-text">
                    Probation duration: minimum 3 months, maximum 6 months.
                    </br>
                    Probation may be extended based on:
                <ul class="bullet-list">
                    <li>Performance</li>
                    <li>Attendance</li>
                    <li>Discipline</li>
                    <li>Learning capability</li>
                    <li>Team integration</li>
                </ul>
                </p>
                <p class="body-text">
                    At the end of probation, the Company may confirm employment, extend probation, or
                    terminate employment — without obligation to provide permanent employment.
                </p>
                </br>

                <div class="toc-part-header">
                    <span class="toc-number">04</span><span class="toc-title">Working Hours</span>
                </div>
                <p class="body-text">
                    The Company follows an 8-hour work schedule.
                <ul class="bullet-list">
                    <li> Shift A — 09:30 to 18:30 (grace up to 09:45)</li>
                    <li>Shift B — 10:00 to 19:00 (grace up to 10:15)</li>
                    <li>Shift C (Support team only) — varies by client requirement and management approval</li>
                </ul>
                </p>
                <!-- Footer -->
                <div class="page-footer">
                    <span>Confidential &nbsp;&nbsp; Internal Use Only &nbsp;&nbsp; Version 1.0</span>
                    <span>Page 6</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ================================================
         PAGE 7: WELCOME MESSAGE, VISION & CORE VALUES
    ================================================ -->
    <div class="page-wrap">
        <div class="letter-sheet">
            <div class="page-inner page4">
                <!-- Header -->
                <div class="page-header">
                    <span>8DOTS &nbsp; Employee Handbook &amp; HR Policy Manual</span>
                    <span>FY <?php echo date('Y'); ?>&ndash;<?php echo date('Y') + 1; ?></span>
                </div>

                <div class="toc-part-header">
                    <span class="toc-number">05</span><span class="toc-title">Alternate Saturday Working</span>
                </div>
                <p class="body-text">
                    Effective from 01 July 2026:
                <ul class="bullet-list">
                    <li>Employees shall work on alternate Saturdays.</li>
                    <li>Working Saturdays shall be communicated through the official company calendar.</li>
                    <li>Attendance on working Saturdays is mandatory.</li>
                </ul>
                </p>
                </br>
                <div class="toc-part-header">
                    <span class="toc-number">06</span><span class="toc-title">Attendance & Punctuality</span>
                </div>
                <p class="body-text">Employees must:</br>
                <ul class="bullet-list">
                    <li>Report to work on time.</li>
                    <li>Mark attendance accurately.</li>
                    <li>Maintain regular attendance.</li>
                </ul>
                </p>
                <p class="body-text">
                    Repeated late coming, absenteeism or attendance manipulation may result in disciplinary
                    action.
                </p>
                <!-- Footer -->
                <div class="page-footer">
                    <span>Confidential &nbsp;&nbsp; Internal Use Only &nbsp;&nbsp; Version 1.0</span>
                    <span>Page 7</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ================================================
         PAGE 8: WELCOME MESSAGE, VISION & CORE VALUES
    ================================================ -->
    <div class="page-wrap">
        <div class="letter-sheet">
            <div class="page-inner page4">
                <!-- Header -->
                <div class="page-header">
                    <span>8DOTS &nbsp; Employee Handbook &amp; HR Policy Manual</span>
                    <span>FY <?php echo date('Y'); ?>&ndash;<?php echo date('Y') + 1; ?></span>
                </div>

                <!-- Welcome Banner -->
                <div class="welcome-banner">
                    <div class="welcome-badge">PART B</div>
                    <div class="welcome-title">Leave</div>
                </div>
                <div class="toc-part-header">
                    <span class="toc-number">07</span><span class="toc-title">Leave Policy</span>
                </div>
                <p class="body-text">
                    Employees receive 1 paid leave per month, up to a maximum of 12 paid leaves per year.
                    </br>
                <div class="sub-label">LEAVE APPROVAL</div>
                <p class="body-text">Every leave request must:
                <ul class="bullet-list">
                    <li>Receive manager approval.</li>
                    <li>Be sent by email to info@8dots.in.</li>
                    <li>Receive written confirmation</li>
                </ul>
                </p>
                <div class="sub-label">UNAUTHORIZED LEAVE</div>
                <p class="body-text">
                <ul class="bullet-list">
                    <li>May be counted as a double leave deduction.</li>
                    <li>Will be considered a red flag in employee records.</li>
                </ul>
                </p>
                <div class="sub-label">EMERGENCY LEAVE</div>
                <p class="body-text">
                    Management may approve emergency leave on a case-by-case basis. Supporting documents
                    may be required.
                </p>
                </br>
                <div class="toc-part-header">
                    <span class="toc-number">08</span><span class="toc-title">Sandwich Leave Policy</span>
                </div>
                <p class="body-text">
                    If leave is taken immediately before or after a weekly off, public holiday or company holiday, the
                    intervening holiday period may be counted as leave. Management shall decide applicability.
                </p>
                <!-- Footer -->
                <div class="page-footer">
                    <span>Confidential &nbsp;&nbsp; Internal Use Only &nbsp;&nbsp; Version 1.0</span>
                    <span>Page 8</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ================================================
         PAGE 9: WELCOME MESSAGE, VISION & CORE VALUES
    ================================================ -->
    <div class="page-wrap">
        <div class="letter-sheet">
            <div class="page-inner page4">
                <!-- Header -->
                <div class="page-header">
                    <span>8DOTS &nbsp; Employee Handbook &amp; HR Policy Manual</span>
                    <span>FY <?php echo date('Y'); ?>&ndash;<?php echo date('Y') + 1; ?></span>
                </div>

                <!-- Welcome Banner -->
                <div class="welcome-banner">
                    <div class="welcome-badge">PART C</div>
                    <div class="welcome-title">Conduct & Workplace Use</div>
                </div>
                <div class="toc-part-header">
                    <span class="toc-number">09</span><span class="toc-title">Mobile Phone Policy</span>
                </div>
                <p class="body-text">
                    To maintain workplace productivity, employees shall not:
                <ul class="bullet-list">
                    <li>Browse Instagram, Facebook or YouTube during work hours.</li>
                    <li>Use social media excessively.</li>
                    <li> Spend washroom breaks on personal social media activity.</li>
                </ul>
                </p>
                <p class="body-text">
                    Mobile phones should remain on silent mode. Use is permitted only for client communication,
                    business work and family emergencies.
                </p>
                </br>
                <div class="toc-part-header">
                    <span class="toc-number">10</span>
                    <span class="toc-title">Internet & Digital Usage</span>
                </div>
                <p class="body-text">
                    Company internet resources are intended for business purposes. Employees shall not:
                <ul class="bullet-list">
                    <li>Access illegal websites.</li>
                    <li>Download unauthorized software.</li>
                    <li>Stream entertainment content during work hours.</li>
                    <li> Access inappropriate content.</li>
                </ul>
                </p>
                <p class="body-text">
                    The Company may monitor internet activity.
                </p>
                </br>
                <div class="toc-part-header">
                    <span class="toc-number">11</span>
                    <span class="toc-title">Work from Home</span>
                </div>
                <p class="body-text">
                    8DOTS follows a Work from Office culture. Work From Home is generally not permitted. Any
                    exception requires direct written approval from Management.
                </p>
                </br>
                <div class="toc-part-header">
                    <span class="toc-number">12</span>
                    <span class="toc-title">Professional Conduct</span>
                </div>
                <p class="body-text">
                    Employees shall: </br>
                <ul class="bullet-list">
                    <li>Maintain professionalism.</li>
                    <li>Respect colleagues and clients.</li>
                    <li>Follow company hierarchy.</li>
                    <li> Communicate respectfully</li>
                    <li> Maintain workplace discipline.</li>
                </ul>
                </p>
                <p class="body-text">
                    Abusive language, harassment, discrimination or misconduct shall not be tolerated.
                </p>

                <!-- Footer -->
                <div class="page-footer">
                    <span>Confidential &nbsp;&nbsp; Internal Use Only &nbsp;&nbsp; Version 1.0</span>
                    <span>Page 9</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ================================================
         PAGE 10: WORKPLACE POLICIES
    ================================================ -->
    <div class="page-wrap">
        <div class="letter-sheet">
            <div class="page-inner page4">
                <!-- Header -->
                <div class="page-header">
                    <span>8DOTS &nbsp; Employee Handbook &amp; HR Policy Manual</span>
                    <span>FY <?php echo date('Y'); ?>&ndash;<?php echo date('Y') + 1; ?></span>
                </div>

                <!-- Welcome Banner -->
                <div class="welcome-banner">
                    <div class="welcome-badge">PART D</div>
                    <div class="welcome-title">Performance, Growth & Rewards</div>
                </div>
                <div class="toc-part-header">
                    <span class="toc-number">13</span>
                    <span class="toc-title">Employee Grading System</span>
                </div>
                <p class="body-text">
                    Employees are evaluated against the following weighted criteria:
                </p>
                </br>
                <div class="toc-part-header">
                    <span class="toc-number">14</span>
                    <span class="toc-title">Performance Review</span>
                </div>
                <p class="body-text">
                    Performance is reviewed twice a year through formal appraisal meetings. These reviews assess:
                </p>
                </br>
                <table class="weightage-table">
                    <thead>
                        <tr>
                            <th class="weightage-head">Criteria</th>
                            <th class="weightage-head weightage-right">Weight</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td class="weightage-item">Performance &amp; Productivity</td>
                            <td class="weightage-value">25%</td>
                        </tr>
                        <tr>
                            <td class="weightage-item">Commitment &amp; Ownership</td>
                            <td class="weightage-value">20%</td>
                        </tr>
                        <tr>
                            <td class="weightage-item">Attendance &amp; Regularity</td>
                            <td class="weightage-value">15%</td>
                        </tr>
                        <tr>
                            <td class="weightage-item">Behaviour &amp; Teamwork</td>
                            <td class="weightage-value">10%</td>
                        </tr>
                        <tr>
                            <td class="weightage-item">Attitude &amp; Professionalism</td>
                            <td class="weightage-value">10%</td>
                        </tr>
                        <tr>
                            <td class="weightage-item">Learning &amp; Development</td>
                            <td class="weightage-value">10%</td>
                        </tr>
                        <tr>
                            <td class="weightage-item">Research &amp; Development</td>
                            <td class="weightage-value">10%</td>
                        </tr>
                    </tbody>
                </table>
                <br>
                <div class="sub-label">GRADES</div>
                <p class="body-text">
                    A+ = 90-100 . A = 80-89 . B = 70-79 . C = 60-69 . D = Below 60
                </p>
                <br>
                <div class="toc-part-header">
                    <span class="toc-number">15</span>
                    <span class="toc-title">Monthly Self-Evaluation</span>
                </div>
                <p class="body-text">
                    Every employee shall submit a Monthly Self-Evaluation Report. The report must include:
                </p>
                <ul class="bullet-list">
                    <li>Completed work</li>
                    <li>Achievements</li>
                    <li>New learning</li>
                    <li>Certifications</li>
                    <li>Challenges faced</li>
                    <li>Solutions implemented</li>
                    <li>Goals for next month</li>
                </ul>
                <p class="body-text">
                    Reports shall be submitted through email.
                </p>
                <br>
                <div class="toc-part-header">
                    <span class="toc-number">16</span>
                    <span class="toc-title">Training & Development</span>
                </div>
                <p class="body-text">
                    Every employee is responsible for self-development. Employees are expected to:
                </p>
                <!-- Footer -->
                <div class="page-footer">
                    <span>Confidential &nbsp;&nbsp; Internal Use Only &nbsp;&nbsp; Version 1.0</span>
                    <span>Page 10</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ================================================
         PAGE 11: WORKPLACE POLICIES
    ================================================ -->
    <div class="page-wrap">
        <div class="letter-sheet">
            <div class="page-inner page4">
                <!-- Header -->
                <div class="page-header">
                    <span>8DOTS &nbsp; Employee Handbook &amp; HR Policy Manual</span>
                    <span>FY <?php echo date('Y'); ?>&ndash;<?php echo date('Y') + 1; ?></span>
                </div>

                <ul class="bullet-list">
                    <li>Learn new technologies.</li>
                    <li>Improve professional skills.</li>
                    <li>Attend training sessions.</li>
                    <li>Participate in R&D initiatives.</li>
                </ul>
                <p class="body-text">
                    Continuous learning is mandatory.
                </p>
                </br>
                <div class="toc-part-header">
                    <span class="toc-number">17</span>
                    <span class="toc-title">Performance Management</span>
                </div>
                <p class="body-text">
                    Performance reviews shall be conducted regularly. Evaluation includes attendance, behaviour,
                    commitment, ownership, productivity, learning, teamwork, innovation and client satisfaction.
                    </br>
                    Management reserves the right to place employees under a Performance Improvement Plan
                    (PIP).
                </p>
                </br>
                <div class="toc-part-header">
                    <span class="toc-number">18</span>
                    <span class="toc-title">Salary Increment Point System</span>
                </div>
                <p class="body-text">
                    Increments are calculated on a 100-point scale across the following parameters:
                </p>
                </br>
                <table class="weightage-table">
                    <thead>
                        <tr>
                            <th class="weightage-head">Parameter</th>
                            <th class="weightage-head weightage-right">Points</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td class="weightage-item">Attendance &amp; Punctuality</td>
                            <td class="weightage-value">20</td>
                        </tr>
                        <tr>
                            <td class="weightage-item">Performance</td>
                            <td class="weightage-value">25</td>
                        </tr>
                        <tr>
                            <td class="weightage-item">Ownership</td>
                            <td class="weightage-value">15</td>
                        </tr>
                        <tr>
                            <td class="weightage-item">Learning &amp; Certification</td>
                            <td class="weightage-value">10</td>
                        </tr>
                        <tr>
                            <td class="weightage-item">Behaviour</td>
                            <td class="weightage-value">10</td>
                        </tr>
                        <tr>
                            <td class="weightage-item">Innovation &amp; R&amp;D</td>
                            <td class="weightage-value">10</td>
                        </tr>
                        <tr>
                            <td class="weightage-item">Client Feedback</td>
                            <td class="weightage-value">5</td>
                        </tr>
                        <tr>
                            <td class="weightage-item">Process Improvement</td>
                            <td class="weightage-value">5</td>
                        </tr>
                    </tbody>
                </table>
                <br>
                <div class="sub-label">INCREMENT MATRIX</div>
                <table class="control-table">
                    <tr>
                        <td class="lbl">95-100</td>
                        <td class="val" style="color:#3F6FE5;    text-align: right;">20% - 25%</td>
                    </tr>
                    <tr>
                        <td class="lbl">90-94</td>
                        <td class="val" style="color:#3F6FE5;    text-align: right;">15% - 20%</td>
                    </tr>
                    <tr>
                        <td class="lbl">80-89</td>
                        <td class="val" style="text-align: right;">10% - 15%</td>
                    </tr>
                    <tr>
                        <td class="lbl">70-79</td>
                        <td class="val" style="text-align: right;">5% - 10%</td>
                    </tr>
                    <tr>
                        <td class="lbl">60-69</td>
                        <td class="val" style="text-align: right;">0% - 5%</td>
                    </tr>
                    <tr>
                        <td class="lbl">Below 60</td>
                        <td class="val" style="text-align: right;">No Increment</td>
                    </tr>
                </table>
                </br>
                <p class="body-text">
                    Final decision remains with Management. </p>
                <!-- Footer -->
                <div class="page-footer">
                    <span>Confidential &nbsp;&nbsp; Internal Use Only &nbsp;&nbsp; Version 1.0</span>
                    <span>Page 11</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ================================================
         PAGE 12: WORKPLACE POLICIES
    ================================================ -->
    <div class="page-wrap">
        <div class="letter-sheet">
            <div class="page-inner page4">
                <!-- Header -->
                <div class="page-header">
                    <span>8DOTS &nbsp; Employee Handbook &amp; HR Policy Manual</span>
                    <span>FY <?php echo date('Y'); ?>&ndash;<?php echo date('Y') + 1; ?></span>
                </div>

                <!-- Welcome Banner -->
                <div class="welcome-banner">
                    <div class="welcome-badge">PART E</div>
                    <div class="welcome-title">Communication, Confidentiality & Security</div>
                </div>

                <div class="toc-part-header">
                    <span class="toc-number">19</span>
                    <span class="toc-title">Client Communication</span>
                </div>
                <p class="body-text">
                    Employees shall:
                <ul class="bullet-list">
                    <li>Communicate professionally.</li>
                    <li>Avoid making unauthorized commitments.</li>
                    <li>Follow approved communication channels.</li>
                    <li>Escalate issues appropriately.</li>
                </ul>
                </p>
                <p class="body-text">
                    Only authorized personnel may provide official commitments on behalf of the Company.
                </p>
                </br>
                <div class="toc-part-header">
                    <span class="toc-number">20</span>
                    <span class="toc-title">Confidentiality</span>
                </div>
                <p class="body-text">
                    Employees shall not disclose client information, project details, financial information, internal
                    documents, pricing information or business strategies. This obligation continues after
                    employment ends.
                </p>
                </br>
                <div class="toc-part-header">
                    <span class="toc-number">21</span>
                    <span class="toc-title">NDA & Data Protection</span>
                </div>
                <p class="body-text">
                    All work performed during employment remains the property of 8DOTS. Employees shall not
                    share client databases, source code, credentials or confidential documents. Unauthorized
                    disclosure may result in legal action.
                </p>
                </br>
                <div class="toc-part-header">
                    <span class="toc-number">22</span>
                    <span class="toc-title">IT Security</span>
                </div>
                <p class="body-text">
                    Employees must use strong passwords, protect company systems and follow cybersecurity
                    practices.</br>
                    Employees must not share passwords, install unauthorized software or disable security systems.
                </p>
                </br>

                <div class="toc-part-header">
                    <span class="toc-number">23</span>
                    <span class="toc-title">AI Usage Policy</span>
                </div>
                <p class="body-text">
                    Approved AI tools may be used only for productivity enhancement — for example ChatGPT,
                    Gemini, Claude and GitHub Copilot.
                    </br>
                    Employees shall not upload client confidential information, source code without authorization,
                    financial records or sensitive company documents. Misuse of AI tools shall be treated as serious
                    misconduct.
                </p>
                </br>
                <div class="toc-part-header">
                    <span class="toc-number">24</span>
                    <span class="toc-title">Company Assets</span>
                </div>
                <p class="body-text">
                    Employees are responsible for laptops, desktops, monitors, software licenses, company
                    documents and access credentials. Any loss or damage due to negligence may be recovered from
                    the employee.
                </p>
                </br>
                <div class="toc-part-header">
                    <span class="toc-number">25</span>
                    <span class="toc-title">CCTV & Monitoring</span>
                </div>
                <p class="body-text">
                    For security and operational purposes, the Company may monitor office premises, attendance
                    systems, internet usage, official communications and company devices. Employees
                    acknowledge such monitoring.
                </p>

                <!-- Footer -->
                <div class="page-footer">
                    <span>Confidential &nbsp;&nbsp; Internal Use Only &nbsp;&nbsp; Version 1.0</span>
                    <span>Page 12</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ================================================
         PAGE 13: WORKPLACE POLICIES
    ================================================ -->
    <div class="page-wrap">
        <div class="letter-sheet">
            <div class="page-inner page4">
                <!-- Header -->
                <div class="page-header">
                    <span>8DOTS &nbsp; Employee Handbook &amp; HR Policy Manual</span>
                    <span>FY <?php echo date('Y'); ?>&ndash;<?php echo date('Y') + 1; ?></span>
                </div>

                <!-- Welcome Banner -->
                <div class="welcome-banner">
                    <div class="welcome-badge">PART F</div>
                    <div class="welcome-title">Integrity & Online Conduct</div>
                </div>
                <div class="toc-part-header">
                    <span class="toc-number">26</span>
                    <span class="toc-title">Moonlighting Policy</span>
                </div>
                <p class="body-text">
                    Without prior written approval, employees shall not work for competitors, operate conflicting
                    businesses, or accept freelance assignments that interfere with company work.
                </p>
                </br>
                <div class="toc-part-header">
                    <span class="toc-number">27</span>
                    <span class="toc-title">Social Media Policy</span>
                </div>
                <p class="body-text">
                    Employees shall not damage the Company's reputation online, share confidential information,
                    or misrepresent company positions. Professional conduct is expected on all social media
                    platforms. </p>
                </br>
                <div class="toc-part-header">
                    <span class="toc-number">28</span>
                    <span class="toc-title">Disciplinary Action</span>
                </div>
                <p class="body-text">
                    Depending upon severity, disciplinary action may escalate through: Verbal Warning → Written
                    Warning → Final Warning → PIP → Suspension → Termination.
                </p>
                <!-- Footer -->
                <div class="page-footer">
                    <span>Confidential &nbsp;&nbsp; Internal Use Only &nbsp;&nbsp; Version 1.0</span>
                    <span>Page 13</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ================================================
         PAGE 14: WORKPLACE POLICIES
    ================================================ -->
    <div class="page-wrap">
        <div class="letter-sheet">
            <div class="page-inner page4">
                <!-- Header -->
                <div class="page-header">
                    <span>8DOTS &nbsp; Employee Handbook &amp; HR Policy Manual</span>
                    <span>FY <?php echo date('Y'); ?>&ndash;<?php echo date('Y') + 1; ?></span>
                </div>

                <!-- Welcome Banner -->
                <div class="welcome-banner">
                    <div class="welcome-badge">PART G</div>
                    <div class="welcome-title">Separation & Final Authority</div>
                </div>
                <div class="toc-part-header">
                    <span class="toc-number">29</span>
                    <span class="toc-title">Resignation & Notice Period</span>
                </div>
                <p class="body-text">
                    Minimum notice period: 60 days. Resigning employees must:
                <ul class="bullet-list">
                    <li>Submit a written resignation.</li>
                    <li>Complete handover.</li>
                    <li>Return company assets.</li>
                    <li>Complete pending assignments.</li>
                </ul>
                </p>
                </br>

                <div class="toc-part-header">
                    <span class="toc-number">30</span>
                    <span class="toc-title">Bond Agreement & Security Cheque</span>
                </div>
                <p class="body-text">
                    Where an employee has signed a Bond or Service Agreement with the Company, the employee
                    shall remain bound by its terms for the agreed period.
                <ul class="bullet-list">
                    <li>At the time of signing the bond or joining, the employee may be required to submit a security
                        cheque to the Company.
                    </li>
                    <li>If the employee breaches the Bond Agreement, the Company reserves the right to deposit or
                        present the said security cheque.</li>
                    <li>f the cheque is dishonoured, bounced or found invalid for any reason, the Company may
                        initiate legal action against the employee for breach of agreement, as permitted under
                        applicable laws.</li>
                    <li>These rights are in addition to any other remedy available to the Company under the Bond
                        Agreement or applicable law.</li>
                </ul>

                </br>
                <div class="toc-part-header">
                    <span class="toc-number">31</span>
                    <span class="toc-title">Full & Final Settlement</span>
                </div>
                <p class="body-text">
                    Settlement may include salary dues, approved reimbursements and leave adjustments. The
                    Company may deduct asset recovery, notice recovery and other approved liabilities.
                </p>
                </br>
                <div class="toc-part-header">
                    <span class="toc-number">32</span>
                    <span class="toc-title">Termination Policy</span>
                </div>
                <p class="body-text">
                    As permitted by applicable laws, the Company reserves the right to terminate employment due
                    to misconduct, poor performance, policy violations, attendance issues, data security breaches or
                    business requirements.</p>
                </br>
                <div class="toc-part-header">
                    <span class="toc-number">33</span>
                    <span class="toc-title">Employee Code of Ethics</span>
                </div>
                <p class="body-text">
                    Every employee shall act honestly, act professionally, respect others, protect confidentiality,
                    demonstrate accountability, promote teamwork and support company growth.</p>
                </br>

                <!-- Footer -->
                <div class="page-footer">
                    <span>Confidential &nbsp;&nbsp; Internal Use Only &nbsp;&nbsp; Version 1.0</span>
                    <span>Page 14</span>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!-- ================================================
         PAGE 15: WORKPLACE POLICIES
    ================================================ -->
    <div class="page-wrap">
        <div class="letter-sheet">
            <div class="page-inner page4">
                <!-- Header -->
                <div class="page-header">
                    <span>8DOTS &nbsp; Employee Handbook &amp; HR Policy Manual</span>
                    <span>FY <?php echo date('Y'); ?>&ndash;<?php echo date('Y') + 1; ?></span>
                </div>
                <div class="toc-part-header">
                    <span class="toc-number">34</span>
                    <span class="toc-title">Final Authority</span>
                </div>
                <p class="body-text">
                    The Company Management shall have final authority regarding attendance, leave, promotions,
                    salary revisions, appraisals, transfers, warnings, terminations, policy interpretation and policy
                    exceptions.</br>All management decisions shall be final and binding.
                </p>
            </div>


            <!-- Footer -->
            <div class="page-footer">
                <span>Confidential &nbsp;&nbsp; Internal Use Only &nbsp;&nbsp; Version 1.0</span>
                <span>Page 15</span>
            </div>
        </div>
    </div>
    </div>

    <!-- ================================================
         PAGE 16: WORKPLACE POLICIES
    ================================================ -->
    <div class="page-wrap">
        <div class="letter-sheet">
            <div class="page-inner page4">
                <!-- Header -->
                <div class="page-header">
                    <span>8DOTS &nbsp; Employee Handbook &amp; HR Policy Manual</span>
                    <span>FY <?php echo date('Y'); ?>&ndash;<?php echo date('Y') + 1; ?></span>
                </div>

                <!-- Welcome Banner -->
                <div class="welcome-banner">
                    <div class="welcome-badge">ACKNOWLEDGEMENT</div>
                    <div class="welcome-title">Employee Declaration</div>
                </div>
                <p class="body-text">
                    I confirm that:
                <ul class="bullet-list">
                    <li> I have received the Employee Handbook.
                    </li>
                    <li> I have read and understood all policies.</li>
                    <li>I agree to comply with all company rules.</li>
                    <li>I understand that company policies may change from time to time.</li>
                    <li>I understand that final authority remains with 8DOTS Management.</li>
                </ul>
                </p>
                </br>
                <table class="control-table">
                    <tr>
                        <td class="lbl">Employee Name</td>
                        <td class="val"><?php echo htmlspecialchars($name); ?></td>
                    </tr>
                    <tr>
                        <td class="lbl">Employee ID</td>
                        <td class="val"><?php echo htmlspecialchars($employee_id); ?></td>
                    </tr>
                    <tr>
                        <td class="lbl">Department</td>
                        <td class="val"><?php echo htmlspecialchars($department); ?></td>
                    </tr>
                    <tr>
                        <td class="lbl">Designation</td>
                        <td class="val"><?php echo htmlspecialchars($designation); ?></td>
                    </tr>
                    <tr>
                        <td class="lbl">Joining Date</td>
                        <td class="val"><?php echo htmlspecialchars($start_date); ?></td>
                    </tr>
                    <tr>
                        <td class="lbl">Date</td>
                        <td class="val"><?php echo date('d F Y'); ?></td>
                    </tr>
                </table>
                </br>
                <div class="sub-label">Employee Signature &amp; Date</div>
                <div class="sub-label">HR Representative Signature &amp; Date</div>
                <div class="approval-section">
                    <div class="approval-content">
                        <span class="label">Approved by</span>
                        <span class="name">
                            Kamal Parmar — Director, 8DOTS
                        </span>
                        <span class="divider"></span>
                        <span class="effective">
                            Effective 01 August 2026 · FY 2026-2027
                        </span>
                    </div>
                </div>
                <!-- Footer -->
                <div class="page-footer">
                    <span>Confidential &nbsp;&nbsp; Internal Use Only &nbsp;&nbsp; Version 1.0</span>
                    <span>Page 16</span>
                </div>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</body>

</html>
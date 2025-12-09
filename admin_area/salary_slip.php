<?php
// ---- Salary Slip fragment (include from index.php) ----
// Assumes: $con (mysqli connection) and session already started in index.php
// Also assumes Bootstrap + Font Awesome loaded in main layout

// ------------------ HELPERS ------------------ //
function format_money($n)
{
    return number_format((float)$n, 2);
}

function format_money_with_symbol($n, $currency_symbol)
{
    return '<span class="currency-symbol">' . $currency_symbol . '</span>' . format_money($n);
}

// Convert number to words (supports up to billions, with paise/decimal part)
function number_to_words($number)
{
    $no = floor($number);
    $decimal = round(($number - $no) * 100);
    $words = array(
        '0' => 'Zero',
        '1' => 'One',
        '2' => 'Two',
        '3' => 'Three',
        '4' => 'Four',
        '5' => 'Five',
        '6' => 'Six',
        '7' => 'Seven',
        '8' => 'Eight',
        '9' => 'Nine',
        '10' => 'Ten',
        '11' => 'Eleven',
        '12' => 'Twelve',
        '13' => 'Thirteen',
        '14' => 'Fourteen',
        '15' => 'Fifteen',
        '16' => 'Sixteen',
        '17' => 'Seventeen',
        '18' => 'Eighteen',
        '19' => 'Nineteen',
        '20' => 'Twenty',
        '30' => 'Thirty',
        '40' => 'Forty',
        '50' => 'Fifty',
        '60' => 'Sixty',
        '70' => 'Seventy',
        '80' => 'Eighty',
        '90' => 'Ninety'
    );

    if ($no == 0) {
        $result = 'Zero';
    } else {
        $result = '';
        $units = array('', 'Thousand', 'Million', 'Billion');
        $i = 0;
        while ($no > 0) {
            $chunk = $no % 1000;
            if ($chunk) {
                $hundreds = floor($chunk / 100);
                $remainder = $chunk % 100;
                $str = '';
                if ($hundreds) {
                    $str .= $words[$hundreds] . ' Hundred';
                    if ($remainder) $str .= ' and ';
                }
                if ($remainder) {
                    if ($remainder < 21) {
                        $str .= $words[$remainder];
                    } else {
                        $tens = floor($remainder / 10) * 10;
                        $ones = $remainder % 10;
                        $str .= $words[$tens];
                        if ($ones) $str .= ' ' . $words[$ones];
                    }
                }
                if (!empty($units[$i])) $str .= ' ' . $units[$i];
                $result = trim($str . ' ' . $result);
            }
            $no = floor($no / 1000);
            $i++;
        }
    }

    if ($decimal > 0) {
        $result .= ' and ' . $decimal . '/100';
    }
    return $result;
}

// Currency symbol (HTML entity keeps it ASCII-safe)
$currency_symbol = '&#8377;';

// ------------------ FETCH EMPLOYEES ------------------ //
$employees = array();
$res = mysqli_query(
    $con,
    "SELECT id, name, COALESCE(salary, '') AS salary
     FROM emp_list
     ORDER BY name ASC"
);
if ($res) {
    while ($r = mysqli_fetch_assoc($res)) {
        $employees[] = $r;
    }
}

// ------------------ GET FILTER VALUES ------------------ //
$selected_emp   = isset($_GET['emp_id']) ? (int)$_GET['emp_id'] : 0;
$selected_month = isset($_GET['month']) && $_GET['month'] !== ''
    ? $_GET['month']           // format Y-m from input type="month"
    : date('Y-m'); // default to current month
$view_mode      = isset($_GET['view']) && $_GET['view'] == '1';
$download_mode  = isset($_GET['download']) && $_GET['download'] == '1';
$view_all_mode  = isset($_GET['view_all']) && $_GET['view_all'] == '1'; // show all employees for current month
$print_all_mode = isset($_GET['print_all']) && $_GET['print_all'] == '1'; // render and print all slips
// Optional return parameter (e.g. return=dashboard) to redirect back after viewing
$return_to      = isset($_GET['return']) ? $_GET['return'] : '';

// ------------------ FETCH SELECTED EMPLOYEE ------------------ //
$employee    = null;
$base_salary = 0.00;
$designation = '';
$department  = '';

if ($selected_emp) {
    $q = mysqli_query(
        $con,
        "SELECT id, name, COALESCE(salary, '') AS salary
         FROM emp_list
         WHERE id = '" . (int)$selected_emp . "'
         LIMIT 1"
    );
    if ($q && mysqli_num_rows($q)) {
        $employee    = mysqli_fetch_assoc($q);
        $base_salary = $employee['salary'] !== '' ? (float)$employee['salary'] : 0.00;
        $designation = isset($employee['designation']) ? $employee['designation'] : '';
        $department  = isset($employee['department']) ? $employee['department'] : '';
    }
}

// ------------------ SALARY CALCULATION (simple formula) ------------------ //
if ($base_salary <= 0) {
    $base_salary_val = 30000.00;   // default
} else {
    $base_salary_val = (float)$base_salary;
}

$hra              = round($base_salary_val * 0.20, 2);
$pf               = round($base_salary_val * 0.05, 2);
$tax              = round($base_salary_val * 0.10, 2);
$other            = 0.00;
$gross            = $base_salary_val + $hra;
$total_deductions = $pf + $tax + $other;
$net              = $gross - $total_deductions;

// ------------------ BASIC STYLES ------------------ //
echo '<link href="css/salary-slip.css" rel="stylesheet">';;

// Wrapper classes (flag print-all so CSS can adjust print rules)
$slip_wrap_classes = 'salary-slip-wrap';
if ($print_all_mode) {
    $slip_wrap_classes .= ' print-all-mode';
}
?>

<div class="<?php echo $slip_wrap_classes; ?>">
    <div class="slip-controls" style="margin-bottom:18px;">
        <h2 class="slip-title"><i class="fa fa-file-text-o" style="margin-right: 10px;"></i> Salary Slip </h2>

        <?php if (!$view_mode && !$view_all_mode): ?>
            <!-- Selection panel -->
            <div class="panel panel-default" style="max-width:600px;">
                <div class="panel-body">
                    <form method="GET" class="form-horizontal" style="margin:20px;">
                        <input type="hidden" name="salary_slip" value="1">

                        <div class="form-group">
                            <label for="emp_id" class="col-sm-2 control-label">Employee</label>
                            <div class="col-sm-6">
                                <select name="emp_id" id="emp_id" class="form-control" style="width:143%;" required>
                                    <option value="">-- Select Employee --</option>
                                    <?php foreach ($employees as $e): ?>
                                        <option value="<?php echo (int)$e['id']; ?>" <?php echo ($selected_emp == $e['id']) ? "selected" : ""; ?>>
                                            <?php echo htmlspecialchars($e['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="month" class="col-sm-2 control-label">Month</label>
                            <div class="col-sm-6">
                                <input type="month"
                                    id="month"
                                    name="month"
                                    class="form-control"
                                    style="width:143%;"
                                    value="<?php echo $selected_month ? htmlspecialchars($selected_month) : ''; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-1 col-sm-10" style="padding-left: 2px;">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-search"></i> Show 
                                </button>
                                <a href="index.php?dashboard" class="btn btn-default" style="margin-left:8px;">
                                    <i class="fa fa-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php
    // ------------------ ALL EMPLOYEES SALARY SLIPS VIEW (for current month) ------------------ //
    if ($view_all_mode):
        $current_month = $selected_month ? $selected_month : date('Y-m');
        $current_month_label = date('F, Y', strtotime($current_month . '-01'));

        // If print_all_mode is requested, render full slips for each employee and trigger print
        if (isset($print_all_mode) && $print_all_mode):
            foreach ($employees as $emp):
                $emp_id_local = isset($emp['id']) ? (int)$emp['id'] : 0;
                $emp_name_local = isset($emp['name']) ? $emp['name'] : '';
                $emp_salary_raw = (isset($emp['salary']) && $emp['salary'] !== '') ? (float)$emp['salary'] : 0.00;
                $base_val_local = ($emp_salary_raw <= 0) ? 30000.00 : $emp_salary_raw;
                $hra_local = round($base_val_local * 0.20, 2);
                $pf_local = round($base_val_local * 0.05, 2);
                $tax_local = round($base_val_local * 0.10, 2);
                $other_local = 0.00;
                $gross_local = $base_val_local + $hra_local;
                $total_deductions_local = $pf_local + $tax_local + $other_local;
                $net_local = $gross_local - $total_deductions_local;
    ?>
                <div class="salary-slip card" style="margin:14px auto; padding:18px; max-width:820px;">
                    <div class="slip-top-decor"></div>
                    <div class="slip-header">
                        <div class="company-left">
                            <img src="../other_images/company-logo.png" alt="Logo" class="company-logo" onerror="this.style.display='none'">
                            <div class="company-center">
                                <h3 class="company-name">8Dots - Innovation IT Solution</h3>
                                <div class="company-address">516, Shivam Trade Centre (STC), Near One World West, Ahmedabad, Gujarat 380058</div>
                                <div class="company-meta-small">Phone: +91 8155 8133 55 &nbsp;|&nbsp; Email: 8dotsinfo@gmail.com</div>
                            </div>
                        </div>
                        <div class="slip-meta">
                            <h4>Salary Slip</h4>
                            <div class="slip-id">Slip No: <strong><?php echo sprintf('%05d', $emp_id_local); ?></strong></div>
                            <p><strong>Period:</strong> <?php echo htmlspecialchars($current_month_label); ?></p>
                        </div>
                    </div>

                    <div class="employee-info clearfix">
                        <div class="emp-left">
                            <p><strong>Employee Name:</strong> <?php echo htmlspecialchars($emp_name_local); ?></p>
                            <p><strong>Employee ID:</strong> <?php echo $emp_id_local; ?></p>
                        </div>
                        <div class="emp-right"></div>
                    </div>

                    <div class="slip-tables">
                        <table class="earn-ded-table">
                            <thead>
                                <tr>
                                    <th style="width:26%;">Earnings</th>
                                    <th class="amt" style="width:24%;">Amount (<?php echo $currency_symbol; ?>)</th>
                                    <th style="width:26%;">Deductions</th>
                                    <th class="amt" style="width:24%;">Amount (<?php echo $currency_symbol; ?>)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Basic Salary</td>
                                    <td class="amt"><?php echo format_money_with_symbol($base_val_local, $currency_symbol); ?></td>
                                    <td>Provident Fund (PF)</td>
                                    <td class="amt"><?php echo format_money_with_symbol($pf_local, $currency_symbol); ?></td>
                                </tr>
                                <tr>
                                    <td>House Rent Allowance (HRA)</td>
                                    <td class="amt"><?php echo format_money_with_symbol($hra_local, $currency_symbol); ?></td>
                                    <td>Tax Deduction</td>
                                    <td class="amt"><?php echo format_money_with_symbol($tax_local, $currency_symbol); ?></td>
                                </tr>
                                <tr>
                                    <td>Other Allowances</td>
                                    <td class="amt"><?php echo format_money_with_symbol($other_local, $currency_symbol); ?></td>
                                    <td>Other Deductions</td>
                                    <td class="amt"><?php echo format_money_with_symbol(0, $currency_symbol); ?></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="totals">
                                    <td><strong>Gross Pay</strong></td>
                                    <td class="amt"><strong><?php echo format_money_with_symbol($gross_local, $currency_symbol); ?></strong></td>
                                    <td><strong>Total Deductions</strong></td>
                                    <td class="amt"><strong><?php echo format_money_with_symbol($total_deductions_local, $currency_symbol); ?></strong></td>
                                </tr>
                                <tr class="net">
                                    <td class="net-label"><strong>Net Pay</strong></td>
                                    <td class="net-amt"><strong><?php echo format_money_with_symbol($net_local, $currency_symbol); ?></strong></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="4" style="padding-top:10px; font-size:12px; font-style:italic;">
                                        <strong>Amount in words:</strong> <?php echo htmlspecialchars(number_to_words($net_local)); ?> Only
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="slip-signature clearfix">
                        <div class="sign-left">
                            <p>Employee Signature</p>
                        </div>
                        <div class="sign-right">
                            <p>Authorized Signatory</p>
                        </div>
                    </div>
                </div>
            <?php
            endforeach; // employees
            ?>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // delay slightly to ensure stylesheets/fonts finish loading
                    // increased timeout to allow CSS/fonts to load before print
                    setTimeout(function() {
                        try {
                            window.focus();
                        } catch (e) {}
                        window.print();
                    }, 800);
                });
            </script>
        <?php
        else:
            // normal table view with link to invoke print-all
            $current_month = $selected_month ? $selected_month : date('Y-m');
            $current_month_label = date('F, Y', strtotime($current_month . '-01'));
            $emp_slips = array();
            foreach ($employees as $emp) {
                $emp_slips[] = array(
                    'id' => $emp['id'],
                    'name' => $emp['name'],
                    'salary' => isset($emp['salary']) ? $emp['salary'] : 0,
                );
            }
        ?>
            <div style="margin-top:18px;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
                    <h3 style="margin:0;">Salary Slips - <?php echo htmlspecialchars($current_month_label); ?></h3>
                    <div>
                        <a class="btn btn-primary" href="?salary_slip=1&view_all=1&print_all=1&month=<?php echo htmlspecialchars($current_month); ?>" style="margin-left:8px;">
                            <i class="fa fa-print"></i> Print All
                        </a>
                        <a href="index.php?dashboard" class="btn btn-default" style="margin-left:8px;">
                            <i class="fa fa-arrow-left"></i> Back to Dashboard
                        </a>
                    </div>
                </div>

                <table class="table table-bordered table-striped slip-table" style="margin-top:12px;">
                    <thead>
                        <tr style="background:#f5f5f5;">
                            <th style="width:50px;">ID</th>
                            <th>Employee Name</th>
                            <th style="width:140px;">Salary (<?php echo $currency_symbol; ?>)</th>
                            <th style="width:220px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($emp_slips as $slip): ?>
                            <tr>
                                <td><?php echo (int)$slip['id']; ?></td>
                                <td><?php echo htmlspecialchars($slip['name']); ?></td>
                                <td class="amt"><?php echo format_money_with_symbol($slip['salary'], $currency_symbol); ?></td>
                                <td>
                                    <a class="btn btn-sm btn-info" href="?salary_slip=1&emp_id=<?php echo (int)$slip['id']; ?>&month=<?php echo htmlspecialchars($current_month); ?>&view=1<?php echo ($return_to == 'dashboard') ? '&return=dashboard' : '&return=view_all'; ?>">
                                        <i class="fa fa-eye"></i> View
                                    </a>
                                    <a class="btn btn-sm btn-success" href="?salary_slip=1&emp_id=<?php echo (int)$slip['id']; ?>&month=<?php echo htmlspecialchars($current_month); ?>&view=1&download=1<?php echo ($return_to == 'dashboard') ? '&return=dashboard' : '&return=view_all'; ?>" style="margin-left:4px;">
                                        <i class="fa fa-download"></i> Download
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php
        endif; // print_all_mode
    endif; // view_all_mode

    // ------------------ RECORDS TABLE (selected month only) ------------------ //
    if ($selected_emp && $selected_month && !$view_mode && $employee): ?>
        <?php
        $months = array();
        $current_month_dt = DateTime::createFromFormat('Y-m', $selected_month);
        if (!$current_month_dt) {
            $current_month_dt = new DateTime();
        }
        $months = array($current_month_dt);
        ?>
        <div class="records-table" style="margin-top:12px;">
            <table class="table table-bordered table-striped slip-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Employee</th>
                        <th>Period (Month - Year)</th>
                        <th style="width:180px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($months as $m_dt): ?>
                        <?php
                        $m_str   = $m_dt->format('Y-m');
                        $m_label = $m_dt->format('F, Y');
                        ?>
                        <tr>
                            <td><?php echo (int)$employee['id']; ?></td>
                            <td><?php echo htmlspecialchars($employee['name']); ?></td>
                            <td><?php echo htmlspecialchars($m_label); ?></td>
                            <td>
                                <a class="btn btn-sm btn-info"
                                    href="?salary_slip=1&emp_id=<?php echo (int)$selected_emp; ?>&month=<?php echo htmlspecialchars($m_str); ?>&view=1<?php echo ($return_to == 'dashboard') ? '&return=dashboard' : ''; ?>">
                                    View
                                </a>
                                <a class="btn btn-sm btn-success"
                                    href="?salary_slip=1&emp_id=<?php echo (int)$selected_emp; ?>&month=<?php echo htmlspecialchars($m_str); ?>&view=1&download=1<?php echo ($return_to == 'dashboard') ? '&return=dashboard' : ''; ?>"
                                    style="margin-left:6px;">
                                    Download
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <?php
    // ------------------ SALARY SLIP VIEW ------------------ //
    if ($view_mode && $employee && $selected_month): ?>
        <div id="slip" class="salary-slip card" style="margin-top:18px; padding:18px;">

            <div style="margin-bottom:10px;">
                <?php
                // Build back URL:
                // - if user requested return to dashboard, go there
                // - if user requested return to view_all, go back to the listing for the same month
                // - otherwise default to the selection/records page for this employee/month
                if ($return_to === 'dashboard') {
                    $back_url = 'index.php?dashboard';
                } elseif ($return_to === 'view_all') {
                    $back_url = 'index.php?salary_slip=1&view_all=1&month=' . urlencode($selected_month);
                } else {
                    $back_url = 'index.php?salary_slip=1&emp_id=' . (int)$selected_emp . '&month=' . urlencode($selected_month);
                }
                ?>
                <a href="<?php echo $back_url; ?>" class="btn btn-default">&larr; Back</a>
            </div>

            <!-- HEADER -->
            <div class="slip-header">
                <div class="company-left">
                    <img src="../other_images/company-logo.png"
                        alt="Logo"
                        class="company-logo"
                        onerror="this.style.display='none'">
                    <div class="company-center">
                        <h3 class="company-name">8Dots - Innovation IT Solution</h3>
                        <div class="company-address">
                            516, Shivam Trade Centre (STC), Near One World West, Ahmedabad, Gujarat 380058
                        </div>
                        <div class="company-meta-small">
                            Phone: +91 8155 8133 55 &nbsp;|&nbsp; Email: 8dotsinfo@gmail.com
                        </div>
                    </div>
                </div>
                <div class="slip-meta">
                    <h4>Salary Slip</h4>
                    <div class="slip-id">Slip No: <strong><?php echo sprintf("%05d", (int)$employee["id"]); ?></strong></div>
                    <p><strong>Period:</strong> <?php echo date("F, Y", strtotime($selected_month . "-01")); ?></p>
                    <p><strong>Pay Date:</strong> <?php echo date("d M Y"); ?></p>
                </div>
            </div>

            <!-- EMPLOYEE INFO -->
            <div class="employee-info clearfix">
                <div class="emp-left">
                    <p><strong>Employee Name:</strong> <?php echo htmlspecialchars($employee["name"]); ?></p>
                    <p><strong>Employee ID:</strong> <?php echo (int)$employee["id"]; ?></p>
                </div>
                <div class="emp-right">
                    <?php if (!empty($designation)): ?>
                        <p><strong>Designation:</strong> <?php echo htmlspecialchars($designation); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($department)): ?>
                        <p><strong>Department:</strong> <?php echo htmlspecialchars($department); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- EARNINGS / DEDUCTIONS TABLE -->
            <div class="slip-tables">
                <table class="earn-ded-table">
                    <thead>
                        <tr>
                            <th style="width:26%;">Earnings</th>
                            <th class="amt" style="width:24%;">Amount (<?php echo $currency_symbol; ?>)</th>
                            <th style="width:26%;">Deductions</th>
                            <th class="amt" style="width:24%;">Amount (<?php echo $currency_symbol; ?>)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Basic Salary</td>
                            <td class="amt"><?php echo format_money_with_symbol($base_salary_val, $currency_symbol); ?></td>
                            <td>Provident Fund (PF)</td>
                            <td class="amt"><?php echo format_money_with_symbol($pf, $currency_symbol); ?></td>
                        </tr>
                        <tr>
                            <td>House Rent Allowance (HRA)</td>
                            <td class="amt"><?php echo format_money_with_symbol($hra, $currency_symbol); ?></td>
                            <td>Tax Deduction</td>
                            <td class="amt"><?php echo format_money_with_symbol($tax, $currency_symbol); ?></td>
                        </tr>
                        <tr>
                            <td>Other Allowances</td>
                            <td class="amt"><?php echo format_money_with_symbol($other, $currency_symbol); ?></td>
                            <td>Other Deductions</td>
                            <td class="amt"><?php echo format_money_with_symbol(0, $currency_symbol); ?></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="totals">
                            <td><strong>Gross Pay</strong></td>
                            <td class="amt"><strong><?php echo format_money_with_symbol($gross, $currency_symbol); ?></strong></td>
                            <td><strong>Total Deductions</strong></td>
                            <td class="amt"><strong><?php echo format_money_with_symbol($total_deductions, $currency_symbol); ?></strong></td>
                        </tr>
                        <tr class="net">
                            <td class="net-label"><strong>Net Pay</strong></td>
                            <td class="net-amt"><strong><?php echo format_money_with_symbol($net, $currency_symbol); ?></strong></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="4" style="padding-top:10px; font-size:12px; font-style:italic;">
                                <strong>Amount in words:</strong> <?php echo htmlspecialchars(number_to_words($net)); ?> Only
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- SIGNATURES -->
            <div class="slip-signature clearfix">
                <div class="sign-left">
                    <p>Employee Signature</p>
                </div>
                <div class="sign-right">
                    <p>Authorized Signatory</p>
                </div>
            </div>

            <!-- ACTION BUTTONS -->
            <div class="slip-actions" style="text-align:right; margin-top:12px;">
                <button id="printBtn" class="btn btn-default">
                    <i class="fa fa-print"></i> Print
                </button>
                <button id="downloadBtn" class="btn btn-success" style="margin-left:8px;">
                    <i class="fa fa-download"></i> Save as PDF
                </button>
            </div>
        </div>

        <?php if ($download_mode): ?>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    window.print();
                });
            </script>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var printBtn = document.getElementById("printBtn");
        var downloadBtn = document.getElementById("downloadBtn");

        if (printBtn) {
            printBtn.addEventListener("click", function(e) {
                e.preventDefault();
                window.print();
            });
        }
        if (downloadBtn) {
            downloadBtn.addEventListener("click", function(e) {
                e.preventDefault();
                window.print(); // Use browser "Save as PDF"
            });
        }
    });

    // Function to print individual slip from all-employees view
    function printSlip(empId, month) {
        window.location.href = "?salary_slip=1&emp_id=" + empId + "&month=" + month + "&view=1&download=1";
    }
</script>

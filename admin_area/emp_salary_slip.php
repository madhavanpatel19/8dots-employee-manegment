<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($con) || !$con) {
    include("connection.php");
}

if (!isset($_SESSION['emp_id'])) {

    // If AJAX request
    if (isset($_GET['ajax'])) {
        echo "SessionExpired";
        exit();
    }

    header("Location: emp-login.php");
    exit();
}

// ------------------ HELPERS ------------------ //
function format_money($n)
{
    return number_format((float)$n, 2);
}

function format_money_with_symbol($n, $currency_symbol)
{
    return '<span class="currency-symbol">' . $currency_symbol . '</span>' . format_money($n);
}

// Convert number to words
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

$emp_id = (int)$_SESSION['emp_id'];
$emp_name = $_SESSION['emp_name'];

// Security: Force emp_id to the session value to prevent viewing others
$_GET['emp_id'] = $emp_id;

// Get selected month
$selected_month = isset($_GET['month']) && $_GET['month'] !== '' ? $_GET['month'] : date('Y-m');
$view_mode = isset($_GET['view']) && $_GET['view'] == '1';

// Fetch salary slip for this employee and month
$month_q = mysqli_real_escape_string($con, $selected_month);
$q = mysqli_query($con, "SELECT e.*, 
                h.basic_salary AS h_basic, 
                h.hra AS h_hra, 
                h.pf AS h_pf, 
                h.tax AS h_tax, 
                h.allowance AS h_allow, 
                h.deductions AS h_ded
         FROM emp_list e
         LEFT JOIN emp_salary_history h ON e.id = h.emp_id AND h.month = '$month_q'
         WHERE e.id = " . (int)$emp_id . " LIMIT 1");
$employee = ($q && mysqli_num_rows($q)) ? mysqli_fetch_assoc($q) : null;

$base_salary = $employee && $employee['salary'] !== '' ? (float)$employee['salary'] : 0.00;

// Salary calculation components (Prioritize history)
$base_salary_val = ($employee && $employee['h_basic'] !== null && $employee['h_basic'] > 0) ? (float)$employee['h_basic'] : (($employee && $employee['basic_salary'] !== null && $employee['basic_salary'] > 0) ? (float)$employee['basic_salary'] : (($base_salary <= 0) ? 30000.00 : (float)$base_salary));

$hra = ($employee && $employee['h_hra'] !== null) ? (float)$employee['h_hra'] : (($employee && $employee['hra'] !== null) ? (float)$employee['hra'] : round($base_salary_val * 0.20, 2));

$pf = ($employee && $employee['h_pf'] !== null) ? (float)$employee['h_pf'] : round($base_salary_val * 0.05, 2);

$tax = ($employee && $employee['h_tax'] !== null) ? (float)$employee['h_tax'] : round($base_salary_val * 0.10, 2);

$other_allow = ($employee && $employee['h_allow'] !== null) ? (float)$employee['h_allow'] : (($employee && $employee['allowance'] !== null) ? (float)$employee['allowance'] : 0.00);

$other_ded = ($employee && $employee['h_ded'] !== null) ? (float)$employee['h_ded'] : (($employee && $employee['deductions'] !== null) ? (float)$employee['deductions'] : 0.00);

$gross = $base_salary_val + $hra + $other_allow;
$total_deductions = $pf + $tax + $other_ded;
$net = $gross - $total_deductions;

$currency_symbol = '&#8377;';

// Handle AJAX request for viewing a slip
if (isset($_GET['ajax']) && isset($_GET['view']) && $employee) {
    ob_start();
?>
    <div id="slip-content" class="salary-slip card" style="margin:10px auto; padding:18px; max-width:820px;">
        <div class="slip-top-decor"></div>
        <div class="slip-header">
            <div class="company-left">
                <img src="other_images/company-logo.png" alt="Logo" class="company-logo" onerror="this.style.display='none'">
                <div class="company-center">
                    <h3 class="company-name">8Dots</h3>
                    <div class="company-address">516, Shivam Trade Centre (STC), Near One World West, Ahmedabad, Gujarat 380058</div>
                    <div class="company-meta-small">Phone: +91 8155 8133 55 &nbsp;|&nbsp; Email: 8dotsinfo@gmail.com</div>
                </div>
            </div>
            <div class="slip-meta">
                <h4>Salary Slip</h4>
                <div class="slip-id">Slip No: <strong><?php echo sprintf('%05d', $emp_id); ?></strong></div>
                <p><strong>Period:</strong> <?php echo date('F, Y', strtotime($selected_month . '-01')); ?></p>
            </div>
        </div>

        <div class="employee-info clearfix">
            <div class="emp-left">
                <p><strong>Employee Name:</strong> <?php echo htmlspecialchars($emp_name); ?></p>
                <p><strong>Employee ID:</strong> <?php echo (int)$emp_id; ?></p>
            </div>
            <div class="emp-right">
                <p><strong>Pay Date:</strong> <?php echo date('t M Y', strtotime($selected_month . '-01')); ?></p>
            </div>
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
                        <td class="amt"><?php echo format_money_with_symbol($other_allow, $currency_symbol); ?></td>
                        <td>Other Deductions</td>
                        <td class="amt"><?php echo format_money_with_symbol($other_ded, $currency_symbol); ?></td>
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
                        <td colspan="2"></td>
                    </tr>
                    <tr>
                        <td colspan="4" style="padding-top:10px; font-size:12px; font-style:italic;">
                            <strong>Amount in words:</strong> <?php echo htmlspecialchars(number_to_words($net)); ?> Only
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
    $content = ob_get_clean();
    echo $content;
    exit();
}

// UI
?>
<link href="css/salary-slip.css" rel="stylesheet">
<style>
    @media print {
        body * {
            visibility: hidden;
        }

        #salarySlipModalBody,
        #salarySlipModalBody * {
            visibility: visible;
        }

        #salarySlipModalBody {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }

        .modal-header,
        .modal-footer,
        .btn,
        .no-print {
            display: none !important;
        }
    }
</style>

<div class="salary-slip-wrap">
    <div style="margin-top:18px;">
        <h3 style="margin:0 0 15px 0;">My Salary Slips</h3>
        <table class="table table-bordered table-striped slip-table" style="margin-top:12px;">
            <thead>
                <tr style="background:#f5f5f5;">
                    <th style="width:50px;">ID</th>
                    <th>Employee Name</th>
                    <th style="width:120px;">Month</th>
                    <th style="width:140px;">Salary (<?php echo $currency_symbol; ?>)</th>
                    <th style="width:120px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Show last 12 months, but not before join date
                $join_month = ($employee && !empty($employee['join_date']) && $employee['join_date'] !== '0000-00-00') 
                            ? date('Y-m', strtotime($employee['join_date'])) 
                            : '1970-01';

                for ($i = 0; $i < 12; $i++) {
                    $ts = strtotime("-{$i} month");
                    $m_val = date('Y-m', $ts);
                    
                    // Stop if we go before join date
                    if ($m_val < $join_month) break;

                    $m_label = date('F, Y', $ts);
                    $is_current = ($m_val === date('Y-m'));
                    $current_day = (int)date('d');

                    // Fetch history for this row to show correct amount in table
                    $row_q = mysqli_query($con, "SELECT net_pay FROM emp_salary_history WHERE emp_id = '" . (int)$emp_id . "' AND month = '$m_val'");
                    $row_data = mysqli_fetch_assoc($row_q);
                    
                    if ($row_data && $row_data['net_pay'] !== null) {
                        $row_salary = (float)$row_data['net_pay'];
                    } else {
                        // Calculate default row salary if no history using profile fields
                        $row_base = ($employee && $employee['basic_salary'] !== null && $employee['basic_salary'] > 0) ? (float)$employee['basic_salary'] : (($base_salary <= 0) ? 30000.00 : $base_salary);
                        
                        $row_hra = ($employee && $employee['hra'] !== null) ? (float)$employee['hra'] : round($row_base * 0.20, 2);
                        $row_pf = round($row_base * 0.05, 2);
                        $row_tax = round($row_base * 0.10, 2);
                        $row_allow = ($employee && $employee['allowance'] !== null) ? (float)$employee['allowance'] : 0.00;
                        $row_ded = ($employee && $employee['deductions'] !== null) ? (float)$employee['deductions'] : 0.00;
                        
                        $row_salary = ($row_base + $row_hra + $row_allow) - ($row_pf + $row_tax + $row_ded);
                    }
                    
                    // Logic: Disable view for current month until end of month (e.g., after 25th)
                    // unless a specific history record exists (admin manually saved it)
                    $can_view = true;
                    if ($is_current && $current_day < 25) {
                        if (!$row_data) {
                            $can_view = false;
                        }
                    }
                ?>
                    <tr<?php if ($is_current) echo ' style="background:#eaf7ff;"'; ?>>
                        <td><?php echo (int)$emp_id; ?></td>
                        <td><?php echo htmlspecialchars($emp_name); ?></td>
                        <td><?php echo htmlspecialchars($m_label); ?></td>
                        <td class="amt"><?php echo format_money_with_symbol($row_salary, $currency_symbol); ?></td>
                        <td>
                            <?php if ($can_view): ?>
                                <button type="button"
                                    class="btn btn-sm btn-info view-slip-btn"
                                    data-month="<?php echo $m_val; ?>">
                                    <i class="fa fa-eye"></i> View
                                </button>
                            <?php else: ?>
                                <span class="text-muted" title="Available at month end"><i class="fa fa-lock"></i> Locked</span>
                            <?php endif; ?>
                        </td>
                        </tr>
                    <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="salarySlipModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Salary Slip</h4>
                </div>
                <div class="modal-body" id="salarySlipModalBody">
                    <div style="text-align:center; padding:40px;"><i class="fa fa-spinner fa-spin fa-2x"></i><br>Loading...</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="modalPrintBtn">
                        <i class="fa fa-print"></i> Print
                    </button>
                    <button type="button" class="btn btn-success" id="modalDownloadBtn">
                        <i class="fa fa-download"></i> Save as PDF
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {

            $('.view-slip-btn').click(function() {

                var month = $(this).data('month');

                $('#salarySlipModalBody').html(
                    '<div style="text-align:center;padding:40px;"><i class="fa fa-spinner fa-spin fa-2x"></i><br>Loading...</div>'
                );

                $('#salarySlipModal').modal('show');

                $.ajax({
                    url: 'emp_salary_slip.php',
                    type: 'GET',
                    data: {
                        month: month,
                        view: 1,
                        ajax: 1
                    },
                    success: function(data) {

                        if (data.trim() === "SessionExpired") {
                            window.location = "emp-login.php";
                            return;
                        }

                        $('#salarySlipModalBody').html(data);

                    },
                    error: function() {
                        $('#salarySlipModalBody').html(
                            '<div style="text-align:center;color:red;">Error loading salary slip.</div>'
                        );
                    }
                });

            });
            
            // Print Button Event
            $('#modalPrintBtn').click(function() {
                window.print();
            });
            
            // Download Button Event (Save as PDF)
            $('#modalDownloadBtn').click(function() {
                window.print();
              });

        });
    </script>
</div>
<?php
if (!isset($_SESSION['admin_email'])) {
    echo "<script>window.open('../../pages/auth/login.php','_self')</script>";
} else {
    // Mark all feedback as read when viewing this page
    $update_read = "UPDATE customer_feedback SET is_read=1 WHERE is_read=0";
    mysqli_query($con, $update_read);
?>
    <style>
        .table-premium th {
            text-align: center !important;
        }

        .table-premium td:nth-child(3),
        .table-premium th:nth-child(3) {
            text-align: left !important;
        }

        .table-premium td:nth-child(5),
        .table-premium th:nth-child(5) {
            text-align: left !important;
        }

        .p-badge-primary {
            background: rgba(79, 70, 229, 0.05);
            color: var(--p-bg-color);
        }

        .p-badge-info {
            background: rgba(14, 165, 233, 0.1);
            color: var(--p-bg-color);
        }

        .p-badge-danger {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }
    </style>

    <div class="page-wrapper premium-ui-enabled">
        <div class="page-header-premium">
            <h1><i class="fa fa-comments" style="color: #333;"></i> Client Feedback</h1>
            <button onclick="shareFeedbackForm()" class="btn-premium-add" style="background: #0f172a !important; border: none; cursor: pointer;">
                <i class="fa fa-share-alt"></i> Share Feedback Form
            </button>
        </div>

        <div class="premium-card">
            <div class="card-hdr">
                <i class="fa fa-commenting-o"></i>
                <h3>Recent Customer Feedback</h3>
            </div>
            <div style="overflow-x: auto;">
                <table class="table-premium">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th style="width: 120px;">Date</th>
                            <th style="width: 200px;">Customer Name</th>
                            <th style="width: 120px;">Rating</th>
                            <th style="width: 180px;">Contact info</th>
                            <th style="width: 130px;">Month</th>
                            <th style="width: 100px;">Quality</th>
                            <th style="width: 100px;">On Time</th>
                            <th style="width: 120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 0;
                        $get_feedback = "select * from customer_feedback order by created_at DESC";
                        $run_feedback = mysqli_query($con, $get_feedback);

                        if (!$run_feedback) {
                            echo "<tr><td colspan='9' class='text-center' style='padding: 20px; color: red;'>Query Error: " . mysqli_error($con) . "</td></tr>";
                        } elseif (mysqli_num_rows($run_feedback) == 0) {
                            echo "<tr><td colspan='9' class='text-center' style='padding: 40px; color: #64748b;'>
                                <i class='fa fa-info-circle' style='font-size: 24px; display: block; margin-bottom: 10px;'></i>
                                No feedback records found in database.
                              </td></tr>";
                        } else {
                            while ($row_feedback = mysqli_fetch_array($run_feedback)) {
                                $id = $row_feedback['id'];
                                $customer_name = $row_feedback['customer_name'];
                                $rating = (int)$row_feedback['rating'];
                                $is_read = $row_feedback['is_read'];
                                $created_at = $row_feedback['created_at'];
                                $i++;
                        ?>
                                <tr>
                                    <td class="text-center" style="font-weight: 700; color: #64748b;"><?php echo $i; ?></td>
                                    <td class="text-center" style="color: #64748b; font-size: 13px;">
                                        <?php echo date('d-m-Y', strtotime($created_at)); ?>
                                    </td>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <span style="font-weight: 700; color: #1e293b;"><?php echo htmlspecialchars($customer_name); ?></span>
                                            <?php if ($is_read == 0) : ?>
                                                <span class="p-badge p-badge-danger" style="font-size: 10px;">NEW</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="text-center" style="white-space: nowrap;">
                                        <?php
                                        for ($star = 1; $star <= 5; $star++) {
                                            echo $star <= $rating ? '<i class="fa fa-star" style="color: #fbbf24;"></i>' : '<i class="fa fa-star-o" style="color: #cbd5e1;"></i>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <div style="font-size: 13px; color: #475569;">
                                            <i class="fa fa-phone" style="color: #94a3b8; font-size: 12px;"></i> <?php echo htmlspecialchars($row_feedback['contact_number']); ?>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="p-badge p-badge-primary"><?php echo htmlspecialchars($row_feedback['service_month']); ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="p-badge p-badge-info"><?php echo htmlspecialchars($row_feedback['service_quality']); ?></span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($row_feedback['service_on_time'] == "Yes"): ?>
                                            <span style="color: #10b981; font-weight: 800; font-size: 13px;"><i class="fa fa-check-circle"></i> Yes</span>
                                        <?php else: ?>
                                            <span style="color: #ef4444; font-weight: 800; font-size: 13px;"><i class="fa fa-times-circle"></i> No</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-xs btn-default" style="padding: 6px 12px; border-radius: 6px;" data-toggle="modal" data-target="#feedbackModal<?php echo $i; ?>">
                                            <i class="fa fa-expand"></i> Details
                                        </button>
                                    </td>
                                </tr>

                                <!-- Feedback Modal (Enhanced) -->
                                <div id="feedbackModal<?php echo $i; ?>" class="modal fade" role="dialog">
                                    <div class="modal-dialog modal-md">
                                        <div class="modal-content" style="border-radius: 16px; border: none; overflow: hidden;">
                                            <div class="modal-header" style="background: #f8fafc; padding: 20px 25px; border-bottom: 1px solid #e2e8f0;">
                                                <button type="button" class="close" data-dismiss="modal" style="background: #f1f5f9; width: 32px; height: 32px; border-radius: 50%; opacity: 1; display: flex; align-items: center; justify-content: center;">&times;</button>
                                                <h4 class="modal-title" style="font-weight: 800; color: #0f172a;">Feedback: <?php echo htmlspecialchars($customer_name); ?></h4>
                                            </div>
                                            <div class="modal-body" style="padding: 25px;">
                                                <div style="display: flex; flex-direction: column; gap: 20px;">
                                                    <div>
                                                        <label style="text-transform: uppercase; font-size: 11px; font-weight: 800; color: #64748b; letter-spacing: 0.5px; display: block; margin-bottom: 8px;">Customer Email</label>
                                                        <p style="margin: 0; color: #334155; font-weight: 600;"><?php echo htmlspecialchars($row_feedback['email'] ?: 'N/A'); ?></p>
                                                    </div>

                                                    <div style="display: flex; gap: 15px;">
                                                        <div style="flex: 1; background: #f0f9ff; padding: 12px; border-radius: 10px; border: 1px solid #bae6fd;">
                                                            <label style="font-weight: 800; color: #0369a1; font-size: 11px; display: block; margin-bottom: 3px;">Professionalism</label>
                                                            <span style="font-weight: 700; color: #075985;"><?php echo htmlspecialchars($row_feedback['professionalism'] ?: 'N/A'); ?></span>
                                                        </div>
                                                        <div style="flex: 1; background: #f0fdf4; padding: 12px; border-radius: 10px; border: 1px solid #bbf7d0;">
                                                            <label style="font-weight: 800; color: #166534; font-size: 11px; display: block; margin-bottom: 3px;">Satisfaction</label>
                                                            <span style="font-weight: 700; color: #15803d;"><?php echo htmlspecialchars($row_feedback['overall_satisfaction'] ?: 'N/A'); ?></span>
                                                        </div>
                                                    </div>

                                                    <div style="background: #fdf2f8; padding: 15px; border-radius: 12px; border-left: 4px solid #db2777;">
                                                        <label style="font-weight: 800; color: #9d174d; font-size: 12px; display: block; margin-bottom: 5px;"><i class="fa fa-heart"></i> Liked Most</label>
                                                        <p style="margin: 0; color: #831843; font-size: 14px; line-height: 1.6;"><?php echo nl2br(htmlspecialchars($row_feedback['liked'] ?: 'No comment')); ?></p>
                                                    </div>
                                                    <div style="background: #fff7ed; padding: 15px; border-radius: 12px; border-left: 4px solid #ea580c;">
                                                        <label style="font-weight: 800; color: #9a3412; font-size: 12px; display: block; margin-bottom: 5px;"><i class="fa fa-wrench"></i> Improvement Areas</label>
                                                        <p style="margin: 0; color: #7c2d12; font-size: 14px; line-height: 1.6;"><?php echo nl2br(htmlspecialchars($row_feedback['improvement'] ?: 'No comment')); ?></p>
                                                    </div>
                                                    <div style="background: #f8fafc; padding: 15px; border-radius: 12px; border: 1px solid #e2e8f0;">
                                                        <label style="font-weight: 800; color: #475569; font-size: 12px; display: block; margin-bottom: 5px;"><i class="fa fa-comment-o"></i> Additional Comments</label>
                                                        <p style="margin: 0; color: #1e293b; font-size: 14px; line-height: 1.6;"><?php echo nl2br(htmlspecialchars($row_feedback['comments'] ?: 'None')); ?></p>
                                                    </div>
                                                    <div style="display: flex; justify-content: space-between; align-items: center; background: #f0fdf4; padding: 12px 18px; border-radius: 10px;">
                                                        <span style="font-weight: 700; color: #166534; font-size: 14px;">Recommend to others?</span>
                                                        <span style="font-weight: 800; color: #15803d; text-transform: uppercase;"><?php echo htmlspecialchars($row_feedback['recommend']); ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer" style="padding: 15px 25px; background: #f8fafc; border-top: 1px solid #e2e8f0;">
                                                <button type="button" class="btn btn-default" data-dismiss="modal" style="font-weight: 700; border-radius: 8px;">Dismiss</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        <?php }
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function shareFeedbackForm() {
            // Construct the full URL
            const url = window.location.origin + window.location.pathname.replace('index.php', 'client_feedback_form.php');

            // Check if Web Share API is available
            if (navigator.share) {
                navigator.share({
                    title: 'Customer Feedback Form',
                    text: 'Please provide your valuable feedback:',
                    url: url
                }).catch(err => {
                    console.log('Error sharing:', err);
                    copyToClipboard(url);
                });
            } else {
                copyToClipboard(url);
            }
        }

        function copyToClipboard(text) {
            const el = document.createElement('textarea');
            el.value = text;
            document.body.appendChild(el);
            el.select();
            document.execCommand('copy');
            document.body.removeChild(el);

            // Show a custom toast/alert
            const toast = document.createElement('div');
            toast.style.cssText = `
        position: fixed;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%);
        background: #0f172a;
        color: #fff;
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 600;
        z-index: 9999;
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.2);
        animation: fadeInOut 2s ease forwards;
    `;
            toast.innerHTML = '<i class="fa fa-check-circle" style="color: #10b981; margin-right: 8px;"></i> Link copied to clipboard!';
            document.body.appendChild(toast);

            // Injects the animation keyframes
            if (!document.getElementById('toast-animation-style')) {
                const style = document.createElement('style');
                style.id = 'toast-animation-style';
                style.innerHTML = `
            @keyframes fadeInOut {
                0% { opacity: 0; transform: translate(-50%, 20px); }
                15% { opacity: 1; transform: translate(-50%, 0); }
                85% { opacity: 1; transform: translate(-50%, 0); }
                100% { opacity: 0; transform: translate(-50%, -20px); }
            }
        `;
                document.head.appendChild(style);
            }

            setTimeout(() => toast.remove(), 2500);

            // Option to open in other tab after a delay if desired (following user's "after open other tab")
            setTimeout(() => {
                Swal.fire({
                    title: 'Link copied!',
                    text: "Would you like to open the form in a new tab for preview?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#94a3b8',
                    confirmButtonText: 'Yes, open it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.open(text, '_blank');
                    }
                });
            }, 500);
        }
    </script>

<?php } ?>
<style>
    .text-warning {
        color: #f0ad4e;
    }

    .text-muted {
        color: #ccc;
    }

    .text-success {
        color: #28a745;
    }

    .text-danger {
        color: #dc3545;
    }

    .label-info {
        background-color: #5bc0de;
    }

    .table th {
        background-color: #f8f9fa;
        border-top: 2px solid #dee2e6 !important;
    }

    .table td,
    .table th {
        vertical-align: middle !important;
    }

    .text-center {
        text-align: center;
    }
</style>
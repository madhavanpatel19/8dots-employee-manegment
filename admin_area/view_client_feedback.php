<?php
if (!isset($_SESSION['admin_email'])) {
    echo "<script>window.open('login.php','_self')</script>";
} else {
    // Mark all feedback as read when viewing this page
    $update_read = "UPDATE customer_feedback SET is_read=1 WHERE is_read=0";
    mysqli_query($con, $update_read);
?>
    <div class="row"><!-- row Starts -->
        <div class="col-lg-12"><!-- col-lg-12 Starts -->
            <ol class="breadcrumb"><!-- breadcrumb Starts -->
                <li class="active">
                    <i class="fa fa-dashboard"></i> Dashboard / View Client Feedback
                </li>
            </ol><!-- breadcrumb Ends -->
        </div><!-- col-lg-12 Ends -->
    </div><!-- row Ends -->

    <div class="row"><!-- 2 row Starts -->
        <div class="col-lg-12"><!-- col-lg-12 Starts -->
            <div class="panel panel-default"><!-- panel panel-default Starts -->
                <div class="custom-page-header" style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; margin-bottom: 20px; gap: 15px;">
                    <h3 class="panel-title" style="margin: 0;">
                        <i class="fa fa-comments fa-fw"></i> Client Feedback
                    </h3>
                </div>

                <div class="panel-body"><!-- panel-body Starts -->
                    <div class="table-responsive"><!-- table-responsive Starts -->
                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Date</th>
                                    <th>Customer Name</th>
                                    <th class="text-center">Rating</th>
                                    <th>Contact</th>
                                    <th class="text-center">Service Month</th>
                                    <th class="text-center">Quality</th>
                                    <th class="text-center">On Time</th>
                                    <th class="text-center">Professionalism</th>
                                    <th class="text-center">Overall</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 0;
                                $get_feedback = "select * from customer_feedback order by created_at DESC";
                                $run_feedback = mysqli_query($con, $get_feedback);
                                while ($row_feedback = mysqli_fetch_array($run_feedback)) {
                                    $id = $row_feedback['id']; // Assuming there is an ID column, let me check check_table.php output again
                                    $customer_name = $row_feedback['customer_name'];
                                    $contact_number = $row_feedback['contact_number'];
                                    $service_month = $row_feedback['service_month'];
                                    $service_quality = $row_feedback['service_quality'];
                                    $service_on_time = $row_feedback['service_on_time'];
                                    $professionalism = $row_feedback['professionalism'];
                                    $overall_satisfaction = $row_feedback['overall_satisfaction'];
                                    $rating = $row_feedback['rating'];
                                    $is_read = $row_feedback['is_read'];
                                    $created_at = $row_feedback['created_at'];
                                    $i++;
                                ?>
                                    <tr>
                                        <td class="text-center"><?php echo $i; ?></td>
                                        <td class="text-center"><?php echo date('d-m-y', strtotime($created_at)); ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($customer_name); ?></strong>
                                            <?php if ($is_read == 0) : ?>
                                                <span class="label label-danger" style="font-size: 9px; padding: 1px 3px; margin-left: 5px;">NEW</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php
                                            for ($star = 1; $star <= 5; $star++) {
                                                if ($star <= $rating) {
                                                    echo "<i class='fa fa-star text-warning'></i>";
                                                } else {
                                                    echo "<i class='fa fa-star-o text-muted'></i>";
                                                }
                                            }
                                            ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($contact_number); ?></td>
                                        <td class="text-center"><?php echo htmlspecialchars($service_month); ?></td>
                                        <td class="text-center"><span class="label label-info"><?php echo htmlspecialchars($service_quality); ?></span></td>
                                        <td class="text-center">
                                            <?php if ($service_on_time == "Yes"): ?>
                                                <span class="text-success"><i class="fa fa-check-circle"></i> Yes</span>
                                            <?php else: ?>
                                                <span class="text-danger"><i class="fa fa-times-circle"></i> No</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center"><?php echo htmlspecialchars($professionalism); ?></td>
                                        <td class="text-center"><?php echo htmlspecialchars($overall_satisfaction); ?></td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#feedbackModal<?php echo $i; ?>">
                                                <i class="fa fa-eye"></i> View
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Feedback Modal -->
                                    <div id="feedbackModal<?php echo $i; ?>" class="modal fade" role="dialog">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                    <h4 class="modal-title">Feedback Details - <?php echo htmlspecialchars($customer_name); ?></h4>
                                                </div>
                                                <div class="modal-body">
                                                    <p><strong>Email:</strong> <?php echo htmlspecialchars($row_feedback['email']); ?></p>
                                                    <p><strong>Liked Most:</strong> <?php echo nl2br(htmlspecialchars($row_feedback['liked'])); ?></p>
                                                    <p><strong>Improvement Areas:</strong> <?php echo nl2br(htmlspecialchars($row_feedback['improvement'])); ?></p>
                                                    <p><strong>Additional Comments:</strong> <?php echo nl2br(htmlspecialchars($row_feedback['comments'])); ?></p>
                                                    <p><strong>Recommend:</strong> <?php echo htmlspecialchars($row_feedback['recommend']); ?></p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                <?php } ?>
                            </tbody>
                        </table>
                    </div><!-- table-responsive Ends -->
                </div><!-- panel-body Ends -->
            </div><!-- panel panel-default Ends -->
        </div><!-- col-lg-12 Ends -->
    </div><!-- 2 row Ends -->
<?php } ?>
<style>
    .text-warning { color: #f0ad4e; }
    .text-muted { color: #ccc; }
    .text-success { color: #28a745; }
    .text-danger { color: #dc3545; }
    .label-info { background-color: #5bc0de; }
    .table th { background-color: #f8f9fa; border-top: 2px solid #dee2e6 !important; }
    .table td, .table th { vertical-align: middle !important; }
    .text-center { text-align: center; }
</style>

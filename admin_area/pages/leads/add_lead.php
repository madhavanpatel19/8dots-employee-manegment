<?php
if (!isset($_SESSION['admin_email'])) {
    echo "<script>window.open('../../pages/auth/login.php','_self')</script>";
    exit;
}

if (isset($_POST['save_lead'])) {
    $client_name = mysqli_real_escape_string($con, $_POST['client_name']);
    $phone = mysqli_real_escape_string($con, $_POST['phone']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $company_name = mysqli_real_escape_string($con, $_POST['company_name']);
    $project_name = mysqli_real_escape_string($con, $_POST['project_name']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $remark = mysqli_real_escape_string($con, $_POST['remark']);
    $budget = mysqli_real_escape_string($con, $_POST['budget']);
    $currency = mysqli_real_escape_string($con, $_POST['currency']);
    $status = mysqli_real_escape_string($con, $_POST['status']);
    $followup_date = mysqli_real_escape_string($con, $_POST['followup_date']);

    // Handle multiple checkboxes for lead source
    $lead_sources = isset($_POST['lead_source']) ? $_POST['lead_source'] : [];
    $lead_source_str = implode(', ', $lead_sources);

    $insert_lead = "INSERT INTO leads (client_name, phone, email, company_name, project_name, description, remark, budget, currency, lead_source, status, followup_date) 
                    VALUES ('$client_name', '$phone', '$email', '$company_name', '$project_name', '$description', '$remark', '$budget', '$currency', '$lead_source_str', '$status', '$followup_date')";

    if (mysqli_query($con, $insert_lead)) {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Success!',
                    text: 'Lead added successfully!',
                    icon: 'success',
                    confirmButtonColor: '#10b981',
                    background: '#ffffff',
                    customClass: { popup: 'premium-card' }
                }).then(() => {
                    window.location.href = 'index.php?leads';
                });
            });
        </script>";
    } else {
        $err = mysqli_real_escape_string($con, mysqli_error($con));
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Error!',
                    text: '$err',
                    icon: 'error',
                    confirmButtonColor: '#ef4444'
                });
            });
        </script>";
    }
}
?>

<div class="page-wrapper premium-ui-enabled">
    <div class="page-header-premium">
        <h1></h1>
        <div class="header-actions-premium">
            <a href="index.php?leads" class="btn-premium-add" style="background: #f1f5f9 !important; color: #475569 !important; border: 1.5px solid #e2e8f0 !important; box-shadow: none !important;">
                <i class="fa fa-arrow-left"></i> Back to Leads
            </a>
        </div>
    </div>

    <div class="premium-card">
        <div class="card-hdr" style="background: var(--p-bg-header);">
            <i class="fa fa-plus-circle"></i>
            <h3>New Lead Registration</h3>
        </div>

        <div style="padding: 40px;">
            <form method="POST" class="form-horizontal">
                <!-- Section: Client Details -->
                <div style="margin-bottom: 35px; padding-bottom: 10px; border-bottom: 1px solid #f1f5f9;">
                    <h4 style="font-weight: 700; color: #4f46e5; margin: 0;"><i class="fa fa-user-circle-o"></i> Client Information</h4>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-md-4 control-label" style="text-align: left; color: #475569; font-weight: 600;">Client Name <span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <input type="text" name="client_name" class="p-input-premium" required placeholder="Full Name">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label" style="text-align: left; color: #475569; font-weight: 600;">
                                Phone No <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-8">
                                <div style="position: relative;">
                                    <i class="fa fa-phone" style="position: absolute; left: 12px; top: 15px; color: #94a3b8; font-size: 14px;"></i>
                                    <input type="text"
                                        name="phone"
                                        class="p-input-premium"
                                        required
                                        placeholder="Mobile Number"
                                        maxlength="10"
                                        pattern="[0-9]{10}"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                        style="padding-left: 35px;">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label" style="text-align: left; color: #475569; font-weight: 600;">Email ID</label>
                            <div class="col-md-8">
                                <div style="position: relative;">
                                    <i class="fa fa-envelope-o" style="position: absolute; left: 12px; top: 15px; color: #94a3b8; font-size: 14px;"></i>
                                    <input type="email" name="email" class="p-input-premium" placeholder="email@example.com" style="padding-left: 35px;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-md-4 control-label" style="text-align: left; color: #475569; font-weight: 600;">Company</label>
                            <div class="col-md-8">
                                <input type="text" name="company_name" class="p-input-premium" placeholder="Organization Name">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label" style="text-align: left; color: #475569; font-weight: 600;">Project Name</label>
                            <div class="col-md-8">
                                <input type="text" name="project_name" class="p-input-premium" placeholder="Project Name">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label" style="text-align: left; color: #475569; font-weight: 600;">Budget</label>
                            <div class="col-md-8">
                                <div style="display: flex; gap: 10px;">
                                    <select name="currency" class="p-input-premium" style="width: 100px; flex-shrink: 0;">
                                        <option value="INR" selected>₹ INR</option>
                                        <option value="USD">$ USD</option>
                                        <option value="EUR">€ EUR</option>
                                        <option value="GBP">£ GBP</option>
                                        <option value="AED">د.إ AED</option>
                                    </select>
                                    <input type="text" name="budget" class="p-input-premium" placeholder="e.g. 50k, 1 Lac">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section: Lead Status & Source -->
                <div style="margin: 40px 0 35px 0; padding-bottom: 10px; border-bottom: 1px solid #f1f5f9;">
                    <h4 style="font-weight: 700; color: #10b981; margin: 0;"><i class="fa fa-sliders"></i> Classification & Follow-up</h4>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-md-4 control-label" style="text-align: left; color: #475569; font-weight: 600;">Status</label>
                            <div class="col-md-8">
                                <select name="status" class="p-input-premium">
                                    <option value="active">Active</option>
                                    <option value="future">Future</option>
                                    <option value="expired">Expired</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label" style="text-align: left; color: #475569; font-weight: 600;">Next Follow-up</label>
                            <div class="col-md-8">
                                <input type="date" name="followup_date" class="p-input-premium">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="col-md-4" style="display: flex; justify-content: flex-start; align-items: center; gap: 10px; padding-top: 7px; padding-right: 0;">
                                <label class="control-label" style="text-align: left; color: #475569; font-weight: 600; margin: 0; padding-top: 0;">Source</label>
                                <button type="button" class="btn btn-xs btn-success" data-toggle="modal" data-target="#addSourceModal" style="border-radius: 6px; padding: 2px 8px; font-size: 10px; font-weight: 700; background: #10b981; border: none; box-shadow: 0 2px 4px rgba(16,185,129,0.2);">
                                    <i class="fa fa-plus"></i> New
                                </button>
                            </div>
                            <div class="col-md-8">
                                <div style="background: #f8fafc; padding: 10px; border-radius: 12px; border: 1px solid #e2e8f0; min-height: 100px; max-height: 150px; overflow-y: auto;" id="source_checkbox_container">
                                    <?php
                                    $get_sources = "SELECT * FROM lead_sources ORDER BY source_name ASC";
                                    $run_sources = mysqli_query($con, $get_sources);
                                    while ($row_s = mysqli_fetch_array($run_sources)):
                                        $s_name = $row_s['source_name'];
                                        $s_id = $row_s['id'];
                                    ?>
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                                            <label style="font-weight: 500; color: #475569; cursor: pointer; margin: 0;">
                                                <input type="checkbox" name="lead_source[]" value="<?php echo htmlspecialchars($s_name); ?>" style="margin-right: 8px; width: 16px; height: 16px; vertical-align: middle; accent-color: #4f46e5;"> <?php echo htmlspecialchars($s_name); ?>
                                            </label>
                                            <i class="fa fa-trash" style="color: #ef4444; cursor: pointer; font-size: 13px;" onclick="deleteSource(<?php echo $s_id; ?>, this)"></i>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                                <small style="color: #94a3b8; font-size: 11px; margin-top: 5px; display: block;">Select all that apply</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section: Remarks -->
                <div style="margin: 40px 0 35px 0; padding-bottom: 10px; border-bottom: 1px solid #f1f5f9;">
                    <h4 style="font-weight: 700; color: #64748b; margin: 0;"><i class="fa fa-commenting-o"></i> Additional Remarks</h4>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-md-4 control-label" style="text-align: left; color: #475569; font-weight: 600;">Description</label>
                            <div class="col-md-8">
                                <textarea name="description" class="p-input-premium" style="height: 100px; padding: 12px;" placeholder="Detailed lead requirements..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-md-4 control-label" style="text-align: left; color: #475569; font-weight: 600;">Internal Note</label>
                            <div class="col-md-8">
                                <textarea name="remark" class="p-input-premium" style="height: 100px; padding: 12px;" placeholder="Initial internal remarks..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="margin-top: 50px; text-align: right; border-top: 1.5px solid #f1f5f9; padding-top: 30px;">
                    <a href="index.php?leads" class="btn btn-default" style="height: 48px; border-radius: 12px; padding: 12px 30px; font-weight: 600; margin-right: 10px;">Cancel</a>
                    <button type="submit" name="save_lead" class="btn-premium-add" style="padding: 14px 45px !important; font-size: 15px !important; border: none;">
                        <i class="fa fa-save"></i> Save Lead Information
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Source Modal -->
<div class="modal fade" id="addSourceModal" tabindex="-1" role="dialog" aria-labelledby="addSourceModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); overflow: hidden;">
            <div class="modal-header" style="background: #1e293b; color: #fff; padding: 20px 25px; border: none;">
                <h4 class="modal-title" id="addSourceModalLabel" style="font-weight: 700; display: flex; align-items: center; gap: 12px; margin: 0;">
                    <div style="background: rgba(255,255,255,0.1); width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fa fa-plus" style="font-size: 14px;"></i>
                    </div>
                    Add New Source
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #fff; opacity: 0.8; font-size: 24px; position: absolute; right: 20px; top: 20px;">&times;</button>
                </h4>
            </div>
            <div class="modal-body" style="padding: 30px; background: #fff;">
                <form id="add-source-form-main">
                    <div style="margin-bottom: 25px;">
                        <label style="font-weight: 700; color: #475569; display: block; margin-bottom: 12px; font-size: 11px; text-transform: uppercase; letter-spacing: 1px;">Source Name</label>
                        <input type="text" name="source_name" id="new_source_name" placeholder="e.g. Website, LinkedIn" required style="height: 50px; background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 14px; padding: 12px 20px; width: 100%; color: #0f172a; font-weight: 600; outline: none; transition: all 0.3s;" onfocus="this.style.borderColor='#6366f1'; this.style.boxShadow='0 0 0 4px rgba(99, 102, 241, 0.1)';" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none';">
                    </div>
                    <div style="text-align: right; gap: 12px; display: flex; justify-content: flex-end;">
                        <button type="button" data-dismiss="modal" style="background: #f1f5f9; color: #64748b; border: none; padding: 12px 25px; border-radius: 12px; font-weight: 700; transition: all 0.3s;" onmouseover="this.style.background='#e2e8f0'; this.style.color='#0f172a';" onmouseout="this.style.background='#f1f5f9'; this.style.color='#64748b';">Cancel</button>
                        <button type="submit" style="background: #10b981; color: #fff; border: none; padding: 12px 35px; border-radius: 12px; font-size: 14px; font-weight: 700; transition: all 0.3s; display: flex; align-items: center; gap: 8px;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 10px 15px -3px rgba(0,0,0,0.1)';" onmouseout="this.style.transform='none'; this.style.boxShadow='none';">
                            <i class="fa fa-save"></i> Save Source
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function showPremiumAlert(message) {
        let container = document.getElementById('toast-container-custom');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container-custom';
            container.style.cssText = 'position: fixed; top: 30px; right: 30px; z-index: 10000;';
            document.body.appendChild(container);
        }
        const toast = document.createElement('div');
        toast.style.cssText = 'background: #0f172a; color: #fff; padding: 18px 25px; border-radius: 16px; margin-bottom: 15px; display: flex; align-items: center; gap: 15px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2); transform: translateX(120%); transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1); border: 1px solid rgba(255,255,255,0.1); min-width: 300px;';
        toast.innerHTML = `
        <div style="background: #10b981; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <i class="fa fa-check" style="font-size: 14px;"></i>
        </div>
        <div style="flex-grow: 1;">
            <div style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 2px;">Success</div>
            <div style="font-size: 14px; font-weight: 600;">${message}</div>
        </div>
    `;
        container.appendChild(toast);
        setTimeout(() => toast.style.transform = 'translateX(0)', 10);
        setTimeout(() => {
            toast.style.transform = 'translateX(120%)';
            setTimeout(() => toast.remove(), 400);
        }, 4000);
    }

    $(document).ready(function() {
        $('#add-source-form-main').submit(function(e) {
            e.preventDefault();
            var source = $('#new_source_name').val();
            var submitBtn = $(this).find('button[type="submit"]');
            submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');

            $.ajax({
                url: "ajax/misc/ajax_add_source.php",
                method: "POST",
                data: {
                    source_name: source
                },
                dataType: "json",
                success: function(data) {
                    submitBtn.prop('disabled', false).html('<i class="fa fa-save"></i> Save Source');
                    if (data.status == "success") {
                        var newHtml = '<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">' +
                            '<label style="font-weight: 500; color: #475569; cursor: pointer; margin: 0;">' +
                            '<input type="checkbox" name="lead_source[]" value="' + data.name + '" checked style="margin-right: 8px; width: 16px; height: 16px; vertical-align: middle; accent-color: #4f46e5;"> ' + data.name +
                            '</label>' +
                            '<i class="fa fa-trash" style="color: #ef4444; cursor: pointer; font-size: 13px;" onclick="deleteSource(' + data.id + ', this)"></i>' +
                            '</div>';
                        $("#source_checkbox_container").append(newHtml);
                        $('#addSourceModal').modal('hide');
                        $('#new_source_name').val('');
                        showPremiumAlert("Source added and selected!");
                    } else {
                        alert("Error: " + data.message);
                    }
                },
                error: function() {
                    submitBtn.prop('disabled', false).html('<i class="fa fa-save"></i> Save Source');
                    alert("Connection Error.");
                }
            });
        });
    });

    function deleteSource(sourceId, element) {
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you really want to delete this source?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, delete it!',
            customClass: { popup: 'premium-card swal2-premium' }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "ajax/misc/ajax_delete_source.php",
                    method: "POST",
                    data: {
                        source_id: sourceId
                    },
                    dataType: "json",
                    success: function(data) {
                        if (data.status === "success") {
                            $(element).closest('div').remove();
                            showPremiumAlert("Source deleted successfully!");
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Connection Error', 'Failed to connect to the server.', 'error');
                    }
                });
            }
        });
    }
</script>
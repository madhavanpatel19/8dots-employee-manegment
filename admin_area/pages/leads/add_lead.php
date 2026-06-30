<?php
if (!isset($_SESSION['admin_email'])) {
    echo "<script>window.open('../../pages/auth/login.php','_self')</script>";
    exit;
}

global $con;

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

<style>
    .premium-label {
        font-weight: 600;
        display: block;
        margin-bottom: 8px;
    }

    .select2-container {
        box-sizing: border-box;
        display: inline-block;
        margin: 0;
        position: relative;
        vertical-align: middle
    }

    .select2-container .select2-selection--single {
        box-sizing: border-box;
        cursor: pointer;
        display: block;
        height: 28px;
        user-select: none;
        -webkit-user-select: none
    }

    .select2-container .select2-selection--single .select2-selection__rendered {
        display: block;
        padding-left: 8px;
        padding-right: 20px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap
    }

    .select2-container .select2-selection--single .select2-selection__clear {
        background-color: transparent;
        border: none;
        font-size: 1em
    }

    .select2-container[dir="rtl"] .select2-selection--single .select2-selection__rendered {
        padding-right: 8px;
        padding-left: 20px
    }

    .select2-container .select2-selection--multiple {
        box-sizing: border-box;
        cursor: pointer;
        display: block;
        min-height: 32px;
        user-select: none;
        -webkit-user-select: none
    }

    .select2-container .select2-selection--multiple .select2-selection__rendered {
        display: inline;
        list-style: none;
        padding: 0
    }

    .select2-container .select2-selection--multiple .select2-selection__clear {
        background-color: transparent;
        border: none;
        font-size: 1em
    }

    .select2-container .select2-search--inline .select2-search__field {
        box-sizing: border-box;
        border: none;
        font-size: 100%;
        margin-top: 5px;
        margin-left: 5px;
        padding: 0;
        max-width: 100%;
        resize: none;
        height: 18px;
        vertical-align: bottom;
        font-family: sans-serif;
        overflow: hidden;
        word-break: keep-all
    }

    .select2-container .select2-search--inline .select2-search__field::-webkit-search-cancel-button {
        -webkit-appearance: none
    }

    .select2-dropdown {
        background-color: white;
        border: 1px solid #aaa;
        border-radius: 4px;
        box-sizing: border-box;
        display: block;
        position: absolute;
        left: -100000px;
        width: 100%;
        z-index: 1051
    }

    .select2-results {
        display: block
    }

    .select2-results__options {
        list-style: none;
        margin: 0;
        padding: 0
    }

    .select2-results__option {
        padding: 6px;
        user-select: none;
        -webkit-user-select: none
    }

    .select2-results__option--selectable {
        cursor: pointer
    }

    .select2-container--open .select2-dropdown {
        left: 0
    }

    .select2-container--open .select2-dropdown--above {
        border-bottom: none;
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 0
    }

    .select2-container--open .select2-dropdown--below {
        border-top: none;
        border-top-left-radius: 0;
        border-top-right-radius: 0
    }

    .select2-search--dropdown {
        display: block;
        padding: 4px
    }

    .select2-search--dropdown .select2-search__field {
        padding: 4px;
        width: 100%;
        box-sizing: border-box
    }

    .select2-search--dropdown .select2-search__field::-webkit-search-cancel-button {
        -webkit-appearance: none
    }

    .select2-search--dropdown.select2-search--hide {
        display: none
    }

    .select2-close-mask {
        border: 0;
        margin: 0;
        padding: 0;
        display: block;
        position: fixed;
        left: 0;
        top: 0;
        min-height: 100%;
        min-width: 100%;
        height: auto;
        width: auto;
        opacity: 0;
        z-index: 99;
        background-color: #fff;
        filter: alpha(opacity=0)
    }

    .select2-hidden-accessible {
        border: 0 !important;
        clip: rect(0 0 0 0) !important;
        -webkit-clip-path: inset(50%) !important;
        clip-path: inset(50%) !important;
        height: 1px !important;
        overflow: hidden !important;
        padding: 0 !important;
        position: absolute !important;
        width: 1px !important;
        white-space: nowrap !important
    }

    .select2-container--default .select2-selection--single {
        background-color: #fff;
        border: 1px solid #aaa;
        border-radius: 4px
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #444;
        line-height: 28px
    }

    .select2-container--default .select2-selection--single .select2-selection__clear {
        cursor: pointer;
        float: right;
        font-weight: bold;
        height: 26px;
        margin-right: 20px;
        padding-right: 0px
    }

    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #999
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 26px;
        position: absolute;
        top: 1px;
        right: 1px;
        width: 20px
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow b {
        border-color: #888 transparent transparent transparent;
        border-style: solid;
        border-width: 5px 4px 0 4px;
        height: 0;
        left: 50%;
        margin-left: -4px;
        margin-top: -2px;
        position: absolute;
        top: 50%;
        width: 0
    }

    .select2-container--default[dir="rtl"] .select2-selection--single .select2-selection__clear {
        float: left
    }

    .select2-container--default[dir="rtl"] .select2-selection--single .select2-selection__arrow {
        left: 1px;
        right: auto
    }

    .select2-container--default.select2-container--disabled .select2-selection--single {
        background-color: #eee;
        cursor: default
    }

    .select2-container--default.select2-container--disabled .select2-selection--single .select2-selection__clear {
        display: none
    }

    .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
        border-color: transparent transparent #888 transparent;
        border-width: 0 4px 5px 4px
    }

    .select2-container--default .select2-selection--multiple {
        background-color: white;
        border: 1px solid #aaa;
        border-radius: 4px;
        cursor: text;
        padding-bottom: 5px;
        padding-right: 5px;
        position: relative
    }

    .select2-container--default .select2-selection--multiple.select2-selection--clearable {
        padding-right: 25px
    }

    .select2-container--default .select2-selection--multiple .select2-selection__clear {
        cursor: pointer;
        font-weight: bold;
        height: 20px;
        margin-right: 10px;
        margin-top: 5px;
        position: absolute;
        right: 0;
        padding: 1px
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #e4e4e4;
        border: 1px solid #aaa;
        border-radius: 4px;
        box-sizing: border-box;
        display: inline-flex;
        margin-left: 5px;
        margin-top: 5px;
        padding: 0;
        padding-left: 20px;
        position: relative;
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        vertical-align: bottom;
        white-space: nowrap
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__display {
        cursor: default;
        padding-left: 2px;
        padding-right: 5px
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        background-color: transparent;
        border: none;
        border-right: 1px solid #aaa;
        border-top-left-radius: 4px;
        border-bottom-left-radius: 4px;
        color: #999;
        cursor: pointer;
        font-size: 1em;
        font-weight: bold;
        padding: 0 4px;
        position: absolute;
        left: 0;
        top: 0
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover,
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:focus {
        background-color: #f1f1f1;
        color: #333;
        outline: none
    }

    .select2-container--default[dir="rtl"] .select2-selection--multiple .select2-selection__choice {
        margin-left: 5px;
        margin-right: auto
    }

    .select2-container--default[dir="rtl"] .select2-selection--multiple .select2-selection__choice__display {
        padding-left: 5px;
        padding-right: 2px
    }

    .select2-container--default[dir="rtl"] .select2-selection--multiple .select2-selection__choice__remove {
        border-left: 1px solid #aaa;
        border-right: none;
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        border-top-right-radius: 4px;
        border-bottom-right-radius: 4px
    }

    .select2-container--default[dir="rtl"] .select2-selection--multiple .select2-selection__clear {
        float: left;
        margin-left: 10px;
        margin-right: auto
    }

    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border: solid black 1px;
        outline: 0
    }

    .select2-container--default.select2-container--disabled .select2-selection--multiple {
        background-color: #eee;
        cursor: default
    }

    .select2-container--default.select2-container--disabled .select2-selection__choice__remove {
        display: none
    }

    .select2-container--default.select2-container--open.select2-container--above .select2-selection--single,
    .select2-container--default.select2-container--open.select2-container--above .select2-selection--multiple {
        border-top-left-radius: 0;
        border-top-right-radius: 0
    }

    .select2-container--default.select2-container--open.select2-container--below .select2-selection--single,
    .select2-container--default.select2-container--open.select2-container--below .select2-selection--multiple {
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 0
    }

    .select2-container--default .select2-search--dropdown .select2-search__field {
        border: 1px solid #aaa
    }

    .select2-container--default .select2-search--inline .select2-search__field {
        background: transparent;
        border: none;
        outline: 0;
        box-shadow: none;
        -webkit-appearance: textfield
    }

    .select2-container--default .select2-results>.select2-results__options {
        max-height: 200px;
        overflow-y: auto
    }

    .select2-container--default .select2-results__option .select2-results__option {
        padding-left: 1em
    }

    .select2-container--default .select2-results__option .select2-results__option .select2-results__group {
        padding-left: 0
    }

    .select2-container--default .select2-results__option .select2-results__option .select2-results__option {
        margin-left: -1em;
        padding-left: 2em
    }

    .select2-container--default .select2-results__option .select2-results__option .select2-results__option .select2-results__option {
        margin-left: -2em;
        padding-left: 3em
    }

    .select2-container--default .select2-results__option .select2-results__option .select2-results__option .select2-results__option .select2-results__option {
        margin-left: -3em;
        padding-left: 4em
    }

    .select2-container--default .select2-results__option .select2-results__option .select2-results__option .select2-results__option .select2-results__option .select2-results__option {
        margin-left: -4em;
        padding-left: 5em
    }

    .select2-container--default .select2-results__option .select2-results__option .select2-results__option .select2-results__option .select2-results__option .select2-results__option .select2-results__option {
        margin-left: -5em;
        padding-left: 6em
    }

    .select2-container--default .select2-results__option--group {
        padding: 0
    }

    .select2-container--default .select2-results__option--disabled {
        color: #999
    }

    .select2-container--default .select2-results__option--selected {
        background-color: #ddd
    }

    .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
        background-color: #5897fb;
        color: white
    }

    .select2-container--default .select2-results__group {
        cursor: default;
        display: block;
        padding: 6px
    }

    .select2-container--classic .select2-selection--single {
        background-color: #f7f7f7;
        border: 1px solid #aaa;
        border-radius: 4px;
        outline: 0;
        background-image: -webkit-linear-gradient(top, #fff 50%, #eee 100%);
        background-image: -o-linear-gradient(top, #fff 50%, #eee 100%);
        background-image: linear-gradient(to bottom, #fff 50%, #eee 100%);
        background-repeat: repeat-x;
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#FFFFFFFF', endColorstr='#FFEEEEEE', GradientType=0)
    }

    .select2-container--classic .select2-selection--single:focus {
        border: 1px solid #5897fb
    }

    .select2-container--classic .select2-selection--single .select2-selection__rendered {
        color: #444;
        line-height: 28px
    }

    .select2-container--classic .select2-selection--single .select2-selection__clear {
        cursor: pointer;
        float: right;
        font-weight: bold;
        height: 26px;
        margin-right: 20px
    }

    .select2-container--classic .select2-selection--single .select2-selection__placeholder {
        color: #999
    }

    .select2-container--classic .select2-selection--single .select2-selection__arrow {
        background-color: #ddd;
        border: none;
        border-left: 1px solid #aaa;
        border-top-right-radius: 4px;
        border-bottom-right-radius: 4px;
        height: 26px;
        position: absolute;
        top: 1px;
        right: 1px;
        width: 20px;
        background-image: -webkit-linear-gradient(top, #eee 50%, #ccc 100%);
        background-image: -o-linear-gradient(top, #eee 50%, #ccc 100%);
        background-image: linear-gradient(to bottom, #eee 50%, #ccc 100%);
        background-repeat: repeat-x;
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#FFEEEEEE', endColorstr='#FFCCCCCC', GradientType=0)
    }

    .select2-container--classic .select2-selection--single .select2-selection__arrow b {
        border-color: #888 transparent transparent transparent;
        border-style: solid;
        border-width: 5px 4px 0 4px;
        height: 0;
        left: 50%;
        margin-left: -4px;
        margin-top: -2px;
        position: absolute;
        top: 50%;
        width: 0
    }

    .select2-container--classic[dir="rtl"] .select2-selection--single .select2-selection__clear {
        float: left
    }

    .select2-container--classic[dir="rtl"] .select2-selection--single .select2-selection__arrow {
        border: none;
        border-right: 1px solid #aaa;
        border-radius: 0;
        border-top-left-radius: 4px;
        border-bottom-left-radius: 4px;
        left: 1px;
        right: auto
    }

    .select2-container--classic.select2-container--open .select2-selection--single {
        border: 1px solid #5897fb
    }

    .select2-container--classic.select2-container--open .select2-selection--single .select2-selection__arrow {
        background: transparent;
        border: none
    }

    .select2-container--classic.select2-container--open .select2-selection--single .select2-selection__arrow b {
        border-color: transparent transparent #888 transparent;
        border-width: 0 4px 5px 4px
    }

    .select2-container--classic.select2-container--open.select2-container--above .select2-selection--single {
        border-top: none;
        border-top-left-radius: 0;
        border-top-right-radius: 0;
        background-image: -webkit-linear-gradient(top, #fff 0%, #eee 50%);
        background-image: -o-linear-gradient(top, #fff 0%, #eee 50%);
        background-image: linear-gradient(to bottom, #fff 0%, #eee 50%);
        background-repeat: repeat-x;
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#FFFFFFFF', endColorstr='#FFEEEEEE', GradientType=0)
    }

    .select2-container--classic.select2-container--open.select2-container--below .select2-selection--single {
        border-bottom: none;
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 0;
        background-image: -webkit-linear-gradient(top, #eee 50%, #fff 100%);
        background-image: -o-linear-gradient(top, #eee 50%, #fff 100%);
        background-image: linear-gradient(to bottom, #eee 50%, #fff 100%);
        background-repeat: repeat-x;
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#FFEEEEEE', endColorstr='#FFFFFFFF', GradientType=0)
    }

    .select2-container--classic .select2-selection--multiple {
        background-color: white;
        border: 1px solid #aaa;
        border-radius: 4px;
        cursor: text;
        outline: 0;
        padding-bottom: 5px;
        padding-right: 5px
    }

    .select2-container--classic .select2-selection--multiple:focus {
        border: 1px solid #5897fb
    }

    .select2-container--classic .select2-selection--multiple .select2-selection__clear {
        display: none
    }

    .select2-container--classic .select2-selection--multiple .select2-selection__choice {
        background-color: #e4e4e4;
        border: 1px solid #aaa;
        border-radius: 4px;
        display: inline-block;
        margin-left: 5px;
        margin-top: 5px;
        padding: 0
    }

    .select2-container--classic .select2-selection--multiple .select2-selection__choice__display {
        cursor: default;
        padding-left: 2px;
        padding-right: 5px
    }

    .select2-container--classic .select2-selection--multiple .select2-selection__choice__remove {
        background-color: transparent;
        border: none;
        border-top-left-radius: 4px;
        border-bottom-left-radius: 4px;
        color: #888;
        cursor: pointer;
        font-size: 1em;
        font-weight: bold;
        padding: 0 4px
    }

    .select2-container--classic .select2-selection--multiple .select2-selection__choice__remove:hover {
        color: #555;
        outline: none
    }

    .select2-container--classic[dir="rtl"] .select2-selection--multiple .select2-selection__choice {
        margin-left: 5px;
        margin-right: auto
    }

    .select2-container--classic[dir="rtl"] .select2-selection--multiple .select2-selection__choice__display {
        padding-left: 5px;
        padding-right: 2px
    }

    .select2-container--classic[dir="rtl"] .select2-selection--multiple .select2-selection__choice__remove {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        border-top-right-radius: 4px;
        border-bottom-right-radius: 4px
    }

    .select2-container--classic.select2-container--open .select2-selection--multiple {
        border: 1px solid #5897fb
    }

    .select2-container--classic.select2-container--open.select2-container--above .select2-selection--multiple {
        border-top: none;
        border-top-left-radius: 0;
        border-top-right-radius: 0
    }

    .select2-container--classic.select2-container--open.select2-container--below .select2-selection--multiple {
        border-bottom: none;
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 0
    }

    .select2-container--classic .select2-search--dropdown .select2-search__field {
        border: 1px solid #aaa;
        outline: 0
    }

    .select2-container--classic .select2-search--inline .select2-search__field {
        outline: 0;
        box-shadow: none
    }

    .select2-container--classic .select2-dropdown {
        background-color: #fff;
        border: 1px solid transparent
    }

    .select2-container--classic .select2-dropdown--above {
        border-bottom: none
    }

    .select2-container--classic .select2-dropdown--below {
        border-top: none
    }

    .select2-container--classic .select2-results>.select2-results__options {
        max-height: 200px;
        overflow-y: auto
    }

    .select2-container--classic .select2-results__option--group {
        padding: 0
    }

    .select2-container--classic .select2-results__option--disabled {
        color: grey
    }

    .select2-container--classic .select2-results__option--highlighted.select2-results__option--selectable {
        background-color: #3875d7;
        color: #fff
    }

    .select2-container--classic .select2-results__group {
        cursor: default;
        display: block;
        padding: 6px
    }

    .select2-container--classic.select2-container--open .select2-dropdown {
        border-color: #5897fb
    }
</style>
<div class="page-wrapper premium-ui-enabled">
    <form method="POST" id="add_lead_form">
        <!-- Card 1: Lead Information -->
        <div class="premium-card" style="margin: 0 30px 30px 30px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); background: #fff;">
            <div style="padding: 25px 30px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; gap: 15px;">
                <div style="width: 32px; height: 32px; background: #DF2127; color: #fff; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px;">
                    1
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 18px; font-weight: 700; color: #1e293b;">Lead Information</h3>
                    <p style="margin: 4px 0 0 0; font-size: 13px; color: #64748b;">Basic details about the lead</p>
                </div>
            </div>

            <div style="padding: 30px;">
                <div class="row">
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label class="premium-label" style="font-size: 14px; color: #334155;">Client Name <span style="color: #ef4444;">*</span></label>
                                    <input type="text" name="client_name" class="p-input-premium" style="height: 48px;" placeholder="Full Name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label class="premium-label" style="font-size: 14px; color: #334155;">Phone No <span style="color: #ef4444;">*</span></label>
                                    <div style="position: relative;">
                                        <i class="fa fa-phone" style="position: absolute; left: 15px; top: 16px; color: #94a3b8; font-size: 14px;"></i>
                                        <input type="text" name="phone" class="p-input-premium" style="height: 48px; padding-left: 40px;" placeholder="Mobile Number" required minlength="10" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label class="premium-label" style="font-size: 14px; color: #334155;">Email ID</label>
                                    <div style="position: relative;">
                                        <i class="fa fa-envelope-o" style="position: absolute; left: 15px; top: 16px; color: #94a3b8; font-size: 14px;"></i>
                                        <input type="email" name="email" class="p-input-premium" style="height: 48px; padding-left: 40px;" placeholder="email@example.com">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label class="premium-label" style="font-size: 14px; color: #334155;">Company Name</label>
                                    <input type="text" name="company_name" class="p-input-premium" style="height: 48px;" placeholder="Organization Name">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Project Details -->
        <div class="premium-card" style="margin: 0 30px 30px 30px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); background: #fff;">
            <div style="padding: 25px 30px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; gap: 15px;">
                <div style="width: 32px; height: 32px; background: #DF2127; color: #fff; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px;">
                    2
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 18px; font-weight: 700; color: #1e293b;">Project Details</h3>
                    <p style="margin: 4px 0 0 0; font-size: 13px; color: #64748b;">Description and remarks for the lead</p>
                </div>
            </div>

            <div style="padding: 30px;">
                <div class="row">
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label class="premium-label" style="font-size: 14px; color: #334155;">Project Name</label>
                                    <input type="text" name="project_name" class="p-input-premium" style="height: 48px;" placeholder="Project Name">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label class="premium-label" style="font-size: 14px; color: #334155;">Description</label>
                                    <textarea name="description" class="p-input-premium" style="min-height: 120px; padding: 15px; resize: vertical;" placeholder="Detailed lead requirements..."></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label class="premium-label" style="font-size: 14px; color: #334155;">Internal Note</label>
                                    <textarea name="remark" class="p-input-premium" style="min-height: 120px; padding: 15px; resize: vertical;" placeholder="Initial internal remarks..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: Financials & Status -->
        <div class="premium-card" style="margin: 0 30px 30px 30px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); background: #fff;">
            <div style="padding: 25px 30px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; gap: 15px;">
                <div style="width: 32px; height: 32px; background: #DF2127; color: #fff; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px;">
                    3
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 18px; font-weight: 700; color: #1e293b;">Financials & Status</h3>
                    <p style="margin: 4px 0 0 0; font-size: 13px; color: #64748b;">Budget, lead source, and follow-up tracking</p>
                </div>
            </div>

            <div style="padding: 30px;">
                <div class="row">
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label class="premium-label" style="font-size: 14px; color: #334155;">Budget</label>
                                    <div style="display: flex; gap: 10px;">
                                        <select name="currency" class="p-input-premium" style="height: 48px; width: 120px; flex-shrink: 0;">
                                            <option value="INR" selected>₹ INR</option>
                                            <option value="USD">$ USD</option>
                                            <option value="EUR">€ EUR</option>
                                            <option value="GBP">£ GBP</option>
                                            <option value="AED">د.إ AED</option>
                                        </select>
                                        <input type="text" name="budget" class="p-input-premium" style="height: 48px;" placeholder="e.g. 50k, 1 Lac">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label class="premium-label" style="font-size: 14px; color: #334155; display: flex; justify-content: space-between; align-items: center;">
                                        Lead Source
                                        <span class="add-source-btn" data-toggle="modal" data-target="#addSourceModal" style="color: #10b981; font-size: 12px; cursor: pointer; padding: 4px 10px; background: #ecfdf5; border-radius: 6px; font-weight: 700;">
                                            <i class="fa fa-plus"></i> New
                                        </span>
                                    </label>
                                    <div style="background: #f8fafc; padding: 15px; border-radius: 12px; border: 1.5px solid #e2e8f0; min-height: 100px; max-height: 150px; overflow-y: auto;" id="source_checkbox_container">
                                        <?php
                                        $get_sources = "SELECT * FROM lead_sources ORDER BY source_name ASC";
                                        $run_sources = mysqli_query($con, $get_sources);
                                        while ($row_s = mysqli_fetch_array($run_sources)):
                                            $s_name = $row_s['source_name'];
                                            $s_id = $row_s['id'];
                                        ?>
                                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                                <label style="font-weight: 500; color: #475569; cursor: pointer; margin: 0; display: flex; align-items: center; gap: 8px;">
                                                    <input type="checkbox" name="lead_source[]" value="<?php echo htmlspecialchars($s_name); ?>" style="width: 16px; height: 16px; accent-color: #DF2127;">
                                                    <?php echo htmlspecialchars($s_name); ?>
                                                </label>
                                                <i class="fa fa-trash" style="color: #ef4444; cursor: pointer; font-size: 13px; padding: 5px;" onclick="deleteSource(<?php echo $s_id; ?>, this)"></i>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>
                                    <small style="color: #94a3b8; font-size: 11px; margin-top: 8px; display: block;">Select all that apply</small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label class="premium-label" style="font-size: 14px; color: #334155;">Status</label>
                                    <select name="status" class="p-input-premium" style="height: 48px;">
                                        <option value="active">Active</option>
                                        <option value="future">Future</option>
                                        <option value="expired">Expired</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label class="premium-label" style="font-size: 14px; color: #334155;">Next Follow-up</label>
                                    <input type="date" name="followup_date" class="p-input-premium" style="height: 48px;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div style="margin: 0 30px 40px 30px; display: flex; justify-content: flex-end; gap: 15px;">
            <a href="index.php?leads" class="btn-premium-cancel" style="text-decoration: none;">Cancel</a>
            <button type="submit" name="save_lead" class="btn-premium-add">
                <i class="fa fa-save"></i> Save Lead Information
            </button>
        </div>
    </form>
</div>
<!-- Add Source Modal -->
<div class="modal fade" id="addSourceModal" tabindex="-1" role="dialog" aria-labelledby="addSourceModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); overflow: hidden;">
            <div class="modal-header" style="background: #ffedeb; color: #1e293b; padding: 20px 25px; border: none; position: relative;">
                <button class="btn-modal-close" data-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times"></i>
                </button>
                <h4 class="modal-title" id="addIndustryModalLabel" style="font-weight: 700; display: flex; align-items: center; gap: 12px; margin: 0;">
                    <div style="background: #dd2127; color:white; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fa fa-plus" style="font-size: 14px;"></i>
                    </div>
                    Add New Source
                </h4>
            </div>
            <div class="modal-body" style="padding: 30px; background: #fff;">
                <form id="add-source-form-main" onsubmit="event.preventDefault();">
                    <div style="margin-bottom: 25px;">
                        <label style="font-weight: 700; color: #475569; display: block; margin-bottom: 12px; font-size: 11px; text-transform: uppercase; letter-spacing: 1px;">Source Name</label>
                        <input type="text" name="source_name" id="new_source_name" placeholder="e.g. Website, LinkedIn" required style="height: 50px; background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 14px; padding: 12px 20px; width: 100%; color: #0f172a; font-weight: 600; outline: none; transition: all 0.3s;" onfocus="this.style.borderColor='#6366f1'; this.style.boxShadow='0 0 0 4px rgba(99, 102, 241, 0.1)';" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none';">
                    </div>
                    <div style="text-align: right; gap: 12px; display: flex; justify-content: flex-end;">
                        <button type="button" data-dismiss="modal" class="btn-premium-cancel">Cancel</button>
                        <button type="submit" class="btn-premium-add">
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
                        Swal.fire('Notification', "Error: " + data.message, 'error');
                    }
                },
                error: function() {
                    submitBtn.prop('disabled', false).html('<i class="fa fa-save"></i> Save Source');
                    Swal.fire('Notification', "Connection Error.", 'error');
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
            customClass: {
                popup: 'premium-card swal2-premium'
            }
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
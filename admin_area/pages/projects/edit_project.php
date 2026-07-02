<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<style>
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

<style>
    .select2-container {
        width: 100% !important;
    }

    .select2-container--default .select2-selection--single,
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #e2e8f0 !important;
        border-radius: 8px !important;
        min-height: 48px !important;
        background-color: #fff !important;
        display: flex;
        align-items: center;
        padding: 0 8px;
        transition: all 0.3s ease;
    }

    .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #DF2127 !important;
        box-shadow: 0 0 0 4px rgba(197, 197, 197, 0.1) !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #334155 !important;
        line-height: normal !important;
        padding-left: 8px;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 46px !important;
        right: 10px !important;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #eff6ff !important;
        border: 1px solid #bfdbfe !important;
        border-radius: 6px !important;
        color: #1e3a8a !important;
        padding: 4px 8px 4px 24px !important;
        /* Added space on the left for the X icon */
        margin-top: 6px !important;
        position: relative !important;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #1e3a8a !important;
        border-right: 1px solid rgba(30, 58, 138, 0.2) !important;
        /* Subtle separator line */
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        bottom: 0 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 0 6px !important;
        margin: 0 !important;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        background-color: rgba(30, 58, 138, 0.1) !important;
        color: #ef4444 !important;
        /* Turn red on hover */
    }

    .select2-search--inline .select2-search__field {
        margin-top: 8px !important;
        font-family: inherit !important;
        color: #334155 !important;
    }
</style>
<?php
if (!isset($con)) {
    include(__DIR__ . '/../../includes/db.php');
}

$project_id = isset($_GET['edit_project']) ? intval($_GET['edit_project']) : 0;
if ($project_id == 0) {
    echo "<script>window.location.href='index.php?projects';</script>";
    exit();
}

// Fetch existing project data
$get_project = "SELECT * FROM client_projects WHERE id = $project_id";
$run_project = mysqli_query($con, $get_project);
if (mysqli_num_rows($run_project) == 0) {
    echo "<script>window.location.href='index.php?projects';</script>";
    exit();
}
$project_data = mysqli_fetch_assoc($run_project);

// Parse existing fields
$existing_sources = explode(', ', $project_data['source'] ?? '');
$existing_employees = explode(',', $project_data['assigned_employees'] ?? '');
$existing_users = explode(',', $project_data['assigned_users'] ?? '');
$existing_admins = explode(',', $project_data['assigned_admins'] ?? '');

// File Upload Function for Projects
if (!function_exists('handleProjectImageUpload')) {
    function handleProjectImageUpload($fileArray, $targetDir = __DIR__ . "/../../uploads/project_images/")
    {
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        if (isset($fileArray) && $fileArray['error'] == 0) {
            $file_name = $fileArray['name'];
            $tmp_name = $fileArray['tmp_name'];
            $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $new_name = time() . '_' . rand(1000, 9999) . '.' . $ext;
            $target_path = $targetDir . $new_name;

            if (move_uploaded_file($tmp_name, $target_path)) {
                return $new_name;
            }
        }
        return '';
    }
}

$success = false;
$error = '';

if (isset($_POST['submit_project'])) {
    $project_name = mysqli_real_escape_string($con, $_POST['project_name']);
    $client_id = mysqli_real_escape_string($con, $_POST['client_id']);
    $project_desc = mysqli_real_escape_string($con, $_POST['project_desc']);
    $start_date = mysqli_real_escape_string($con, $_POST['start_date']);
    $deadline = mysqli_real_escape_string($con, $_POST['deadline']);
    $status = mysqli_real_escape_string($con, $_POST['status']);

    // Budget Fields
    $currency = mysqli_real_escape_string($con, $_POST['currency']);
    $budget = mysqli_real_escape_string($con, $_POST['budget']);
    if (empty($budget)) $budget = 0;

    $assigned_employees = isset($_POST['assigned_employees']) ? implode(',', $_POST['assigned_employees']) : '';
    $assigned_employees = mysqli_real_escape_string($con, $assigned_employees);

    $assigned_users = isset($_POST['assigned_users']) ? implode(',', $_POST['assigned_users']) : '';
    $assigned_users = mysqli_real_escape_string($con, $assigned_users);

    $assigned_admins = isset($_POST['assigned_admins']) ? implode(',', $_POST['assigned_admins']) : '';
    $assigned_admins = mysqli_real_escape_string($con, $assigned_admins);

    $project_image = handleProjectImageUpload($_FILES['project_image']);
    if (empty($project_image)) {
        $project_image = $project_data['project_image']; // keep existing if no new image
    }

    $source = isset($_POST['project_source']) ? implode(', ', $_POST['project_source']) : '';
    $source = mysqli_real_escape_string($con, $source);

    $update_project = "UPDATE client_projects SET 
        client_id = '$client_id',
        project_name = '$project_name',
        project_date = '$start_date',
        deadline = '$deadline',
        status = '$status',
        project_desc = '$project_desc',
        project_image = '$project_image',
        assigned_employees = '$assigned_employees',
        assigned_users = '$assigned_users',
        assigned_admins = '$assigned_admins',
        currency = '$currency',
        budget = '$budget',
        source = '$source'
        WHERE id = $project_id";

    if (mysqli_query($con, $update_project)) {

        // Re-insert Phases
        if (isset($_POST['phase_name']) && is_array($_POST['phase_name'])) {
            // Delete old phases
            mysqli_query($con, "DELETE FROM project_budget_phases WHERE project_id = $project_id");

            foreach ($_POST['phase_name'] as $key => $p_name) {
                $p_name_esc = mysqli_real_escape_string($con, $p_name);
                $p_desc_esc = mysqli_real_escape_string($con, $_POST['phase_desc'][$key]);
                $p_date_esc = mysqli_real_escape_string($con, $_POST['phase_date'][$key]);
                $p_cost_esc = mysqli_real_escape_string($con, $_POST['phase_cost'][$key]);

                if (!empty($p_name_esc)) {
                    $insert_phase = "INSERT INTO project_budget_phases (project_id, phase_name, description, expected_date, cost) 
                                     VALUES ('$project_id', '$p_name_esc', '$p_desc_esc', " . (!empty($p_date_esc) ? "'$p_date_esc'" : "NULL") . ", '$p_cost_esc')";
                    mysqli_query($con, $insert_phase);
                }
            }
        }

        // Insert Documents (we will NOT delete old documents here to preserve files. The UI can be extended later for doc deletion)
        if (isset($_FILES['doc_file']) && is_array($_FILES['doc_file']['name'])) {
            $docDir = __DIR__ . "/../../uploads/project_documents/";
            if (!is_dir($docDir)) mkdir($docDir, 0777, true);
            foreach ($_FILES['doc_file']['name'] as $key => $fileName) {
                $docName = mysqli_real_escape_string($con, $_POST['doc_name'][$key]);
                $tmpName = $_FILES['doc_file']['tmp_name'][$key];
                $error_code = $_FILES['doc_file']['error'][$key];

                if ($error_code == 0 && !empty($fileName)) {
                    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                    $newName = time() . '_' . rand(1000, 9999) . '.' . $ext;
                    if (move_uploaded_file($tmpName, $docDir . $newName)) {
                        $q = "INSERT INTO project_documents (project_id, document_name, file_path) VALUES ('$project_id', '$docName', '$newName')";
                        mysqli_query($con, $q);
                    }
                }
            }
        }

        // Re-insert Links
        if (isset($_POST['link_name']) && is_array($_POST['link_name'])) {
            mysqli_query($con, "DELETE FROM project_links WHERE project_id = $project_id");
            foreach ($_POST['link_name'] as $key => $lName) {
                $link_name_esc = mysqli_real_escape_string($con, $lName);
                $link_url_esc = mysqli_real_escape_string($con, $_POST['link_url'][$key]);
                if (!empty($link_name_esc) && !empty($link_url_esc)) {
                    $q = "INSERT INTO project_links (project_id, link_name, link_url) VALUES ('$project_id', '$link_name_esc', '$link_url_esc')";
                    mysqli_query($con, $q);
                }
            }
        }

        $success = true;
    } else {
        $error = "Error: " . mysqli_error($con);
    }
}

if ($success): ?>
    <link rel="stylesheet" href="css/success_notification.css">
    <div class="success-modal-overlay" id="successModal">
        <div class="success-modal-content">
            <div class="success-icon-wrapper">
                <i class="fa fa-check"></i>
            </div>
            <h2 class="success-title">Success!</h2>
            <p class="success-message">Project <strong><?php echo htmlspecialchars($project_name); ?></strong> has been updated successfully.</p>
            <div class="success-actions">
                <a href="index.php?projects" class="btn-success-go">
                    <i class="fa fa-list"></i> Go to Projects
                </a>
            </div>
            <div class="success-timer-bar" style="animation-duration: 4s;"></div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('successModal');
            setTimeout(() => {
                modal.classList.add('active');
            }, 100);
            setTimeout(() => {
                window.location.href = 'index.php?projects';
            }, 4000);
        });
    </script>
<?php exit();
endif; ?>

<?php
$current_admin_id = 0;
if (isset($_SESSION['admin_email'])) {
    $admin_email = mysqli_real_escape_string($con, $_SESSION['admin_email']);
    $get_curr_admin = mysqli_query($con, "SELECT admin_id FROM admins WHERE admin_email = '$admin_email' LIMIT 1");
    if ($row_curr = mysqli_fetch_assoc($get_curr_admin)) {
        $current_admin_id = $row_curr['admin_id'];
    }
}

if (empty(array_filter($existing_admins))) {
    $existing_admins = [$current_admin_id];
}

$get_clients = "SELECT id, name, image FROM clients ORDER BY name ASC";
$run_clients = mysqli_query($con, $get_clients);

$get_emps = "SELECT id, employee_image, name FROM emp_list ORDER BY name ASC";
$run_emps = mysqli_query($con, $get_emps);

$get_users = "SELECT id, employee_image, name FROM emp_list ORDER BY name ASC";
$run_users = mysqli_query($con, $get_users);

$get_admins = "SELECT admin_id, admin_image, admin_name FROM admins ORDER BY admin_name ASC";
$run_admins = mysqli_query($con, $get_admins);
?>

<div class="page-wrapper premium-ui-enabled">
    <!-- <div style="padding: 20px 30px; margin-bottom: 20px;">
        <a href="index.php?projects" style="color: #6366f1; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fa fa-chevron-left" style="font-size: 12px;"></i> Back to Projects
        </a>
        <h1 style="font-size: 28px; font-weight: 800; color: #0f172a; margin: 15px 0 5px 0;">Add New Project</h1>
        <p style="color: #64748b; font-size: 15px; margin: 0;">Create a new project and track it efficiently</p>
    </div> -->

    <form method="POST" id="add_project_form" enctype="multipart/form-data">

        <div class="premium-card" style="margin: 0 30px 30px 30px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); background: #fff;">
            <div style="padding: 25px 30px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; gap: 15px;">
                <div style="width: 32px; height: 32px; background: #DF2127; color: #fff; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px;">
                    1
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 18px; font-weight: 700; color: #1e293b;">Project Information</h3>
                    <p style="margin: 4px 0 0 0; font-size: 13px; color: #64748b;">Basic details about the project</p>
                </div>
            </div>

            <div style="padding: 30px;">

                <div class="row">
                    <!-- Image Upload -->
                    <div class="col-md-4">
                        <label class="premium-label" style="font-size: 14px; color: #334155;">Project Image</label>
                        <div class="upload-area" style="border: 2px dashed #cbd5e1; border-radius: 12px; padding: 30px; text-align: center; background: #f8fafc; position: relative; transition: 0.3s;">
                            <div style="width: 48px; height: 48px; background: #eff6ff; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; color: #DF2127; font-size: 20px; margin-bottom: 15px;">
                                <i class="fa fa-image"></i>
                            </div>
                            <h4 style="margin: 0 0 5px 0; font-size: 15px; font-weight: 700; color: #1e293b;">Upload project image</h4>
                            <p style="margin: 0 0 15px 0; font-size: 12px; color: #64748b;">JPG, PNG up to 5MB</p>

                            <label for="project_image" class="btn btn-outline-primary" style="background: #fff; border: 1px solid #e2e8f0; color: #DF2127; font-weight: 600; padding: 8px 20px; border-radius: 8px; cursor: pointer;">
                                Choose File
                            </label>
                            <input type="file" name="project_image" id="project_image" style="display: none;" accept="image/*" onchange="previewImage(this)">

                            <!-- Preview overlay -->
                            <?php $has_img = !empty($project_data['project_image']); ?>
                            <div id="image_preview_container" style="display: <?php echo $has_img ? 'block' : 'none'; ?>; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: #fff; border-radius: 12px; overflow: hidden;">
                                <img id="project_preview" src="<?php echo $has_img ? 'uploads/project_images/' . $project_data['project_image'] : ''; ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                <div style="position: absolute; top: 10px; right: 10px; background: rgba(0,0,0,0.5); color: #fff; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer;" onclick="removeImage()">
                                    <i class="fa fa-times"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label class="premium-label" style="font-size: 14px; color: #334155;">Project Name <span style="color: #ef4444;">*</span></label>
                                    <input type="text" name="project_name" class="p-input-premium" style="height: 48px;" placeholder="Enter project name" value="<?php echo htmlspecialchars($project_data['project_name']); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="premium-label">
                                        Client Name <span style="color:red">*</span>
                                    </label>

                                    <select id="clientSelect" name="client_id" required>
                                        <option value=""></option>

                                        <?php while ($client = mysqli_fetch_assoc($run_clients)) {
                                            $client_img = !empty($client['image']) ? 'uploads/client_images/' . $client['image'] : 'admin_images/default.png';
                                            $selected = ($client['id'] == $project_data['client_id']) ? 'selected' : '';
                                        ?>
                                            <option value="<?php echo $client['id']; ?>"
                                                data-image="<?php echo $client_img; ?>" <?php echo $selected; ?>>
                                                <?php echo htmlspecialchars($client['name']); ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label class="premium-label" style="font-size: 14px; color: #334155;">Project Description</label>
                                    <textarea name="project_desc" class="p-input-premium" style="height: 100px; border-radius: 8px; border: 1px solid #e2e8f0; resize: none;" placeholder="Enter project description..."><?php echo htmlspecialchars($project_data['project_desc']); ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row" style="margin-top: 10px;">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="premium-label" style="font-size: 14px; color: #334155;">Start Date <span style="color: #ef4444;">*</span></label>
                            <input type="date" name="start_date" class="p-input-premium" style="height: 48px; border-radius: 8px; border: 1px solid #e2e8f0;" value="<?php echo $project_data['project_date']; ?>" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="premium-label" style="font-size: 14px; color: #334155;">Deadline <span style="color: #ef4444;">*</span></label>
                            <input type="date" name="deadline" class="p-input-premium" style="height: 48px; border-radius: 8px; border: 1px solid #e2e8f0;" value="<?php echo $project_data['deadline']; ?>" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="premium-label" style="font-size: 14px; color: #334155;">Initial Status <span style="color: #ef4444;">*</span></label>
                            <select name="status" class="p-input-premium" style="height: 48px; border-radius: 8px; border: 1px solid #e2e8f0;" required>
                                <option value="">Select status</option>
                                <option value="Active" <?php echo ($project_data['status'] == 'Active') ? 'selected' : ''; ?>>Active</option>
                                <option value="Pending" <?php echo ($project_data['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                <option value="Completed" <?php echo ($project_data['status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row" style="margin-top: 10px;">
                    <div class="col-md-6">
                        <!-- Assign Employees -->
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label class="premium-label" style="font-size:14px; color:#334155;">
                                Assign Employees <span style="color:#ef4444;">*</span>
                            </label>
                            <select id="employeeSelect" name="assigned_employees[]" multiple required>
                                <?php while ($emp = mysqli_fetch_assoc($run_emps)) {
                                    $emp_img = !empty($emp['employee_image']) ? 'uploads/' . $emp['employee_image'] : 'admin_images/default.png';
                                    $emp_selected = in_array($emp['id'], $existing_employees) ? 'selected' : '';
                                ?>
                                    <option value="<?php echo $emp['id']; ?>" data-image="<?php echo $emp_img; ?>" <?php echo $emp_selected; ?>>
                                        <?php echo htmlspecialchars($emp['name']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                            <small style="color:#64748b; font-size:12px; margin-top:4px; display:block;">
                                Select one or more employees for this project.
                            </small>
                        </div>

                        <!-- Assign Admin -->
                        <div class="form-group">
                            <label class="premium-label" style="font-size:14px; color:#334155;">
                                Assign Admin
                            </label>
                            <select id="adminSelect" name="assigned_admins[]" multiple>
                                <?php while ($adm = mysqli_fetch_assoc($run_admins)) {
                                    $adm_img = !empty($adm['admin_image']) ? 'admin_images/' . $adm['admin_image'] : 'admin_images/default.png';
                                    $adm_selected = in_array($adm['admin_id'], $existing_admins) ? 'selected' : '';
                                ?>
                                    <option value="<?php echo $adm['admin_id']; ?>" data-image="<?php echo $adm_img; ?>" <?php echo $adm_selected; ?>>
                                        <?php echo htmlspecialchars($adm['admin_name']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                            <small style="color:#64748b; font-size:12px; margin-top:4px; display:block;">
                                Select admins to assign to this project.
                            </small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                                <label class="premium-label" style="font-size:14px; color:#334155; margin: 0;">Source</label>
                                <button type="button" class="btn btn-xs btn-success" data-toggle="modal" data-target="#addSourceModal" style="border-radius: 6px; padding: 2px 8px; font-size: 10px; font-weight: 700; background: #10b981; border: none; box-shadow: 0 2px 4px rgba(16,185,129,0.2);">
                                    <i class="fa fa-plus"></i> New
                                </button>
                            </div>
                            <div style="background: #fff; padding: 10px; border-radius: 8px; border: 1px solid #e2e8f0; min-height: 48px; max-height: 150px; overflow-y: auto;" id="source_checkbox_container">
                                <?php
                                $get_sources = "SELECT * FROM lead_sources ORDER BY source_name ASC";
                                $run_sources = mysqli_query($con, $get_sources);
                                while ($row_s = mysqli_fetch_array($run_sources)):
                                    $s_name = $row_s['source_name'];
                                    $s_id = $row_s['id'];
                                    $s_checked = in_array($s_name, $existing_sources) ? 'checked' : '';
                                ?>
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                                        <label style="font-weight: 500; color: #475569; cursor: pointer; margin: 0;">
                                            <input type="checkbox" name="project_source[]" value="<?php echo htmlspecialchars($s_name); ?>" <?php echo $s_checked; ?> style="margin-right: 8px; width: 16px; height: 16px; vertical-align: middle; accent-color: #DF2127;"> <?php echo htmlspecialchars($s_name); ?>
                                        </label>
                                        <i class="fa fa-trash" style="color: #ef4444; cursor: pointer; font-size: 13px;" onclick="deleteSource(<?php echo $s_id; ?>, this)"></i>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                            <small style="color:#64748b; font-size:12px; margin-top:4px; display:block;">Select all that apply</small>
                        </div>
                    </div>
                </div>

            </div>
        </div> <!-- End First Card -->

        <!-- Second Card: Project Budget -->
        <div class="premium-card" style="margin: 0 30px 30px 30px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); background: #fff;">
            <div style="padding: 25px 30px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; gap: 15px;">
                <div style="width: 32px; height: 32px; background: #DF2127; color: #fff; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px;">
                    2
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 18px; font-weight: 700; color: #1e293b;">Project Budget</h3>
                    <p style="margin: 4px 0 0 0; font-size: 13px; color: #64748b;">Define total budget and phase-wise deliverables</p>
                </div>
            </div>

            <div style="padding: 30px;">
                <div class="row" style="margin-bottom: 25px;">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="premium-label">Currency <span style="color:#ef4444">*</span></label>
                            <select name="currency" class="p-input-premium" style="height:48px; border-radius:8px; border:1px solid #e2e8f0; width:100%;" required>
                                <option value="INR" <?php echo ($project_data['currency'] == 'INR') ? 'selected' : ''; ?>>INR - Indian Rupee (₹)</option>
                                <option value="USD" <?php echo ($project_data['currency'] == 'USD') ? 'selected' : ''; ?>>USD - US Dollar ($)</option>
                                <option value="EUR" <?php echo ($project_data['currency'] == 'EUR') ? 'selected' : ''; ?>>EUR - Euro (€)</option>
                                <option value="GBP" <?php echo ($project_data['currency'] == 'GBP') ? 'selected' : ''; ?>>GBP - British Pound (£)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="premium-label">Total Project Budget <span style="color:#ef4444">*</span></label>
                            <div style="display:flex; align-items:center; border:1px solid #e2e8f0; border-radius:8px; overflow:hidden;">
                                <div style="background:#f8fafc; padding:0 15px; height:48px; display:flex; align-items:center; border-right:1px solid #e2e8f0; color:#64748b; font-weight:600;" id="currency_symbol">₹</div>
                                <input type="number" name="budget" id="total_budget" class="p-input-premium" style="height:48px; border:none; width:100%; outline:none; padding:0 15px;" placeholder="Enter total budget" value="<?php echo $project_data['budget']; ?>" required min="0" step="0.01">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Phase Wise Breakdown -->
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
                    <div style="padding: 15px 20px; border-bottom: 1px solid #e2e8f0; background: #f1f5f9;">
                        <h4 style="margin: 0; font-size: 15px; font-weight: 700; color: #334155;">Phase Wise Breakdown</h4>
                    </div>

                    <div style="padding: 0;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th style="padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase;">Phase / Deliverable</th>
                                    <th style="padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase;">Description</th>
                                    <th style="padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase;">Expected Date</th>
                                    <th style="padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase;">Cost</th>
                                    <th style="padding: 12px 20px; text-align: center; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="phase_container">
                                <?php
                                $get_phases = "SELECT * FROM project_budget_phases WHERE project_id = $project_id ORDER BY id ASC";
                                $run_phases = mysqli_query($con, $get_phases);
                                if (mysqli_num_rows($run_phases) > 0) {
                                    while ($p = mysqli_fetch_assoc($run_phases)) {
                                ?>
                                        <tr class="phase-row" style="border-top: 1px solid #e2e8f0;">
                                            <td style="padding: 15px 20px;">
                                                <input type="text" name="phase_name[]" class="p-input-premium" style="height: 42px; border-radius: 6px; width: 100%;" placeholder="Enter phase name" value="<?php echo htmlspecialchars($p['phase_name']); ?>" required>
                                            </td>
                                            <td style="padding: 15px 20px;">
                                                <input type="text" name="phase_desc[]" class="p-input-premium" style="height: 42px; border-radius: 6px; width: 100%;" value="<?php echo htmlspecialchars($p['description']); ?>" placeholder="Enter description">
                                            </td>
                                            <td style="padding: 15px 20px;">
                                                <input type="date" name="phase_date[]" class="p-input-premium" value="<?php echo $p['expected_date']; ?>" style="height: 42px; border-radius: 6px; width: 100%;">
                                            </td>
                                            <td style="padding: 15px 20px;">
                                                <input type="number" name="phase_cost[]" class="p-input-premium phase-cost" style="height: 42px; border-radius: 6px; width: 100%; text-align:right;" value="<?php echo $p['cost']; ?>" placeholder="0.00" min="0" step="0.01">
                                            </td>
                                            <td style="padding: 15px 20px; text-align: center;">
                                                <button type="button" class="btn btn-light text-danger delete-phase-btn" style="width:36px; height:36px; border-radius:6px; border:none; background:#fee2e2; color:#ef4444; display:inline-flex; align-items:center; justify-content:center;">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                } else {
                                    ?>
                                    <tr class="phase-row" style="border-top: 1px solid #e2e8f0;">
                                        <td style="padding: 15px 20px;">
                                            <input type="text" name="phase_name[]" class="p-input-premium" style="height: 42px; border-radius: 6px; width: 100%;" placeholder="Enter phase name" value="Phase 1" required>
                                        </td>
                                        <td style="padding: 15px 20px;">
                                            <input type="text" name="phase_desc[]" class="p-input-premium" style="height: 42px; border-radius: 6px; width: 100%;" placeholder="Enter description">
                                        </td>
                                        <td style="padding: 15px 20px;">
                                            <input type="date" name="phase_date[]" class="p-input-premium" style="height: 42px; border-radius: 6px; width: 100%;">
                                        </td>
                                        <td style="padding: 15px 20px;">
                                            <input type="number" name="phase_cost[]" class="p-input-premium phase-cost" style="height: 42px; border-radius: 6px; width: 100%; text-align:right;" placeholder="0.00" min="0" step="0.01">
                                        </td>
                                        <td style="padding: 15px 20px; text-align: center;">
                                            <button type="button" class="btn btn-light text-danger delete-phase-btn" style="width:36px; height:36px; border-radius:6px; border:none; background:#fee2e2; color:#ef4444; display:inline-flex; align-items:center; justify-content:center;">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>

                    <div style="padding: 15px 20px; border-top: 1px solid #e2e8f0; background: #fff; display: flex; justify-content: space-between; align-items: center;">
                        <button type="button" id="add_phase_btn" style="background: #FFEAEB; color: #DF2127; border: 1px solid #FFEAEB; padding: 8px 16px; border-radius: 6px; font-weight: 600; font-size: 13px; display: inline-flex; align-items: center; gap: 8px; cursor: pointer;">
                            <i class="fa fa-plus"></i> Add New Phase
                        </button>

                        <div style="display:flex; align-items:center; gap:15px;">
                            <span style="color:#64748b; font-size:14px; font-weight:600;">Total Cost</span>
                            <span id="calculated_total_cost" style="color:#10b981; font-size:20px; font-weight:800;">₹ 0.00</span>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- End Second Card -->

        <!-- Third Card: Project Documents & Links -->
        <div class="premium-card" style="margin: 0 30px 30px 30px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); background: #fff;">
            <div style="padding: 25px 30px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; gap: 15px;">
                <div style="width: 32px; height: 32px; background: #DF2127; color: #fff; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px;">
                    3
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 18px; font-weight: 700; color: #1e293b;">Project Documents & Links</h3>
                    <p style="margin: 4px 0 0 0; font-size: 13px; color: #64748b;">Upload important documents and add useful links</p>
                </div>
            </div>

            <div style="padding: 30px;">

                <!-- Files Section -->
                <div style="margin-bottom: 40px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <h4 style="margin: 0; font-size: 15px; font-weight: 700; color: #1e293b;">Files</h4>
                        <!-- <button type="button" id="add_doc_btn" style="background: #f8fafc; color: #DF2127; border: 1px solid #e2e8f0; padding: 6px 12px; border-radius: 6px; font-weight: 600; font-size: 12px; display: inline-flex; align-items: center; gap: 6px; cursor: pointer; transition: 0.2s;">
                            <i class="fa fa-plus"></i> Add Document
                        </button> -->
                    </div>

                    <div style="border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead style="background: #f8fafc;">
                                <tr>
                                    <th style="padding: 12px 20px; text-align: left; font-size: 12px; font-weight: 600; color: #64748b; width: 45%;">Document Name</th>
                                    <th style="padding: 12px 20px; text-align: left; font-size: 12px; font-weight: 600; color: #64748b; width: 45%;">Select File</th>
                                    <th style="padding: 12px 20px; text-align: center; font-size: 12px; font-weight: 600; color: #64748b; width: 10%;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="docs_container">
                                <?php
                                $get_docs = "SELECT * FROM project_documents WHERE project_id = $project_id";
                                $run_docs = mysqli_query($con, $get_docs);
                                $has_docs = mysqli_num_rows($run_docs) > 0;
                                if ($has_docs) {
                                    while ($doc = mysqli_fetch_assoc($run_docs)) {
                                ?>
                                        <tr style="border-top: 1px solid #e2e8f0;">
                                            <td style="padding: 15px 20px;">
                                                <input type="text" class="p-input-premium" style="height: 42px; border-radius: 6px; width: 100%; background:#f1f5f9; color:#64748b;" value="<?php echo htmlspecialchars($doc['document_name']); ?>" readonly>
                                            </td>
                                            <td style="padding: 15px 20px;">
                                                <span style="font-size: 13px; color: #64748b;"><i class="fa fa-file-text-o"></i> <?php echo htmlspecialchars($doc['file_path']); ?> (Existing)</span>
                                            </td>
                                            <td style="padding: 15px 20px; text-align: center;">
                                                <!-- Actions for existing docs could be added here later -->
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                } else {
                                    ?>
                                    <tr style="border-top: 1px solid #e2e8f0;" id="no_docs_msg_row">
                                        <td colspan="3" style="padding: 20px; text-align: center; color: #94a3b8; font-size: 13px;" id="no_docs_msg">No documents added yet. Click "+ Add Document" to add one.</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <div style="padding: 15px 20px; border-top: 1px solid #e2e8f0; background: #fff; display: flex; justify-content: space-between; align-items: center;">
                        <button type="button" id="add_doc_btn" style="background: #FFEAEB;
                                color: #DF2127;
                                border: 1px solid #FFEAEB;
                                padding: 8px 16px;
                                border-radius: 6px;
                                font-weight: 600;
                                font-size: 13px;
                                display: inline-flex;
                                align-items: center;
                                gap: 8px;
                                cursor: pointer;
                            ">
                            <i class="fa fa-plus"></i> Add Document
                        </button>
                    </div>

                    <!-- Links Section -->
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                            <h4 style="margin: 0; font-size: 15px; font-weight: 700; color: #1e293b;">Links</h4>
                            <!-- <button type="button" id="add_link_btn" style="background: #f8fafc; color: #DF2127; border: 1px solid #e2e8f0; padding: 6px 12px; border-radius: 6px; font-weight: 600; font-size: 12px; display: inline-flex; align-items: center; gap: 6px; cursor: pointer; transition: 0.2s;">
                                <i class="fa fa-plus"></i> Add Link
                            </button> -->
                        </div>

                        <div style="border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead style="background: #f8fafc;">
                                    <tr>
                                        <th style="padding: 12px 20px; text-align: left; font-size: 12px; font-weight: 600; color: #64748b; width: 45%;">Link Name</th>
                                        <th style="padding: 12px 20px; text-align: left; font-size: 12px; font-weight: 600; color: #64748b; width: 45%;">URL</th>
                                        <th style="padding: 12px 20px; text-align: center; font-size: 12px; font-weight: 600; color: #64748b; width: 10%;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="links_container">
                                    <?php
                                    $get_links = "SELECT * FROM project_links WHERE project_id = $project_id";
                                    $run_links = mysqli_query($con, $get_links);
                                    if (mysqli_num_rows($run_links) > 0) {
                                        while ($link = mysqli_fetch_assoc($run_links)) {
                                    ?>
                                            <tr class="link-row" style="border-top: 1px solid #e2e8f0;">
                                                <td style="padding: 15px 20px;">
                                                    <input type="text" name="link_name[]" class="p-input-premium" style="height: 42px; border-radius: 6px; width: 100%;" placeholder="e.g., Figma Design, GitHub Repo" value="<?php echo htmlspecialchars($link['link_name']); ?>" required>
                                                </td>
                                                <td style="padding: 15px 20px;">
                                                    <input type="url" name="link_url[]" class="p-input-premium" style="height: 42px; border-radius: 6px; width: 100%;" placeholder="https://" value="<?php echo htmlspecialchars($link['link_url']); ?>" required>
                                                </td>
                                                <td style="padding: 15px 20px; text-align: center;">
                                                    <button type="button" class="btn btn-light text-danger delete-link-btn" style="width:36px; height:36px; border-radius:6px; border:none; background:#fee2e2; color:#ef4444; display:inline-flex; align-items:center; justify-content:center;">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                    } else {
                                        ?>
                                        <tr style="border-top: 1px solid #e2e8f0;" id="no_links_msg_row">
                                            <td colspan="3" style="padding: 20px; text-align: center; color: #94a3b8; font-size: 13px;" id="no_links_msg">No links added yet. Click "+ Add Link" to add one.</td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div style="padding: 15px 20px; border-top: 1px solid #e2e8f0; background: #fff; display: flex; justify-content: space-between; align-items: center;">
                        <button type="button" id="add_link_btn" style="background: #FFEAEB;
                                color: #DF2127;
                                border: 1px solid #FFEAEB;
                                padding: 8px 16px;
                                border-radius: 6px;
                                font-weight: 600;
                                font-size: 13px;
                                display: inline-flex;
                                align-items: center;
                                gap: 8px;
                                cursor: pointer;
                            ">
                            <i class="fa fa-plus"></i> Add Link
                        </button>
                    </div>

                    <?php if ($error): ?>
                        <div class="alert alert-danger" style="margin-top: 20px; border-radius: 8px;">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <div style="margin-top: 40px; display: flex; justify-content: flex-end; gap: 15px;">
                        <a href="index.php?projects" class="btn-premium-cancel">Cancel</a>
                        <button type="submit" name="submit_project" class="btn-premium-add">Update Project</button>
                    </div>
                </div>
            </div>

    </form>
</div>

<style>
    .premium-label {
        font-weight: 600;
        color: #475569;
        margin-bottom: 8px;
        display: block;
    }

    .upload-area:hover {
        border-color: #DF2127;
    }
</style>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('project_preview').src = e.target.result;
                document.getElementById('image_preview_container').style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function removeImage() {
        document.getElementById('project_image').value = "";
        document.getElementById('image_preview_container').style.display = 'none';
        document.getElementById('project_preview').src = "";
    }

    $(document).ready(function() {

        function formatWithImage(item) {
            if (!item.id) {
                return item.text;
            }
            var image = $(item.element).data('image');
            var $el = $(
                '<span style="display:flex;align-items:center;gap:10px;">' +
                '<img src="' + image + '" style="width:28px;height:28px;border-radius:50%;object-fit:cover;border:2px solid #e2e8f0;flex-shrink:0;" onerror="this.src=\'admin_images/default.png\'"> ' +
                '<span>' + item.text + '</span>' +
                '</span>'
            );
            return $el;
        }

        function formatSelectionWithImage(item) {
            if (!item.id) {
                return item.text;
            }
            var image = $(item.element).data('image');
            var $el = $(
                '<span style="display:flex;align-items:center;gap:6px;">' +
                '<img src="' + image + '" style="width:20px;height:20px;border-radius:50%;object-fit:cover;" onerror="this.src=\'admin_images/default.png\'"> ' +
                '<span>' + item.text + '</span>' +
                '</span>'
            );
            return $el;
        }

        $('#clientSelect').select2({
            placeholder: 'Type client name...',
            allowClear: true,
            templateResult: formatWithImage,
            templateSelection: formatSelectionWithImage
        });

        $('#employeeSelect').select2({
            placeholder: 'Select employees...',
            allowClear: true,
            closeOnSelect: false,
            templateResult: formatWithImage,
            templateSelection: formatSelectionWithImage
        });

        $('#userSelect').select2({
            placeholder: 'Select users...',
            allowClear: true,
            closeOnSelect: false,
            templateResult: formatWithImage,
            templateSelection: formatSelectionWithImage
        });

        $('#adminSelect').select2({
            placeholder: 'Select admins...',
            allowClear: true,
            closeOnSelect: false,
            templateResult: formatWithImage,
            templateSelection: formatSelectionWithImage
        });

        // Dynamic Phases Script
        const symbols = {
            'INR': '₹',
            'USD': '$',
            'EUR': '€',
            'GBP': '£'
        };

        $('select[name="currency"]').on('change', function() {
            const sym = symbols[$(this).val()] || '';
            $('#currency_symbol').text(sym);
            calculateTotalCost();
        });

        function calculateTotalCost() {
            let total = 0;
            $('.phase-cost').each(function() {
                const val = parseFloat($(this).val());
                if (!isNaN(val)) {
                    total += val;
                }
            });
            const sym = symbols[$('select[name="currency"]').val()] || '';
            $('#calculated_total_cost').text(sym + ' ' + total.toFixed(2));

            // Check if it matches total budget
            const totalBudget = parseFloat($('#total_budget').val()) || 0;
            if (total > totalBudget && totalBudget > 0) {
                $('#calculated_total_cost').css('color', '#ef4444');
            } else {
                $('#calculated_total_cost').css('color', '#10b981');
            }
        }

        $(document).on('input', '.phase-cost, #total_budget', function() {
            calculateTotalCost();
        });

        $('#add_phase_btn').on('click', function() {
            const nextPhaseNum = $('.phase-row').length + 1;
            const newRow = `
                <tr class="phase-row" style="border-top: 1px solid #e2e8f0;">
                    <td style="padding: 15px 20px;">
                        <input type="text" name="phase_name[]" class="p-input-premium" style="height: 42px; border-radius: 6px; width: 100%;" placeholder="Enter phase name" value="Phase ${nextPhaseNum}" required>
                    </td>
                    <td style="padding: 15px 20px;">
                        <input type="text" name="phase_desc[]" class="p-input-premium" style="height: 42px; border-radius: 6px; width: 100%;" placeholder="Enter description">
                    </td>
                    <td style="padding: 15px 20px;">
                        <input type="date" name="phase_date[]" class="p-input-premium" style="height: 42px; border-radius: 6px; width: 100%;">
                    </td>
                    <td style="padding: 15px 20px;">
                        <input type="number" name="phase_cost[]" class="p-input-premium phase-cost" style="height: 42px; border-radius: 6px; width: 100%; text-align:right;" placeholder="0.00" min="0" step="0.01">
                    </td>
                    <td style="padding: 15px 20px; text-align: center;">
                        <button type="button" class="btn btn-light text-danger delete-phase-btn" style="width:36px; height:36px; border-radius:6px; border:none; background:#fee2e2; color:#ef4444; display:inline-flex; align-items:center; justify-content:center;">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            $('#phase_container').append(newRow);
        });

        $(document).on('click', '.delete-phase-btn', function() {
            if ($('.phase-row').length > 1) {
                $(this).closest('tr').remove();
                calculateTotalCost();
            } else {
                Swal.fire('Notification', 'You must have at least one phase.', 'info');
            }
        });

        // Add Document Row
        $('#add_doc_btn').on('click', function() {
            $('#no_docs_msg').closest('tr').hide();
            const newDoc = `
                <tr class="doc-row" style="border-top: 1px solid #e2e8f0;">
                    <td style="padding: 15px 20px;">
                        <input type="text" name="doc_name[]" class="p-input-premium" style="height: 42px; border-radius: 6px; width: 100%;" placeholder="e.g. Project Proposal" required>
                    </td>
                    <td style="padding: 15px 20px;">
                        <input type="file" name="doc_file[]" style="width: 100%;" required>
                    </td>
                    <td style="padding: 15px 20px; text-align: center;">
                        <button type="button" class="btn btn-light text-danger delete-doc-btn" style="width:36px; height:36px; border-radius:6px; border:none; background:#fee2e2; color:#ef4444; display:inline-flex; align-items:center; justify-content:center;">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            $('#docs_container').append(newDoc);
        });

        $(document).on('click', '.delete-doc-btn', function() {
            $(this).closest('tr').remove();
            if ($('.doc-row').length === 0) {
                $('#no_docs_msg').closest('tr').show();
            }
        });

        // Add Link Row
        $('#add_link_btn').on('click', function() {
            $('#no_links_msg').closest('tr').hide();
            const newLink = `
                <tr class="link-row" style="border-top: 1px solid #e2e8f0;">
                    <td style="padding: 15px 20px;">
                        <input type="text" name="link_name[]" class="p-input-premium" style="height: 42px; border-radius: 6px; width: 100%;" placeholder="e.g. Figma Design" required>
                    </td>
                    <td style="padding: 15px 20px;">
                        <input type="url" name="link_url[]" class="p-input-premium" style="height: 42px; border-radius: 6px; width: 100%;" placeholder="https://" required>
                    </td>
                    <td style="padding: 15px 20px; text-align: center;">
                        <button type="button" class="btn btn-light text-danger delete-link-btn" style="width:36px; height:36px; border-radius:6px; border:none; background:#fee2e2; color:#ef4444; display:inline-flex; align-items:center; justify-content:center;">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            $('#links_container').append(newLink);
        });

        $(document).on('click', '.delete-link-btn', function() {
            $(this).closest('tr').remove();
            if ($('.link-row').length === 0) {
                $('#no_links_msg').closest('tr').show();
            }
        });

    });
</script>

<!-- Add Source Modal -->
<div class="modal fade" id="addSourceModal" tabindex="-1" role="dialog" aria-labelledby="addSourceModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); overflow: hidden;">
            <div class="modal-header" style="background: #ffedeb; color: #1e293b; padding: 20px 25px; border: none; position: relative;">
                <button class="btn-modal-close" data-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times"></i>
                </button>
                <h4 class="modal-title" id="addSourceModalLabel" style="font-weight: 700; display: flex; align-items: center; gap: 12px; margin: 0;">
                    <div style="background: #DD2127; color: white; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fa fa-plus" style="font-size: 14px;"></i>
                    </div>
                    Add New Source
                </h4>
            </div>
            <div class="modal-body" style="padding: 30px; background: #fff;">
                <form id="add-source-form-main" onsubmit="event.preventDefault();">
                    <div style="margin-bottom: 25px;">
                        <label style="font-weight: 700; color: #475569; display: block; margin-bottom: 12px; font-size: 11px; text-transform: uppercase; letter-spacing: 1px;">Source Name</label>
                        <input type="text" name="source_name" id="new_source_name" placeholder="e.g. Website, LinkedIn" required style="height: 50px; background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 14px; padding: 12px 20px; width: 100%; color: #0f172a; font-weight: 600; outline: none; transition: all 0.3s;" onfocus="this.style.borderColor='#DF2127'; this.style.boxShadow='0 0 0 4px rgba(223, 33, 39, 0.1)';" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none';">
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
                success: function(response) {
                    submitBtn.prop('disabled', false).html('<i class="fa fa-save"></i> Save Source');
                    var data;
                    try {
                        data = typeof response === 'object' ? response : JSON.parse(response.trim());
                    } catch (e) {
                        Swal.fire('Notification', "Server response error: " + response, 'error');
                        return;
                    }
                    if (data.status == "success") {
                        var newHtml = '<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">' +
                            '<label style="font-weight: 500; color: #475569; cursor: pointer; margin: 0;">' +
                            '<input type="checkbox" name="project_source[]" value="' + data.name + '" checked style="margin-right: 8px; width: 16px; height: 16px; vertical-align: middle; accent-color: #DF2127;"> ' + data.name +
                            '</label>' +
                            '<i class="fa fa-trash" style="color: #ef4444; cursor: pointer; font-size: 13px;" onclick="deleteSource(' + data.id + ', this)"></i>' +
                            '</div>';
                        $("#source_checkbox_container").append(newHtml);
                        // Close modal by clicking the dismiss button
                        $('#addSourceModal [data-dismiss="modal"]').first().trigger('click');
                        $('#addSourceModal').hide(); // Fallback for visibility
                        $('#new_source_name').val('');
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open');
                        $('body').css('padding-right', '');
                    } else {
                        if (data.message === "Source already exists") {
                            // Find the existing checkbox and check it
                            var existingCheckbox = $("input[name='project_source[]']").filter(function() {
                                return $(this).val().toLowerCase() === source.toLowerCase();
                            });

                            if (existingCheckbox.length > 0) {
                                existingCheckbox.prop('checked', true);
                            } else {
                                // Fallback: append it if not found in DOM
                                var newHtml = '<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">' +
                                    '<label style="font-weight: 500; color: #475569; cursor: pointer; margin: 0;">' +
                                    '<input type="checkbox" name="project_source[]" value="' + source + '" checked style="margin-right: 8px; width: 16px; height: 16px; vertical-align: middle; accent-color: #DF2127;"> ' + source +
                                    '</label>' +
                                    '<i class="fa fa-trash" style="color: #ef4444; cursor: pointer; font-size: 13px;" onclick="deleteSource(' + data.id + ', this)"></i>' +
                                    '</div>';
                                $("#source_checkbox_container").append(newHtml);
                            }
                            // Close modal by clicking the dismiss button
                            $('#addSourceModal [data-dismiss="modal"]').first().trigger('click');
                            $('#addSourceModal').hide(); // Fallback for visibility
                            $('#new_source_name').val('');
                            $('.modal-backdrop').remove();
                            $('body').removeClass('modal-open');
                            $('body').css('padding-right', '');
                        } else {
                            Swal.fire('Notification', "Error: " + data.message, 'error');
                        }
                    }
                },
                error: function(xhr, status, error) {
                    submitBtn.prop('disabled', false).html('<i class="fa fa-save"></i> Save Source');
                    Swal.fire('Notification', "Connection Error. Details: " + xhr.responseText, 'error');
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
                            if (typeof showPremiumAlert === "function") {
                                showPremiumAlert("Source deleted successfully!");
                            } else {
                                Swal.fire('Deleted!', 'Source deleted successfully.', 'success');
                            }
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

    function showPremiumAlert(message) {
        let container = document.getElementById('toast-container-custom');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container-custom';
            container.style.position = 'fixed';
            container.style.bottom = '20px';
            container.style.right = '20px';
            container.style.zIndex = '999999';
            container.style.display = 'flex';
            container.style.flexDirection = 'column';
            container.style.gap = '10px';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.style.background = '#1e293b';
        toast.style.color = '#fff';
        toast.style.padding = '16px 24px';
        toast.style.borderRadius = '12px';
        toast.style.boxShadow = '0 10px 15px -3px rgba(0,0,0,0.1)';
        toast.style.display = 'flex';
        toast.style.alignItems = 'center';
        toast.style.gap = '12px';
        toast.style.fontSize = '14px';
        toast.style.fontWeight = '600';
        toast.style.transform = 'translateY(100px) scale(0.9)';
        toast.style.opacity = '0';
        toast.style.transition = 'all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275)';

        toast.innerHTML = `
        <div style="width: 24px; height: 24px; background: #10b981; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
            <i class="fa fa-check" style="font-size: 12px;"></i>
        </div>
        ${message}
    `;

        container.appendChild(toast);

        setTimeout(() => {
            toast.style.transform = 'translateY(0) scale(1)';
            toast.style.opacity = '1';
        }, 10);

        setTimeout(() => {
            toast.style.transform = 'translateY(20px) scale(0.9)';
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 400);
        }, 4000);
    }
</script>
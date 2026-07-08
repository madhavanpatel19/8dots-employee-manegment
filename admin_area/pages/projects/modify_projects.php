<?php
$file = 'projects.php';
$content = file_get_contents($file);

// Replace button
$button_search = '<button type="button" class="btn-premium-add" data-toggle="modal" data-target="#addProjectModal">
                <i class="fa fa-plus"></i> Add New Project
            </button>';
$button_replace = '<a href="index.php?add_project" class="btn-premium-add">
                <i class="fa fa-plus"></i> Add New Project
            </a>';
$content = str_replace($button_search, $button_replace, $content);

// Remove modal
$modal_start = '<!-- Add Project Modal -->';
$modal_end = '<!-- Edit Project Modal -->';

$start_pos = strpos($content, $modal_start);
$end_pos = strpos($content, $modal_end);

if ($start_pos !== false && $end_pos !== false) {
    $content = substr_replace($content, '', $start_pos, $end_pos - $start_pos);
}

// Remove JS
$js_start = "$('#add-project-form-main').submit(function(e) {";
$js_end_marker = "});\r\n\r\n        // Edit project";

$js_start_pos = strpos($content, $js_start);
if ($js_start_pos !== false) {
    $js_end_pos = strpos($content, "});", $js_start_pos + 100);
    // Let's find exactly how the JS block is structured
}

file_put_contents($file, $content);
echo "Modification complete.";

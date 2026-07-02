<?php
if (!isset($con)) {
    include(__DIR__ . '/../../includes/db.php');
}

// Only allow access if logged in as employee
if (!isset($_SESSION['emp_id']) || !isset($_SESSION['emp_name'])) {
    header('Location: ../../pages/auth/login.php');
    exit();
}

$emp_id = $_SESSION['emp_id'];

// Fetch allowed categories for this employee
$allowed_categories = [];
$cat_q = mysqli_query($con, "SELECT category FROM company_links_assignments WHERE emp_id = '$emp_id'");
while ($row = mysqli_fetch_assoc($cat_q)) {
    $allowed_categories[] = "'" . mysqli_real_escape_string($con, $row['category']) . "'";
}

// If no categories allowed, we just fetch nothing
$links_data = [];
if (!empty($allowed_categories)) {
    $cat_list = implode(',', $allowed_categories);
    $query = "SELECT * FROM company_links WHERE category IN ($cat_list) ORDER BY category ASC, created_at DESC";
    $run = mysqli_query($con, $query);
    while ($row = mysqli_fetch_assoc($run)) {
        $links_data[] = $row;
    }
}
?>

<div class="premium-ui-enabled">
    <!-- Level 1: Section Browser -->
    <div id="emp-section-browser-view">
        <div style="display: flex; justify-content:flex-end; align-items: center; margin-bottom: 30px; gap: 20px;">
            <div style="position: relative; width: 350px;">
                <i class="fa fa-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                <input type="text" id="emp-section-search-input" placeholder="Search sections..." style="width: 100%; padding: 12px 15px 12px 45px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; outline: none; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            </div>
        </div>

        <div id="emp-sections-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; margin-bottom: 40px;">
            <!-- Rendered by JS -->
        </div>
    </div>

    <!-- Level 2: Resource Hub -->
    <div id="emp-resource-hub-view" style="display: none; width: 100%;">
        <div style="margin-bottom: 20px;">
            <button type="button" id="btn-back-to-sections" style="background: transparent; border: none; color: #64748b; font-weight: 600; font-size: 14px; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: 0.3s;">
                <i class="fa fa-arrow-left"></i> Back to Sections
            </button>
        </div>

        <!-- Header -->
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px;">
            <div style="display: flex; align-items: center; gap: 20px;">
                <div style="width: 60px; height: 60px; background: #ffeaeb; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 28px; color: #df2127;">
                    <i class="fa fa-folder"></i>
                </div>
                <div>
                    <h2 id="hub-section-title" style="margin: 0; color: #0f172a; font-size: 24px; font-weight: 800;">Section</h2>
                    <p id="hub-section-stats" style="margin: 4px 0 0 0; color: #df2127; font-size: 14px; font-weight: 500;">
                        0 Files &bull; 0 Links
                    </p>
                </div>
            </div>

            <div style="display: flex; gap: 10px; align-items: center;">
                <div style="position: relative;">
                    <i class="fa fa-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                    <input type="text" id="hub-search-input" placeholder="Search files and links..." style="width: 300px; padding: 12px 15px 12px 45px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; outline: none; background: #fff;">
                </div>

                <div style="position: relative;">
                    <button type="button" id="btn-add-resource-dropdown" class="btn-premium-add">
                        <i class="fa fa-plus"></i> Add Resource <i class="fa fa-caret-down"></i>
                    </button>
                    <div id="add-resource-menu" style="display: none; position: absolute; top: 100%; right: 0; background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); margin-top: 5px; z-index: 100; min-width: 180px; overflow: hidden;">
                        <div class="add-menu-item" data-type="link" style="padding: 12px 20px; cursor: pointer; border-bottom: 1px solid #e2e8f0; font-size: 14px; font-weight: 500; color: #475569; transition: 0.2s;"><i class="fa fa-link" style="margin-right: 8px; color: #3b82f6;"></i> Add Link</div>
                        <div class="add-menu-item" data-type="document" style="padding: 12px 20px; cursor: pointer; font-size: 14px; font-weight: 500; color: #475569; transition: 0.2s;"><i class="fa fa-file-text-o" style="margin-right: 8px; color: #10b981;"></i> Add Document</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Forms -->
        <div id="inline-add-link-form" class="resource-add-form" style="display: none; background: #f8fafc; padding: 25px; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 25px;">
            <form id="add-link-form" method="POST" onsubmit="submitResourceForm(event, this)">
                <input type="hidden" name="category" class="form-category">
                <input type="hidden" name="resource_type" value="link">
                <h4 style="margin: 0 0 15px 0; color: #0f172a; font-size: 16px;">Add New Link</h4>
                <div class="row">
                    <div class="col-md-5">
                        <label style="font-weight: 600; color: #475569; font-size: 12px; margin-bottom: 8px; display: block;">Link Name</label>
                        <input type="text" name="link_name" class="p-input-premium" placeholder="e.g. Figma Design" required style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid #e2e8f0; outline: none; font-size: 14px;">
                    </div>
                    <div class="col-md-7">
                        <label style="font-weight: 600; color: #475569; font-size: 12px; margin-bottom: 8px; display: block;">URL</label>
                        <input type="url" name="link_url" class="p-input-premium" placeholder="https://..." required style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid #e2e8f0; outline: none; font-size: 14px;">
                    </div>
                </div>
                <div style="margin-top: 20px; display: flex; justify-content: flex-end; gap: 12px;">
                    <button type="button" class="btn-premium-cancel">Cancel</button>
                    <button type="submit" class="btn-premium-add">Save Link</button>
                </div>
            </form>
        </div>

        <div id="inline-add-document-form" class="resource-add-form" style="display: none; background: #f8fafc; padding: 25px; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 25px;">
            <form id="add-document-form" method="POST" enctype="multipart/form-data" onsubmit="submitResourceForm(event, this)">
                <input type="hidden" name="category" class="form-category">
                <input type="hidden" name="resource_type" value="document">
                <h4 style="margin: 0 0 15px 0; color: #0f172a; font-size: 16px;">Upload Document</h4>
                <div class="row">
                    <div class="col-md-5">
                        <label style="font-weight: 600; color: #475569; font-size: 12px; margin-bottom: 8px; display: block;">Document Name</label>
                        <input type="text" name="link_name" class="p-input-premium" placeholder="e.g. Technical Spec" required style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid #e2e8f0; outline: none; font-size: 14px;">
                    </div>
                    <div class="col-md-7">
                        <label style="font-weight: 600; color: #475569; font-size: 12px; margin-bottom: 8px; display: block;">Select File</label>
                        <input type="file" name="document_file" class="p-input-premium" required style="width: 100%; padding: 8px 14px; border-radius: 8px; border: 1px solid #e2e8f0; outline: none; font-size: 14px; background: #fff;">
                    </div>
                </div>
                <div style="margin-top: 20px; display: flex; justify-content: flex-end; gap: 12px;">
                    <button type="button" class="btn-premium-cancel">Cancel</button>
                    <button type="submit" class="btn-premium-add">Upload File</button>
                </div>
            </form>
        </div>

        <!-- Files Table -->
        <div style="margin-bottom: 40px;" id="files-section-container">
            <h3 style="margin: 0 0 15px 0; font-size: 18px; font-weight: 700; color: #0f172a;" id="files-section-title">Files (0)</h3>
            <div style="border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; background: #fff;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: var(--p-bg-header); border-bottom: 1px solid #e2e8f0;">
                            <th style="padding: 12px 20px; text-align: left; font-size: 12px; color: #fff; font-weight: 600;">Name</th>
                            <th style="padding: 12px 20px; text-align: center; font-size: 12px; color: #fff; font-weight: 600;">Type</th>
                            <th style="padding: 12px 20px; text-align: center; font-size: 12px; color: #fff; font-weight: 600;">Updated On</th>
                            <th style="padding: 12px 20px; text-align: center; font-size: 12px; color: #fff; font-weight: 600;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="hub-files-table"></tbody>
                </table>
            </div>
        </div>

        <!-- Links Table -->
        <div id="links-section-container">
            <h3 style="margin: 0 0 15px 0; font-size: 18px; font-weight: 700; color: #0f172a;" id="links-section-title">Links (0)</h3>
            <div style="border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; background: #fff;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: var(--p-bg-header); border-bottom: 1px solid #e2e8f0;">
                            <th style="padding: 12px 20px; text-align: left; font-size: 12px; color: #fff; font-weight: 600;">Name</th>
                            <th style="padding: 12px 20px; text-align: center; font-size: 12px; color: #fff; font-weight: 600;">URL</th>
                            <th style="padding: 12px 20px; text-align: center; font-size: 12px; color: #fff; font-weight: 600;">Updated On</th>
                            <th style="padding: 12px 20px; text-align: center; font-size: 12px; color: #fff; font-weight: 600;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="hub-links-table"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .folder-card {
        background: #fff;
        padding: 20px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        flex-direction: column;
    }

    .folder-card:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    }

    .table-row-hover:hover {
        background: #f8fafc;
    }

    .resource-icon-mini {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }

    .btn-icon-premium {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #fff;
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        width: 34px;
        height: 34px;
        transition: 0.3s;
        cursor: pointer;
        color: #64748b;
        text-decoration: none !important;
    }

    .btn-icon-premium:hover {
        background: #f8fafc;
        border-color: #1e293b;
        color: #1e293b;
        transform: translateY(-2px);
    }

    .btn-premium-add {
        background: #df2127;
        color: #fff;
        border: none;
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        font-size: 13px;
        transition: all 0.3s ease;
    }

    .btn-premium-add:hover {
        background: #b91c1c;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(223, 33, 39, 0.2);
    }

    .btn-premium-cancel {
        background: transparent;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        color: #64748b;
        font-weight: 600;
        padding: 8px 16px;
        cursor: pointer;
        font-size: 13px;
        transition: all 0.2s;
    }

    .btn-premium-cancel:hover {
        background: #f8fafc;
        color: #0f172a;
        border-color: #cbd5e1;
    }

    .add-menu-item:hover {
        background: #f8fafc;
        color: #0f172a !important;
    }
</style>

<script>
    $(document).ready(function() {
        const allResources = <?php echo json_encode($links_data); ?>;

        function getResourceType(url) {
            const ext = url.split('.').pop().toLowerCase();
            if (['pdf'].includes(ext)) return {
                type: 'PDF',
                icon: 'fa-file-pdf-o',
                color: '#ef4444',
                bg: '#fef2f2'
            };
            if (['xlsx', 'xls', 'csv'].includes(ext)) return {
                type: 'XLSX',
                icon: 'fa-file-excel-o',
                color: '#10b981',
                bg: '#ecfdf5'
            };
            if (['docx', 'doc'].includes(ext)) return {
                type: 'DOCX',
                icon: 'fa-file-word-o',
                color: '#3b82f6',
                bg: '#eff6ff'
            };
            if (['png', 'jpg', 'jpeg', 'gif', 'svg'].includes(ext)) return {
                type: 'Image',
                icon: 'fa-file-image-o',
                color: '#8b5cf6',
                bg: '#f5f3ff'
            };
            if (['zip', 'rar'].includes(ext)) return {
                type: 'ZIP',
                icon: 'fa-file-archive-o',
                color: '#f59e0b',
                bg: '#fffbeb'
            };
            return {
                type: 'Link',
                icon: 'fa-link',
                color: '#3b82f6',
                bg: '#eff6ff'
            };
        }

        function formatDateTime(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return date.toLocaleString('en-US', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function renderSections() {
            try {
                const categories = [...new Set(allResources.map(item => item.category))];
                let html = '';

                categories.forEach(cat => {
                    const items = allResources.filter(i => i.category === cat);
                    let filesCount = 0;
                    let linksCount = 0;

                    items.forEach(item => {
                        const typeInfo = getResourceType(item.link_url);
                        if (typeInfo.type === 'Link') linksCount++;
                        else filesCount++;
                    });

                    html += `
            <div class="folder-card" data-category="${cat}">
                <div style="display: flex; gap: 15px; align-items: center;">
                    <div style="width: 50px; height: 50px; background: #FFEAEB; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #DF2127; font-size: 24px;">
                        <i class="fa fa-folder"></i>
                    </div>
                    <div>
                        <h3 style="margin: 0; color: #0f172a; font-size: 16px; font-weight: 700;">${cat}</h3>
                        <p style="margin: 4px 0 0 0; color: #64748b; font-size: 12px;">
                            <i class="fa fa-file-o" style="margin-right: 4px;"></i> ${filesCount} Files &nbsp;&nbsp;
                            <i class="fa fa-link" style="margin-right: 4px;"></i> ${linksCount} Links
                        </p>
                    </div>
                </div>
            </div>
            `;
                });

                if (categories.length === 0) {
                    html = `
            <div style="grid-column: 1 / -1; text-align: center; padding: 60px 0; background: #fff; border-radius: 12px; border: 1px dashed #e2e8f0;">
                <i class="fa fa-lock" style="font-size: 40px; color: #cbd5e1; margin-bottom: 15px;"></i>
                <h3 style="color: #64748b; font-weight: 600; font-size: 16px;">No resources available</h3>
                <p style="color: #94a3b8; font-size: 14px;">You have not been assigned access to any company links yet.</p>
            </div>
            `;
                }

                $('#emp-sections-grid').html(html);
            } catch (e) {
                $('#emp-sections-grid').html('<div style="color:red; padding: 20px;">JS Error in renderSections: ' + e.message + '<br>' + e.stack + '</div>');
            }
        }

        function openResourceHub(category) {
            category = String(category);
            const filtered = allResources.filter(item => String(item.category) === category);

            const searchTerm = $('#hub-search-input').val() ? String($('#hub-search-input').val()).toLowerCase() : '';
            const searched = filtered.filter(item => String(item.link_name).toLowerCase().includes(searchTerm) || String(item.link_url).toLowerCase().includes(searchTerm));

            const files = [];
            const links = [];

            searched.forEach(item => {
                const typeInfo = getResourceType(item.link_url);
                if (typeInfo.type === 'Link') links.push({
                    item,
                    typeInfo
                });
                else files.push({
                    item,
                    typeInfo
                });
            });

            $('#hub-section-title').text(category);
            $('#hub-section-stats').html(`${files.length} Files &bull; ${links.length} Links`);
            $('#files-section-title').text(`Files (${files.length})`);
            $('#links-section-title').text(`Links (${links.length})`);

            // Render Files
            let filesHtml = '';
            files.forEach(({
                item,
                typeInfo
            }) => {
                const dateOn = formatDateTime(item.created_at);
                let url = item.link_url;
                if (!url.startsWith('http://') && !url.startsWith('https://') && !url.startsWith('uploads/')) url = 'http://' + url;
                if (url.startsWith('uploads/')) url = '../admin_area/' + url;

                filesHtml += `
                        <tr class="table-row-hover">
                            <td style="padding: 12px 20px; vertical-align: middle;">
                                <div style="display: flex; align-items: center; justify-content: flex-start; gap: 12px;">
                                    <div class="resource-icon-mini" style="background: #ffeaeb; color: #df2127;">
                                        <i class="fa ${typeInfo.icon}"></i>
                                    </div>
                                    <span style="color: #0f172a; font-weight: 500; font-size: 14px;">${item.link_name}</span>
                                </div>
                            </td>
                            <td style="padding: 12px 20px; text-align: center; vertical-align: middle; color: #475569; font-size: 13px; font-weight: 500;">${typeInfo.type}</td>
                            <td style="padding: 12px 20px; text-align: center; vertical-align: middle; color: #475569; font-size: 13px;">${dateOn}</td>
                            <td style="padding: 12px 20px; text-align: center; vertical-align: middle;">
                                <a href="${url}" target="_blank" class="btn-icon-premium" style="background: #f0f9ff; border-color: #e0f2fe; color: #0284c7;" title="Download">
                                    <i class="fa fa-download"></i>
                                </a>
                            </td>
                        </tr>
                        `;
            });
            if (files.length === 0) filesHtml = `<tr><td colspan="4" style="text-align: center; padding: 30px; color: #94a3b8;">No files found</td></tr>`;
            $('#hub-files-table').html(filesHtml);

            // Render Links
            let linksHtml = '';
            links.forEach(({
                item,
                typeInfo
            }) => {
                const dateOn = formatDateTime(item.created_at);
                let url = item.link_url;
                if (!url.startsWith('http://') && !url.startsWith('https://')) url = 'http://' + url;

                linksHtml += `
                        <tr class="table-row-hover">
                            <td style="padding: 12px 20px; vertical-align: middle;">
                                <div style="display: flex; align-items: center; justify-content: flex-start; gap: 12px;">
                                    <div class="resource-icon-mini" style="background: #ffeaeb; color: #df2127;">
                                        <i class="fa ${typeInfo.icon}"></i>
                                    </div>
                                    <span style="color: #0f172a; font-weight: 500; font-size: 14px;">${item.link_name}</span>
                                </div>
                            </td>
                            <td style="padding: 12px 20px; text-align: center; vertical-align: middle;">
                                <a href="${url}" target="_blank" style="color: #df2127; font-size: 13px; text-decoration: none; word-break: break-all;">${url}</a>
                            </td>
                            <td style="padding: 12px 20px; text-align: center; vertical-align: middle; color: #475569; font-size: 13px;">${dateOn}</td>
                            <td style="padding: 12px 20px; text-align: center; vertical-align: middle;">
                                <a href="${url}" target="_blank" class="btn-icon-premium" style="background: #f0f9ff; border-color: #e0f2fe; color: #0284c7;" title="Visit">
                                    <i class="fa fa-external-link"></i>
                                </a>
                            </td>
                        </tr>
                        `;
            });
            if (links.length === 0) linksHtml = `<tr><td colspan="4" style="text-align: center; padding: 30px; color: #94a3b8;">No links found</td></tr>`;
            $('#hub-links-table').html(linksHtml);

            $('#emp-section-browser-view').hide();
            $('#emp-resource-hub-view').fadeIn(200);
        }

        renderSections();

        $(document).on('click', '.folder-card', function() {
            $('#hub-search-input').val('');
            openResourceHub($(this).data('category'));
        });

        $('#btn-back-to-sections').click(function() {
            $('#emp-resource-hub-view').hide();
            $('#emp-section-browser-view').fadeIn(200);
        });

        $('#hub-search-input').on('keyup', function() {
            const cat = $('#hub-section-title').text();
            openResourceHub(cat);
        });

        $('#btn-add-resource-dropdown').click(function(e) {
            e.stopPropagation();
            $('#add-resource-menu').toggle();
        });

        $(document).click(function(e) {
            if (!$(e.target).closest('#btn-add-resource-dropdown, #add-resource-menu').length) {
                $('#add-resource-menu').hide();
            }
        });

        $('.add-menu-item').click(function() {
            const type = $(this).data('type');
            $('#add-resource-menu').hide();
            $('.resource-add-form').hide();
            $('.form-category').val($('#hub-section-title').text());

            if (type === 'link') {
                $('#inline-add-link-form').slideDown();
            } else if (type === 'document') {
                $('#inline-add-document-form').slideDown();
            }
        });

        $('.btn-premium-cancel').click(function() {
            $('.resource-add-form').slideUp();
            $(this).closest('form')[0].reset();
        });

        window.submitResourceForm = function(e, formElement) {
            e.preventDefault();
            const form = $(formElement);
            const formData = new FormData(formElement);

            let currentCat = $('#hub-section-title').text().trim();
            if (!currentCat) currentCat = $('.form-category').val();
            formData.set('category', currentCat);

            const submitBtn = form.find('button[type="submit"]');
            const originalText = submitBtn.text();
            submitBtn.prop('disabled', true).text('Saving...');

            $.ajax({
                url: 'ajax_add_company_link.php',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    try {
                        const data = JSON.parse(response);
                        if (data.status === 'success') {
                            formElement.reset();
                            $('.resource-add-form').slideUp();

                            // Append to allResources and re-render
                            allResources.push(data.data);
                            openResourceHub(currentCat);
                        } else {
                            if (typeof Swal !== 'undefined') Swal.fire('Error', data.message || 'Unknown error', 'error');
                            else alert('Error: ' + (data.message || 'Unknown error'));
                        }
                    } catch (err) {
                        if (typeof Swal !== 'undefined') Swal.fire('Server Error', response.substring(0, 100), 'error');
                        else alert('Server Error: ' + response.substring(0, 100));
                    }
                    submitBtn.prop('disabled', false).text(originalText);
                },
                error: function() {
                    if (typeof Swal !== 'undefined') Swal.fire('Network Error', 'Failed to submit form', 'error');
                    else alert('Network Error: Failed to submit form');
                    submitBtn.prop('disabled', false).text(originalText);
                }
            });
        };
        $('#emp-section-search-input').on('keyup', function() {
            const searchTerm = $(this).val().toLowerCase();
            if (searchTerm === '') {
                $('.folder-card').show();
            } else {
                $('.folder-card').each(function() {
                    const catName = $(this).data('category').toLowerCase();
                    if (catName.includes(searchTerm)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }
        });

    });
</script>
<?php
if (!isset($con)) {
    if (!isset($con)) {
        include(__DIR__ . '/../../includes/db.php');
    }
}
?>

<div class="page-wrapper premium-ui-enabled" style="background: #fafbfc; min-height: 100vh; padding: 30px 40px;">

    <!-- Level 1: Section Browser -->
    <div id="section-browser-view">

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; gap: 20px;">
            <div> </div>
            <button type="button" id="btn-add-section" class="btn-premium-add">
                <i class="fa fa-plus"></i> New Section
            </button>
        </div>



        <!-- Inline Section Add Form -->
        <div id="inline-section-form" style="display: none; background: #fff; padding: 20px 25px; border-radius: 12px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; margin-bottom: 25px;">
            <form id="add-section-form-direct" style="display: flex; gap: 15px; align-items: flex-end;">
                <div style="flex-grow: 1;">
                    <label style="font-weight: 700; color: #475569; font-size: 12px; margin-bottom: 8px; display: block;">Section Name</label>
                    <input type="text" id="new-section-name" class="p-input-premium" placeholder="e.g. Mechanical Engineering" required style="width: 100%; padding: 10px 15px; border-radius: 8px; border: 1px solid #e2e8f0; outline: none; font-weight: 500; font-size: 14px;">
                </div>
                <button type="submit" style="background: #df2127; color: #fff; border: none; padding: 10px 25px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: 0.3s; font-size: 14px;">
                    Create Section
                </button>
            </form>
        </div>

        <!-- Sections Grid -->
        <div id="sections-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; margin-bottom: 40px;">
            <!-- Sections will be loaded here as folders -->
            <div style="grid-column: 1 / -1; text-align: center; padding: 60px 0;">
                <div class="premium-spinner"></div>
            </div>
        </div>

        <!-- Recently Updated Table -->
        <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; margin-bottom: 30px;">
            <div style="padding: 15px 20px; border-bottom: 1px solid #e2e8f0; background: var(--p-bg-header);">
                <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #fff;">Pinned Links</h3>
            </div>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #fafbfc; border-bottom: 1px solid #e2e8f0;">
                        <th style="padding: 12px 20px; text-align: left; font-size: 12px; color: #64748b; font-weight: 600;">Name</th>
                        <th style="padding: 12px 20px; text-align: center; font-size: 12px; color: #64748b; font-weight: 600;">Type</th>
                        <th style="padding: 12px 20px; text-align: center; font-size: 12px; color: #64748b; font-weight: 600;">Section</th>
                        <th style="padding: 12px 20px; text-align: center; font-size: 12px; color: #64748b; font-weight: 600;">Updated By</th>
                        <th style="padding: 12px 20px; text-align: center; font-size: 12px; color: #64748b; font-weight: 600;">Updated On</th>
                        <th style="padding: 12px 20px; text-align: center; font-size: 12px; color: #64748b; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody id="recently-updated-table">
                    <!-- populated by JS -->
                </tbody>
            </table>
        </div>

    </div>

    <!-- Level 2: Resource Hub -->
    <div id="resource-hub-view" style="display: none; width: 100%;">
        <div style="margin-bottom: 20px;">
            <button type="button" id="btn-back-to-sections" style="background: transparent; border: none; color: #64748b; font-weight: 600; font-size: 14px; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: 0.3s;">
                <i class="fa fa-arrow-left"></i> Back to Repository
            </button>
        </div>

        <!-- Header -->
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px;">
            <div style="display: flex; align-items: center; gap: 20px;">
                <div style="width: 60px; height: 60px; background: #ffeaeb; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 28px; color: #df2127;">
                    <i class="fa fa-folder"></i>
                </div>
                <div>
                    <h2 id="hub-section-title" style="margin: 0; color: #0f172a; font-size: 24px; font-weight: 800; display: flex; align-items: center; gap: 10px;">
                        Section<i id="btn-rename-section" class="fa fa-pencil" style="font-size: 14px; color: #df2127; cursor: pointer;" title="Rename Section"></i>
                    </h2>
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
            <form id="add-link-form" onsubmit="submitResourceForm(event, this)">
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
                    <button type="button" class="btn-cancel-add" style="background: transparent; border: 1px solid #e2e8f0; border-radius: 8px; color: #64748b; font-weight: 600; padding: 8px 16px; cursor: pointer; font-size: 13px;">Cancel</button>
                    <button type="submit" style="background: #df2127; color: #fff; border: none; padding: 8px 16px; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 13px;">Save Link</button>
                </div>
            </form>
        </div>

        <div id="inline-add-document-form" class="resource-add-form" style="display: none; background: #f8fafc; padding: 25px; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 25px;">
            <form id="add-document-form" enctype="multipart/form-data" onsubmit="submitResourceForm(event, this)">
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
                    <button type="button" class="btn-cancel-add" style="background: transparent; border: 1px solid #e2e8f0; border-radius: 8px; color: #64748b; font-weight: 600; padding: 8px 16px; cursor: pointer; font-size: 13px;">Cancel</button>
                    <button type="submit" style="background: #df2127; color: #fff; border: none; padding: 8px 16px; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 13px;">Upload File</button>
                </div>
            </form>
        </div>

        <!-- Search Bar -->
        <!-- Files Table -->
        <div style="margin-bottom: 40px;" id="files-section-container">
            <h3 style="margin: 0 0 15px 0; font-size: 18px; font-weight: 700; color: #0f172a;" id="files-section-title">Files (0)</h3>
            <div style="border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; background: #fff;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: var(--p-bg-header); border-bottom: 1px solid #e2e8f0;">
                            <th style="padding: 12px 20px; text-align: center; font-size: 12px; color: #fff; font-weight: 600;">Name</th>
                            <th style="padding: 12px 20px; text-align: center; font-size: 12px; color: #fff; font-weight: 600;">Type</th>
                            <th style="padding: 12px 20px; text-align: center; font-size: 12px; color: #fff; font-weight: 600;">Uploaded By</th>
                            <th style="padding: 12px 20px; text-align: center; font-size: 12px; color: #fff; font-weight: 600;">Uploaded On</th>
                            <th style="padding: 12px 20px; text-align: center; font-size: 12px; color: #fff; font-weight: 600;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="hub-files-table">
                        <!-- populated by js -->
                    </tbody>
                </table>
                <div id="view-all-files-container" style="padding: 15px 20px; background: #fff; border-top: 1px solid #e2e8f0; display: none;">
                    <a href="#" id="view-all-files-btn" style="color: #df2127; font-size: 14px; font-weight: 600; text-decoration: none; outline: none;">View all files</a>
                </div>
            </div>
        </div>

        <!-- Links Table -->
        <div id="links-section-container">
            <h3 style="margin: 0 0 15px 0; font-size: 18px; font-weight: 700; color: #0f172a;" id="links-section-title">Links (0)</h3>
            <div style="border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; background: #fff;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: var(--p-bg-header); border-bottom: 1px solid #e2e8f0;">
                            <th style="padding: 12px 20px; text-align: center; font-size: 12px; color: #fff; font-weight: 600;">Name</th>
                            <th style="padding: 12px 20px; text-align: center; font-size: 12px; color: #fff; font-weight: 600;">URL</th>
                            <th style="padding: 12px 20px; text-align: center; font-size: 12px; color: #fff; font-weight: 600;">Added By</th>
                            <th style="padding: 12px 20px; text-align: center; font-size: 12px; color: #fff; font-weight: 600;">Added On</th>
                            <th style="padding: 12px 20px; text-align: center; font-size: 12px; color: #fff; font-weight: 600;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="hub-links-table">
                        <!-- populated by js -->
                    </tbody>
                </table>
                <div id="view-all-links-container" style="padding: 15px 20px; background: #fff; border-top: 1px solid #e2e8f0; display: none;">
                    <a href="#" id="view-all-links-btn" style="color: #df2127; font-size: 14px; font-weight: 600; text-decoration: none; outline: none;">View all links</a>
                </div>
            </div>
        </div>

    </div>

</div>

<style>
    .premium-ui-enabled {
        font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
    }

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

    .hub-resource-card {
        background: #fff;
        padding: 15px 20px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: 0.2s;
    }

    .hub-resource-card:hover {
        background: #f8fafc;
    }

    .resource-icon-mini {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }

    .btn-hub-action {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #e2e8f0;
        background: #fff;
        color: #64748b;
        cursor: pointer;
        transition: 0.2s;
        text-decoration: none;
        font-size: 14px;
    }

    .btn-hub-action:hover {
        background: #f1f5f9;
        color: #0f172a;
    }

    .btn-icon-premium {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #fff;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        width: 38px;
        height: 38px;
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
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    .premium-spinner {
        width: 40px;
        height: 40px;
        border: 3px solid rgba(0, 0, 0, 0.05);
        border-top: 3px solid #3b82f6;
        border-radius: 50%;
        margin: 0 auto;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    .table-row-hover:hover {
        background: #f8fafc;
    }
</style>

<script>
    $(document).ready(function() {
        let allResources = [];
        // Fake currently logged in admin user for UI purposes
        const currentUserAvatar = `<?php echo isset($admin_image) && !empty($admin_image) ? 'admin_images/' . $admin_image : 'https://ui-avatars.com/api/?name=Admin&background=0D8ABC&color=fff'; ?>`;
        const currentUserName = `<?php echo isset($header_display_name) ? $header_display_name : 'Admin User'; ?>`;

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

        function formatTimeAgo(dateString) {
            if (!dateString) return 'Unknown';
            const date = new Date(dateString);
            const now = new Date();
            const diffMs = now - date;
            const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));

            if (diffDays === 0) return 'Today';
            if (diffDays === 1) return 'Yesterday';
            if (diffDays < 7) return diffDays + ' days ago';
            if (diffDays < 14) return '1 week ago';
            if (diffDays < 30) return Math.floor(diffDays / 7) + ' weeks ago';
            return Math.floor(diffDays / 30) + ' months ago';
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

        function loadData(callback = null) {
            $.ajax({
                url: 'ajax/misc/ajax_company_links.php?action=fetch',
                method: 'GET',
                success: function(response) {
                    allResources = JSON.parse(response);
                    renderSections();
                    renderRecentlyUpdated();
                    if (callback) callback();
                }
            });
        }

        function renderSections() {
            const categories = [...new Set(allResources.map(item => item.category))];
            let html = '';

            categories.forEach(cat => {
                const items = allResources.filter(i => i.category === cat);
                let filesCount = 0;
                let linksCount = 0;
                let latestDate = null;

                items.forEach(item => {
                    const typeInfo = getResourceType(item.link_url);
                    if (typeInfo.type === 'Link') linksCount++;
                    else filesCount++;

                    if (item.created_at) {
                        const date = new Date(item.created_at);
                        if (!latestDate || date > latestDate) latestDate = date;
                    }
                });

                const updatedAgoText = latestDate ? formatTimeAgo(latestDate) : 'Unknown';

                html += `
                <div class="folder-card" data-category="${cat}">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
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
                        <button style="background: none; border: none; color: #cbd5e1; cursor: pointer; font-size: 16px;">
                            <i class="fa fa-ellipsis-h"></i>
                        </button>
                    </div>
                    <div style="border-top: 1px solid #f1f5f9; padding-top: 15px; display: flex; align-items: center; color: #64748b; font-size: 12px;">
                        <i class="fa fa-calendar-o" style="margin-right: 6px;"></i> Updated ${updatedAgoText}
                    </div>
                </div>
                `;
            });

            if (categories.length === 0) {
                html = `
                <div style="grid-column: 1 / -1; text-align: center; padding: 60px 0; background: #fff; border-radius: 12px; border: 1px dashed #e2e8f0;">
                    <i class="fa fa-folder-open-o" style="font-size: 40px; color: #cbd5e1; margin-bottom: 15px;"></i>
                    <h3 style="color: #64748b; font-weight: 600; font-size: 16px;">No repository sections found</h3>
                    <p style="color: #94a3b8; font-size: 14px;">Initialize your first section above</p>
                </div>
                `;
            }

            $('#sections-grid').html(html);
        }

        function renderRecentlyUpdated() {
            // Sort by created_at DESC
            const pinned = [...allResources].filter(item => item.is_pinned == 1 || item.is_pinned == '1').sort((a, b) => {
                const dateA = a.created_at ? new Date(a.created_at) : new Date(0);
                const dateB = b.created_at ? new Date(b.created_at) : new Date(0);
                return dateB - dateA;
            });

            let html = '';
            pinned.forEach(item => {
                const typeInfo = getResourceType(item.link_url);
                const updatedOn = formatDateTime(item.created_at);

                let url = item.link_url;
                if (!url.startsWith('http://') && !url.startsWith('https://')) url = 'http://' + url;

                html += `
                <tr class="table-row-hover">
                    <td style="padding: 15px 20px; vertical-align: middle;">
                        <div style="display: flex; align-items: center; justify-content: flex-start; gap: 12px;">
                            <div style="width: 32px; height: 32px; border-radius: 6px; background: ${typeInfo.bg}; color: ${typeInfo.color}; display: flex; align-items: center; justify-content: center; font-size: 16px;">
                                <i class="fa ${typeInfo.icon}"></i>
                            </div>
                            <span style="color: #0f172a; font-weight: 500; font-size: 14px;">${item.link_name}</span>
                        </div>
                    </td>
                    <td style="padding: 15px 20px; text-align: center; vertical-align: middle; color: #475569; font-size: 14px;">${typeInfo.type}</td>
                    <td style="padding: 15px 20px; text-align: center; vertical-align: middle;">
                        <span style="color: #3b82f6; cursor: pointer; font-size: 14px;" onclick="openResourceHub('${item.category}')">${item.category}</span>
                    </td>
                    <td style="padding: 15px 20px; vertical-align: middle;">
                        <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                            <img src="${currentUserAvatar}" style="width: 24px; height: 24px; border-radius: 50%; object-fit: cover;">
                            <span style="color: #475569; font-size: 14px;">${currentUserName}</span>
                        </div>
                    </td>
                    <td style="padding: 15px 20px; text-align: center; vertical-align: middle; color: #475569; font-size: 14px;">${updatedOn}</td>
                    <td style="padding: 15px 20px; text-align: center; vertical-align: middle;">
                        <div style="display: flex; justify-content: center; gap: 8px;">
                            <button class="btn-icon-premium btn-pin-resource" data-id="${item.id}" data-pinned="${item.is_pinned}" style="width: 32px; height: 32px; font-size: 12px; background: #fefce8; border-color: #fef9c3; color: #eab308;" title="Unpin">
                                <i class="fa fa-thumb-tack"></i>
                            </button>
                            <a href="${url}" target="_blank" class="btn-icon-premium" style="width: 32px; height: 32px; font-size: 12px; background: #f0f9ff; border-color: #e0f2fe; color: #0284c7;" title="${typeInfo.type === 'Link' ? 'Visit' : 'Download'}">
                                <i class="fa ${typeInfo.type === 'Link' ? 'fa-external-link' : 'fa-download'}"></i>
                            </a>
                            <button class="btn-icon-premium btn-delete-resource" data-id="${item.id}" style="width: 32px; height: 32px; font-size: 12px; background: #fef2f2; border-color: #fee2e2; color: #ef4444;" title="Delete">
                                <i class="fa fa-trash-o"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                `;
            });

            if (pinned.length === 0) {
                html = `<tr><td colspan="6" style="text-align: center; padding: 30px; color: #94a3b8;">No pinned links</td></tr>`;
            }

            $('#recently-updated-table').html(html);
        }

        function openResourceHub(category) {
            category = String(category);
            $('.form-category').val(category);

            const filtered = allResources.filter(item => String(item.category) === category);

            // Search filter
            const searchTerm = $('#hub-search-input').val() ? String($('#hub-search-input').val()).toLowerCase() : '';
            const searched = filtered.filter(item => String(item.link_name).toLowerCase().includes(searchTerm) || String(item.link_url).toLowerCase().includes(searchTerm));

            const files = [];
            const links = [];

            searched.forEach(item => {
                const typeInfo = getResourceType(item.link_url);
                if (typeInfo.type === 'Link') {
                    links.push({
                        item,
                        typeInfo
                    });
                } else {
                    files.push({
                        item,
                        typeInfo
                    });
                }
            });

            $('#hub-section-title').html(`${category} Hub <i id="btn-rename-section" class="fa fa-pencil" style="font-size: 14px; color: #94a3b8; cursor: pointer;" title="Rename Section"></i>`);
            $('#hub-section-stats').html(`${files.length} Files &bull; ${links.length} Links`);
            $('#files-section-title').text(`Files (${files.length})`);
            $('#links-section-title').text(`Links (${links.length})`);

            let filesHtml = '';
            files.forEach(({
                item,
                typeInfo
            }, index) => {
                const dateOn = formatDateTime(item.created_at);
                let url = item.link_url;
                if (!url.startsWith('http://') && !url.startsWith('https://') && !url.startsWith('uploads/')) url = 'http://' + url;

                const displayStyle = index >= 5 ? 'display: none;' : '';
                const rowClass = index >= 5 ? 'table-row-hover extra-file-row' : 'table-row-hover';

                filesHtml += `
                <tr class="${rowClass}" style="${displayStyle}">
                    <td style="padding: 12px 20px; vertical-align: middle;">
                        <div style="display: flex; align-items: center; justify-content: flex-start; gap: 12px;">
                            <div class="resource-icon-mini" style="background: #ffeaeb; color: #df2127; width: 32px; height: 32px; font-size: 16px;">
                                <i class="fa ${typeInfo.icon}"></i>
                            </div>
                            <span style="color: #0f172a; font-weight: 500; font-size: 14px;">${item.link_name}</span>
                        </div>
                    </td>
                    <td style="padding: 12px 20px; text-align: center; vertical-align: middle; color: #475569; font-size: 13px; font-weight: 500;">${typeInfo.type}</td>
                    <td style="padding: 12px 20px; vertical-align: middle;">
                        <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                            <img src="${currentUserAvatar}" style="width: 24px; height: 24px; border-radius: 50%; object-fit: cover;">
                            <span style="color: #475569; font-size: 13px; font-weight: 500;">${currentUserName}</span>
                        </div>
                    </td>
                    <td style="padding: 12px 20px; text-align: center; vertical-align: middle; color: #475569; font-size: 13px;">${dateOn}</td>
                    <td style="padding: 12px 20px; text-align: center; vertical-align: middle;">
                        <div style="display: flex; justify-content: center; gap: 8px;">
                            <button class="btn-icon-premium btn-pin-resource" data-id="${item.id}" data-pinned="${item.is_pinned}" style="width: 32px; height: 32px; font-size: 12px; background: ${item.is_pinned == 1 || item.is_pinned == '1' ? '#fefce8' : '#f1f5f9'}; border-color: ${item.is_pinned == 1 || item.is_pinned == '1' ? '#fef9c3' : '#e2e8f0'}; color: ${item.is_pinned == 1 || item.is_pinned == '1' ? '#eab308' : '#64748b'};" title="${item.is_pinned == 1 || item.is_pinned == '1' ? 'Unpin' : 'Pin'}">
                                <i class="fa fa-thumb-tack"></i>
                            </button>
                            <a href="${url}" target="_blank" class="btn-icon-premium" style="width: 32px; height: 32px; font-size: 12px; background: #f0f9ff; border-color: #e0f2fe; color: #0284c7;" title="Download">
                                <i class="fa fa-download"></i>
                            </a>
                            <button class="btn-icon-premium btn-delete-resource" data-id="${item.id}" style="width: 32px; height: 32px; font-size: 12px; background: #fef2f2; border-color: #fee2e2; color: #ef4444;" title="Delete">
                                <i class="fa fa-trash-o"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                `;
            });

            if (files.length === 0) filesHtml = `<tr><td colspan="5" style="text-align: center; padding: 30px; color: #94a3b8;">No files found</td></tr>`;
            $('#hub-files-table').html(filesHtml);

            if (files.length > 5) {
                $('#view-all-files-container').show();
                $('#view-all-files-btn').text(`View all ${files.length} files`);
            } else {
                $('#view-all-files-container').hide();
            }

            let linksHtml = '';
            links.forEach(({
                item,
                typeInfo
            }, index) => {
                const dateOn = formatDateTime(item.created_at);
                let url = item.link_url;
                if (!url.startsWith('http://') && !url.startsWith('https://')) url = 'http://' + url;

                const displayStyle = index >= 5 ? 'display: none;' : '';
                const rowClass = index >= 5 ? 'table-row-hover extra-link-row' : 'table-row-hover';

                linksHtml += `
                <tr class="${rowClass}" style="${displayStyle}">
                    <td style="padding: 12px 20px; vertical-align: middle;">
                        <div style="display: flex; align-items: center; justify-content: flex-start; gap: 12px;">
                            <div class="resource-icon-mini" style="background: #ffeaeb; color: #df2127; width: 32px; height: 32px; font-size: 16px;">
                                <i class="fa ${typeInfo.icon}"></i>
                            </div>
                            <span style="color: #0f172a; font-weight: 500; font-size: 14px;">${item.link_name}</span>
                        </div>
                    </td>
                    <td style="padding: 12px 20px; text-align: center; vertical-align: middle;">
                        <a href="${url}" target="_blank" style="color: #df2127; font-size: 13px; text-decoration: none; word-break: break-all;">${url}</a>
                    </td>
                    <td style="padding: 12px 20px; vertical-align: middle;">
                        <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                            <img src="${currentUserAvatar}" style="width: 24px; height: 24px; border-radius: 50%; object-fit: cover;">
                            <span style="color: #475569; font-size: 13px; font-weight: 500;">${currentUserName}</span>
                        </div>
                    </td>
                    <td style="padding: 12px 20px; text-align: center; vertical-align: middle; color: #475569; font-size: 13px;">${dateOn}</td>
                    <td style="padding: 12px 20px; text-align: center; vertical-align: middle;">
                        <div style="display: flex; justify-content: center; gap: 8px;">
                            <button class="btn-icon-premium btn-pin-resource" data-id="${item.id}" data-pinned="${item.is_pinned}" style="width: 32px; height: 32px; font-size: 12px; background: ${item.is_pinned == 1 || item.is_pinned == '1' ? '#fefce8' : '#f1f5f9'}; border-color: ${item.is_pinned == 1 || item.is_pinned == '1' ? '#fef9c3' : '#e2e8f0'}; color: ${item.is_pinned == 1 || item.is_pinned == '1' ? '#eab308' : '#64748b'};" title="${item.is_pinned == 1 || item.is_pinned == '1' ? 'Unpin' : 'Pin'}">
                                <i class="fa fa-thumb-tack"></i>
                            </button>
                            <a href="${url}" target="_blank" class="btn-icon-premium" style="width: 32px; height: 32px; font-size: 12px; background: #f0f9ff; border-color: #e0f2fe; color: #0284c7;" title="Visit">
                                <i class="fa fa-external-link"></i>
                            </a>
                            <button class="btn-icon-premium btn-delete-resource" data-id="${item.id}" style="width: 32px; height: 32px; font-size: 12px; background: #fef2f2; border-color: #fee2e2; color: #ef4444;" title="Delete">
                                <i class="fa fa-trash-o"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                `;
            });

            if (links.length === 0) linksHtml = `<tr><td colspan="5" style="text-align: center; padding: 30px; color: #94a3b8;">No links found</td></tr>`;
            $('#hub-links-table').html(linksHtml);

            if (links.length > 5) {
                $('#view-all-links-container').show();
                $('#view-all-links-btn').text(`View all ${links.length} links`);
            } else {
                $('#view-all-links-container').hide();
            }

            $('#section-browser-view').hide();
            $('#resource-hub-view').fadeIn(200);
        }

        window.openResourceHub = openResourceHub;

        window.submitResourceForm = function(e, formElement) {
            e.preventDefault();
            const form = $(formElement);
            const formData = new FormData(formElement);

            // Forcefully inject category to avoid hidden input state bugs
            let currentCat = $('#hub-section-title').text().replace(' Hub ', '').trim();
            if (!currentCat) currentCat = $('.form-category').val();
            formData.set('category', currentCat);

            const submitBtn = form.find('button[type="submit"]');
            const originalText = submitBtn.text();
            submitBtn.prop('disabled', true).text('Saving...');

            $.ajax({
                url: 'ajax/misc/ajax_company_links.php?action=add',
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

                            // Restore hidden inputs after reset
                            $('.form-category').val(currentCat);

                            loadData(function() {
                                if ($('#resource-hub-view').is(':visible')) {
                                    openResourceHub(currentCat);
                                }
                            });
                        } else {
                            Swal.fire('Error', data.message || 'Unknown error', 'error');
                        }
                    } catch (err) {
                        Swal.fire('Server Error', response.substring(0, 100), 'error');
                    }
                    submitBtn.prop('disabled', false).text(originalText);
                },
                error: function() {
                    Swal.fire('Network Error', 'Failed to submit form', 'error');
                    submitBtn.prop('disabled', false).text(originalText);
                }
            });
        };

        loadData();

        // Event Handlers
        $(document).on('click', '#view-all-files-btn', function(e) {
            e.preventDefault();
            $('.extra-file-row').slideDown(200);
            $('#view-all-files-container').hide();
        });

        $(document).on('click', '#view-all-links-btn', function(e) {
            e.preventDefault();
            $('.extra-link-row').slideDown(200);
            $('#view-all-links-container').hide();
        });

        $(document).on('click', '.folder-card', function() {
            $('#hub-search-input').val('');
            openResourceHub($(this).data('category'));
        });

        $('#btn-back-to-sections').click(function() {
            $('#resource-hub-view').hide();
            $('#section-browser-view').fadeIn(200);
            loadData();
        });

        $('#btn-add-section').click(function() {
            $('#inline-section-form').slideToggle(200);
        });

        $('#add-section-form-direct').submit(function(e) {
            e.preventDefault();
            const name = $('#new-section-name').val().trim();
            if (name) {
                $('#inline-section-form').slideUp(200);
                $('#hub-search-input').val('');
                openResourceHub(name);
                $('#new-section-name').val('');
            }
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

        $('.add-menu-item').click(function(e) {
            e.stopPropagation();
            const type = $(this).data('type');
            $('#add-resource-menu').hide();
            $('.resource-add-form').hide();
            if (type === 'link') {
                $('#inline-add-link-form').slideDown(200);
            } else {
                $('#inline-add-document-form').slideDown(200);
            }
        });

        $('.btn-cancel-add').click(function() {
            $('.resource-add-form').slideUp(200);
        });

        $('#hub-search-input').on('input', function() {
            const cat = $('.form-category').val();
            openResourceHub(cat);
        });

        $(document).on('click', '.btn-delete-resource', function(e) {
            e.stopPropagation();
            const id = $(this).data('id');

            let currentCat = $('#hub-section-title').text().replace(' Hub', '').trim();
            if (!currentCat) currentCat = $('.form-category').val();

            Swal.fire({
                title: 'Delete Asset?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, delete it'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'ajax/misc/ajax_company_links.php?action=delete',
                        method: 'POST',
                        data: {
                            id: id
                        },
                        success: function() {
                            loadData(function() {
                                if ($('#resource-hub-view').is(':visible')) {
                                    openResourceHub(currentCat);
                                }
                            });
                        }
                    });
                }
            });
        });

        $(document).on('click', '.btn-pin-resource', function(e) {
            e.stopPropagation();
            const id = $(this).data('id');
            const isPinned = $(this).data('pinned') == 1;
            const action = isPinned ? 'unpin' : 'pin';

            let currentCat = $('#hub-section-title').text().replace(' Hub', '').trim();
            if (!currentCat) currentCat = $('.form-category').val();

            $.ajax({
                url: 'ajax/misc/ajax_company_links.php?action=' + action,
                method: 'POST',
                data: {
                    id: id
                },
                success: function() {
                    loadData(function() {
                        if ($('#resource-hub-view').is(':visible')) {
                            openResourceHub(currentCat);
                        }
                    });
                }
            });
        });

        $(document).on('click', '#btn-rename-section', function(e) {
            e.stopPropagation();
            let currentCat = $('#hub-section-title').text().replace(' Hub', '').trim();
            if (!currentCat) currentCat = $('.form-category').val();

            Swal.fire({
                title: 'Rename Section',
                input: 'text',
                inputValue: currentCat,
                showCancelButton: true,
                confirmButtonText: 'Rename',
                inputValidator: (value) => {
                    if (!value || !value.trim()) {
                        return 'You need to enter a name!'
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const newCat = result.value.trim();
                    if (newCat === currentCat) return;

                    $.ajax({
                        url: 'ajax/misc/ajax_company_links.php?action=rename_category',
                        method: 'POST',
                        data: {
                            old_category: currentCat,
                            new_category: newCat
                        },
                        success: function(resp) {
                            try {
                                const r = JSON.parse(resp);
                                if (r.status === 'success') {
                                    loadData(function() {
                                        openResourceHub(newCat);
                                    });
                                } else {
                                    Swal.fire('Error', r.message, 'error');
                                }
                            } catch (err) {}
                        }
                    });
                }
            });
        });
    });
</script>
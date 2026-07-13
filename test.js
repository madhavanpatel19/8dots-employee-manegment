
$(document).ready(function () {

    const urlParams = new URLSearchParams(window.location.search);
    let currentStatusFilter = urlParams.get('status') || '';

    $('#project-status-filter').val(currentStatusFilter);

    $('#project-status-filter').change(function () {
        currentStatusFilter = $(this).val();
        loadProjects();
    });

    function loadProjects() {
        $('#full-projects-container').css('opacity', '0.6');

        $.ajax({
            url: 'pages/projects/fetch_all_projects.php',
            method: 'GET',
            data: {
                status: currentStatusFilter
            },
            success: function (response) {
                $('#full-projects-container').css('opacity', '1');
                if (response.trim() === "") {
                    $('#full-projects-container').html(`
                        <tr>
                            <td colspan="7" style="padding: 100px 20px; text-align: center;">
                                <div style="width: 60px; height: 60px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                                    <i class="fa fa-folder-open-o" style="font-size: 24px; color: #94a3b8;"></i>
                                </div>
                                <h4 style="color: #1e293b; font-weight: 700; margin-bottom: 5px;">No Projects Found</h4>
                                <p style="color: #64748b; font-size: 13px;">There are currently no active projects in the system.</p>
                            </td>
                        </tr>
                    `);
                } else {
                    $('#full-projects-container').html(response);
                }
            },
            error: function (xhr, status, error) {
                $('#full-projects-container').css('opacity', '1').html(`
                    <tr>
                        <td colspan="7" style="padding: 50px; text-align: center;">
                            <div style="background: #fef2f2; padding: 20px; border-radius: 12px; border: 1.5px dashed #fecaca;">
                                <i class="fa fa-exclamation-circle" style="color: #ef4444; font-size: 32px; margin-bottom: 10px;"></i>
                                <h4 style="color: #991b1b; font-weight: 700;">Connection Error</h4>
                                <p style="color: #b91c1c; font-size: 13px;">Was unable to retrieve data. Status: ${status}</p>
                                <button onclick="location.reload()" class="btn btn-xs" style="margin-top: 10px; background: #ef4444; color: #fff; border-radius: 8px;">Retry</button>
                            </div>
                        </td>
                    </tr>
                `);
            }
        });
    }

    loadProjects();

    // Toggle remarks detail row (Triggered only by History Button)
    $(document).on('click', '.btn-toggle-history', function (e) {
        e.preventDefault();

        const row = $(this).closest('tr');
        const detailRow = row.next('.project-detail-row');
        const icon = $(this).find('.history-toggle-icon');

        detailRow.toggle();

        if (detailRow.is(':visible')) {
            row.css('background-color', '#f8fafc');
            icon.removeClass('fa-history').addClass('fa-times').css('color', '#ef4444');
            $(this).css('background', '#fee2e2').css('border-color', '#fecaca');
        } else {
            row.css('background-color', '');
            icon.removeClass('fa-times').addClass('fa-history').css('color', '#7c3aed');
            $(this).css('background', '#f5f3ff').css('border-color', '#ede9fe');
        }
    });

    $('#add-project-form-main').submit(function (e) {
        e.preventDefault();
        const formData = $(this).serialize();
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();

        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Creating...');

        $.ajax({
            url: 'ajax/clients/ajax_add_client_project.php',
            method: 'POST',
            data: formData,
            success: function (response) {
                submitBtn.prop('disabled', false).html(originalText);
                if (response.success) {
                    $('#addProjectModal').modal('hide');
                    $('#add-project-form-main')[0].reset();
                    loadProjects();
                    showPremiumAlert('Project initiated successfully!');
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: response.message,
                        icon: 'error',
                        customClass: {
                            popup: 'premium-card swal2-premium'
                        },
                        confirmButtonColor: '#ef4444'
                    });
                }
            },
            error: function () {
                submitBtn.prop('disabled', false).html(originalText);
                Swal.fire({
                    title: 'Connection Error',
                    text: 'A network error occurred.',
                    icon: 'error',
                    customClass: {
                        popup: 'premium-card swal2-premium'
                    },
                    confirmButtonColor: '#ef4444'
                });
            }
        });
    });

    window.editProject = function (id) {
        $.ajax({
            url: 'ajax/projects/ajax_get_project_details.php',
            method: 'GET',
            data: {
                project_id: id
            },
            success: function (response) {
                if (response.success) {
                    $('#edit_project_id').val(response.data.id);
                    $('#edit_client_id').val(response.data.client_id);
                    $('#edit_project_name').val(response.data.project_name);
                    $('#edit_project_date').val(response.data.project_date);
                    $('#edit_budget').val(response.data.budget);
                    $('#edit_currency').val(response.data.currency || 'INR');
                    $('#edit_status').val(response.data.status);
                    $('#editProjectModal').modal('show');
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            }
        });
    }

    $('#edit-project-form-main').submit(function (e) {
        e.preventDefault();
        const formData = $(this).serialize();
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();

        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');

        $.ajax({
            url: 'ajax/projects/ajax_update_project.php',
            method: 'POST',
            data: formData,
            success: function (response) {
                submitBtn.prop('disabled', false).html(originalText);
                if (response.success) {
                    $('#editProjectModal').modal('hide');
                    loadProjects();
                    showPremiumAlert('Project updated successfully!');
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function () {
                submitBtn.prop('disabled', false).html(originalText);
                Swal.fire('Network Error', 'Could not update project.', 'error');
            }
        });
    });

    $(document).on('click', '.add-remark-btn', function () {
        const btn = $(this);
        const projectId = btn.data('project-id');
        const container = btn.closest('.project-detail-row').find('.remarks-history-premium');
        const remarkInput = btn.siblings('.remark-textarea');
        const remarkText = remarkInput.val().trim();

        if (!remarkText) {
            remarkInput.focus();
            return;
        }

        btn.prop('disabled', true).html('<i class="fa fa-circle-o-notch fa-spin"></i>');

        $.ajax({
            url: 'ajax/clients/ajax_add_client_remark.php',
            method: 'POST',
            data: {
                project_id: projectId,
                remark: remarkText
            },
            success: function (response) {
                btn.prop('disabled', false).html('<i class="fa fa-send"></i> Post Update');
                if (response.success) {
                    const newRemark = $(`
                        <div class="timeline-remark-item" style="margin-bottom: 25px; position: relative; padding-left: 32px; display: none; width: 100%;">
                            <div class="timeline-dot" style="left: 0; background: #6366f1; border-color: #6366f1; box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);"></div>
                            <div class="remark-content-box" style="border-left: 4px solid #6366f1; padding-left: 20px;">
                                <div class="remark-time-premium" style="margin-bottom: 8px;">
                                    <i class="fa fa-clock-o"></i> JUST NOW
                                </div>
                                <div class="remark-text-premium">${remarkText.replace(/\n/g, '<br>')}</div>
                            </div>
                        </div>`);

                    container.find('.no-remarks-placeholder').remove();
                    container.prepend(newRemark);
                    newRemark.slideDown(400);
                    remarkInput.val('');
                } else {
                    Swal.fire({
                        title: 'Error Saving Remark',
                        text: response.message || 'Unknown error occurred.',
                        icon: 'error',
                        customClass: {
                            popup: 'premium-card swal2-premium'
                        },
                        confirmButtonColor: '#ef4444'
                    });
                }
            },
            error: function () {
                btn.prop('disabled', false).html('<i class="fa fa-send"></i> Post Update');
                Swal.fire({
                    title: 'Connection Error',
                    text: 'Unable to connect to the server to save the remark.',
                    icon: 'error',
                    customClass: {
                        popup: 'premium-card swal2-premium'
                    },
                    confirmButtonColor: '#ef4444'
                });
            }
        });
    });

    $(document).on('change', '.project-status-select', function () {
        const select = $(this);
        const projectId = select.data('project-id');
        const newStatus = select.val();

        select.css('opacity', '0.5');

        $.ajax({
            url: 'ajax/projects/ajax_update_project_status.php',
            method: 'POST',
            data: {
                project_id: projectId,
                status: newStatus
            },
            success: function (response) {
                select.css('opacity', '1');
                if (response.success) {
                    showPremiumAlert(`Status updated to ${newStatus}`);
                } else {
                    Swal.fire({
                        title: 'Update Failed',
                        text: response.message || 'Error updating status.',
                        icon: 'error',
                        customClass: {
                            popup: 'premium-card swal2-premium'
                        },
                        confirmButtonColor: '#ef4444'
                    });
                }
            },
            error: function () {
                select.css('opacity', '1');
                Swal.fire({
                    title: 'Connection Error',
                    text: 'Unable to communicate with the server.',
                    icon: 'error',
                    customClass: {
                        popup: 'premium-card swal2-premium'
                    },
                    confirmButtonColor: '#ef4444'
                });
            }
        });
    }); // Close document.on change

    let projectToDelete = null;
    window.deleteProject = function (id, name) {
        projectToDelete = id;
        $('#delete_project_name_label').text(name);
        $('#projectDeleteConfirmOverlay').addClass('active');
    }

    window.closeProjectDeleteConfirm = function () {
        $('#projectDeleteConfirmOverlay').removeClass('active');
        projectToDelete = null;
    }

    $('#confirmProjectDeleteBtn').on('click', function () {
        if (!projectToDelete) return;

        const btn = $(this);
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Erasing...');

        $.ajax({
            url: 'ajax/projects/ajax_delete_project.php',
            method: 'POST',
            data: {
                project_id: projectToDelete
            },
            success: function (response) {
                btn.prop('disabled', false).html('Delete Project');
                if (response.success) {
                    closeProjectDeleteConfirm();
                    loadProjects();
                    showPremiumAlert('Project and history purged successfully');
                } else {
                    Swal.fire({
                        title: 'Deletion Error',
                        text: response.message || 'Could not delete project.',
                        icon: 'error',
                        customClass: {
                            popup: 'premium-card'
                        },
                        confirmButtonColor: '#ef4444'
                    });
                }
            },
            error: function () {
                btn.prop('disabled', false).html('Delete Project');
                closeProjectDeleteConfirm();
                Swal.fire({
                    title: 'Network Error',
                    text: 'Network error occurred during project excision.',
                    icon: 'error',
                    customClass: {
                        popup: 'premium-card'
                    },
                    confirmButtonColor: '#ef4444'
                });
            }
        });
    });

    // Unified Hub Logic
    let currentProjectIdRepo = null;
    let currentRepoTab = 'documents';

    window.switchRepoTab = function (tab) {
        currentRepoTab = tab;
        $('.repo-tab').removeClass('active');
        $(`#tab-${tab}`).addClass('active');

        // Toggle buttons
        if (tab === 'documents') {
            $('#btn-add-artifact').show();
            $('#btn-add-link').hide();
        } else {
            $('#btn-add-artifact').hide();
            $('#btn-add-link').show();
        }

        // Hide any open forms
        $('#resource-forms-container').hide();
        $('.btn-premium-add-inline .btn-text').each(function () {
            $(this).text($(this).parent().attr('id') === 'btn-add-artifact' ? 'Add Document' : 'Add Link');
        });

        refreshRepoContent();
    }

    window.toggleAddResourceForm = function (type) {
        const container = $('#resource-forms-container');
        const formDoc = $('#add-document-form-unified');
        const formLink = $('#add-link-form-unified');

        if (container.is(':visible')) {
            container.slideUp(300);
            $(`#btn-add-${type === 'link' ? 'link' : 'artifact'} .btn-text`).text(type === 'link' ? 'Add Link' : 'Add Document');
        } else {
            $('.resource-form').hide();
            if (type === 'link' || currentRepoTab === 'links') {
                formLink.show();
                $('#btn-add-link .btn-text').text('Close Form');
            } else {
                formDoc.show();
                $('#btn-add-artifact .btn-text').text('Close Form');
            }
            container.slideDown(300);
        }
    }

    function refreshRepoContent() {
        $('#docs-list-container').html('<div class="spinner-premium" style="margin: 30px auto;"></div>');
        const url = currentRepoTab === 'documents' ? 'ajax/projects/ajax_view_project_documents.php' : 'ajax/projects/ajax_view_project_links.php';

        $.ajax({
            url: url,
            method: 'GET',
            data: {
                project_id: currentProjectIdRepo
            },
            success: function (response) {
                $('#docs-list-container').html(response);
            }
        });
    }

    // Link Submission
    $('#add-link-form-unified').submit(function (e) {
        e.preventDefault();
        const formData = $(this).serialize();
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();

        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');

        $.ajax({
            url: 'ajax/projects/ajax_add_project_link.php',
            method: 'POST',
            data: formData,
            success: function (response) {
                submitBtn.prop('disabled', false).html(originalText);
                if (response.success) {
                    toggleAddResourceForm('link');
                    $('#add-link-form-unified')[0].reset();
                    refreshRepoContent();
                    showPremiumAlert('Link saved to repository');
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            }
        });
    });

    // Existing Document Logic Update
    $('#add-document-form-unified').submit(function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Uploading...');

        $.ajax({
            url: 'ajax/projects/ajax_add_project_document.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                submitBtn.prop('disabled', false).html('Archive Document');
                if (response.success) {
                    toggleAddResourceForm('document');
                    $('#add-document-form-unified')[0].reset();
                    $('#file-name-label-unified').text('Choose file...');
                    refreshRepoContent();
                    showPremiumAlert('Document archived');
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            }
        });
    });

    window.viewDocs = function (id, initialTab = 'documents') {
        currentProjectIdRepo = id;
        $('#doc_project_id_unified').val(id);
        $('#link_project_id_unified').val(id);
        switchRepoTab(initialTab);
        $('#viewDocumentsModal').modal('show');
    }

    window.deleteDoc = function (docId, projectId) {
        Swal.fire({
            title: 'Confirm Removal',
            text: "This resource will be permanently deleted.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Yes, delete it!',
            customClass: {
                popup: 'premium-card'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const url = currentRepoTab === 'documents' ? 'ajax/projects/ajax_delete_project_document.php' : 'ajax/projects/ajax_delete_project_link.php';
                const data = currentRepoTab === 'documents' ? {
                    doc_id: docId
                } : {
                    link_id: docId
                };

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: data,
                    success: function (response) {
                        if (response.success) {
                            refreshRepoContent();
                            showPremiumAlert('Resource removed');
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    }
                });
            }
        });
    }

    $('#project_doc_input_unified').change(function () {
        const fileName = $(this).val().split('\\').pop();
        if (fileName) $('#file-name-label-unified').text(fileName).css('color', '#4f46e5');
    });
});

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

// Budget Management Logic
let currentBudgetId = null;

window.openBudgetModal = function (id, name, initialBudget = 0, currency = 'INR') {
    currentBudgetId = id;
    $('#budget_project_name_title').text('Project: ' + name);
    $('#budget_currency').val(currency); // Set hidden input
    $('#budget_phases_body').empty();
    $('#no_phases_msg').hide();

    // Set initial display
    updateSummarySymbols();
    const sym = getCurrencySymbol();
    $('#summary_total_cost').text(sym + ' ' + initialBudget.toLocaleString());
    $('#summary_received_amount, #summary_pending_amount').text(sym + ' 0');

    $('#projectBudgetModal').modal('show');

    // Fetch existing phases
    $.ajax({
        url: 'ajax/projects/ajax_get_project_budget.php',
        method: 'GET',
        data: {
            project_id: id
        },
        success: function (response) {
            if (response.success) {
                // Update currency from database
                if (response.currency) {
                    $('#budget_currency').val(response.currency);
                    updateSummarySymbols();
                }

                if (response.data.length > 0) {
                    response.data.forEach(phase => {
                        addPhaseRow(phase);
                    });
                } else if (initialBudget > 0) {
                    // Auto-initialize with default phase using project's main budget
                    addPhaseRow({
                        phase_name: 'Project Execution',
                        description: 'Initial budget allocation',
                        cost: initialBudget,
                        received_amount: 0,
                        received_date: '',
                        remark: ''
                    });
                } else {
                    $('#no_phases_msg').show();
                }
            }
            calculateTotals();
        }
    });
}

function getCurrencySymbol() {
    const symbols = {
        'INR': 'â‚¹',
        'USD': '$',
        'EUR': 'â‚¬',
        'GBP': 'Â£',
        'AED': 'Ø¯.Ø¥'
    };
    return symbols[$('#budget_currency').val()] || 'â‚¹';
}

function updateSummarySymbols() {
    const sym = getCurrencySymbol();
    $('.phase-currency-sym').text(sym);
}

$('#budget_currency').on('change', function () {
    updateSummarySymbols();
    calculateTotals();
});

window.openAddPhaseModal = function () {
    $('#phase_edit_index').val(''); // Empty means new
    $('#phase-edit-form')[0].reset();
    $('#phaseEditModal').modal('show');
}

window.editPhase = function (btn) {
    const row = $(btn).closest('tr');
    const index = row.index();
    $('#phase_edit_index').val(index);
    $('#phase_edit_name').val(row.attr('data-name'));
    $('#phase_edit_desc').val(row.attr('data-desc'));
    $('#phase_edit_cost').val(row.attr('data-cost'));
    $('#phase_edit_received').val(row.attr('data-received'));
    $('#phase_edit_method').val(row.attr('data-method'));
    $('#phase_edit_date').val(row.attr('data-date'));
    $('#phaseEditModal').modal('show');
}

window.savePhaseEdit = function () {
    const name = $('#phase_edit_name').val().trim();
    if (!name) {
        Swal.fire('Validation Error', 'Phase Title is required.', 'warning');
        return;
    }

    const desc = $('#phase_edit_desc').val().trim();
    const cost = parseFloat($('#phase_edit_cost').val()) || 0;
    const received = parseFloat($('#phase_edit_received').val()) || 0;
    const method = $('#phase_edit_method').val().trim();
    const date = $('#phase_edit_date').val();

    const data = {
        phase_name: name,
        description: desc,
        cost: cost,
        received_amount: received,
        remark: method,
        received_date: date
    };

    const index = $('#phase_edit_index').val();
    if (index === '') {
        addPhaseRow(data);
    } else {
        updatePhaseRow(parseInt(index), data);
    }

    $('#phaseEditModal').modal('hide');
    calculateTotals();
}

window.addPhaseRow = function (data = null) {
    $('#no_phases_msg').hide();
    const sym = getCurrencySymbol();

    let cost = 0, received = 0;
    if (data) {
        cost = parseFloat(data.cost) || 0;
        received = parseFloat(data.received_amount) || 0;
    }

    let statusHtml = '';
    if (received >= cost && cost > 0) {
        statusHtml = `<span style="background: #dcfce7; color: #16a34a; border-radius: 20px; padding: 4px 12px; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">Received</span>`;
    } else if (received > 0) {
        statusHtml = `<span style="background: #dbeafe; color: #2563eb; border-radius: 20px; padding: 4px 12px; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">Partially Paid</span>`;
    } else {
        statusHtml = `<span style="background: #ffedd5; color: #ea580c; border-radius: 20px; padding: 4px 12px; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">Pending</span>`;
    }

    const index = $('#budget_phases_body tr').length + 1;

    const row = $(`
        <tr class="budget-phase-row" style="transition: 0.3s;" 
            data-name="${data ? escapeHtml(data.phase_name) : ''}"
            data-desc="${data ? escapeHtml(data.description || '') : ''}"
            data-cost="${cost}"
            data-received="${received}"
            data-method="${data ? escapeHtml(data.remark || '') : ''}"
            data-date="${data ? escapeHtml(data.received_date || '') : ''}">
            <td style="padding: 15px 20px; border-bottom: 1px solid #f8fafc; vertical-align: middle; font-weight: 700; color: #64748b; font-size: 12px;">
                <span class="phase-index-display">${index}</span>
            </td>
            <td style="padding: 15px 20px; border-bottom: 1px solid #f8fafc; vertical-align: middle;">
                <div style="font-weight: 700; color: #0f172a; font-size: 13px;">${data ? escapeHtml(data.phase_name) : ''}</div>
            </td>
            <td style="padding: 15px 20px; border-bottom: 1px solid #f8fafc; vertical-align: middle; font-weight: 700; color: #475569; font-size: 13px;">
                <span class="phase-currency-sym">${sym}</span> <span class="display-cost">${cost.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 2 })}</span>
            </td>
            <td style="padding: 15px 20px; border-bottom: 1px solid #f8fafc; vertical-align: middle; font-weight: 800; color: #0f172a; font-size: 13px;">
                <span class="phase-currency-sym">${sym}</span> <span class="display-received">${received.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 2 })}</span>
            </td>
            <td style="padding: 15px 20px; border-bottom: 1px solid #f8fafc; vertical-align: middle; font-weight: 600; color: #64748b; font-size: 12px;">
                <span class="display-method">${data && data.remark ? escapeHtml(data.remark) : 'â€”'}</span>
            </td>
            <td style="padding: 15px 20px; border-bottom: 1px solid #f8fafc; vertical-align: middle; text-align: center;">
                ${statusHtml}
            </td>
            <td style="padding: 15px 20px; border-bottom: 1px solid #f8fafc; vertical-align: middle; text-align: center;">
                <div style="display: flex; gap: 8px; justify-content: center;">
                    <button type="button" onclick="editPhase(this)" style="background: #eff6ff; border: 1px solid #bfdbfe; width: 32px; height: 32px; border-radius: 8px; color: #3b82f6; transition: 0.2s;">
                        <i class="fa fa-pencil" style="font-size: 12px;"></i>
                    </button>
                    <button type="button" class="delete-phase-btn" style="background: #fee2e2; border: 1px solid #fecaca; width: 32px; height: 32px; border-radius: 8px; color: #ef4444; transition: 0.2s;">
                        <i class="fa fa-trash" style="font-size: 12px;"></i>
                    </button>
                </div>
            </td>
        </tr>
    `);
    $('#budget_phases_body').append(row);
    calculateTotals();
}

window.updatePhaseRow = function (index, data) {
    const row = $('#budget_phases_body tr').eq(index);

    let cost = parseFloat(data.cost) || 0;
    let received = parseFloat(data.received_amount) || 0;

    row.attr('data-name', data.phase_name);
    row.attr('data-desc', data.description);
    row.attr('data-cost', cost);
    row.attr('data-received', received);
    row.attr('data-method', data.remark);
    row.attr('data-date', data.received_date);

    row.find('.display-cost').text(cost.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 2 }));
    row.find('.display-received').text(received.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 2 }));
    row.find('td').eq(1).find('div').text(data.phase_name);
    row.find('.display-method').text(data.remark ? data.remark : 'â€”');

    let statusHtml = '';
    if (received >= cost && cost > 0) {
        statusHtml = `<span style="background: #dcfce7; color: #16a34a; border-radius: 20px; padding: 4px 12px; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">Received</span>`;
    } else if (received > 0) {
        statusHtml = `<span style="background: #dbeafe; color: #2563eb; border-radius: 20px; padding: 4px 12px; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">Partially Paid</span>`;
    } else {
        statusHtml = `<span style="background: #ffedd5; color: #ea580c; border-radius: 20px; padding: 4px 12px; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">Pending</span>`;
    }
    row.find('td').eq(5).html(statusHtml);
}

function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return String(text).replace(/[&<>"']/g, function (m) { return map[m]; });
}

$(document).on('click', '.delete-phase-btn', function () {
    $(this).closest('tr').fadeOut(200, function () {
        $(this).remove();
        // Re-index remaining rows
        $('#budget_phases_body tr').each(function (i) {
            $(this).find('.phase-index-display').text(i + 1);
        });
        if ($('#budget_phases_body tr').length === 0) {
            $('#no_phases_msg').show();
        }
        calculateTotals();
    });
});

function calculateTotals() {
    let totalCost = 0;
    let totalReceived = 0;
    const sym = getCurrencySymbol();

    $('.budget-phase-row').each(function () {
        const cost = parseFloat($(this).attr('data-cost')) || 0;
        const received = parseFloat($(this).attr('data-received')) || 0;
        totalCost += cost;
        totalReceived += received;
    });

    const pending = totalCost - totalReceived;

    $('#summary_total_cost').text(sym + ' ' + totalCost.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 2 }));
    $('#summary_received_amount').text(sym + ' ' + totalReceived.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 2 }));
    $('#summary_pending_amount').text(sym + ' ' + pending.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 2 }));

    if (pending <= 0) {
        $('#summary_pending_amount').css('color', '#16a34a'); // Green for no pending
    } else {
        $('#summary_pending_amount').css('color', '#ef4444'); // Red for outstanding
    }
}

window.saveBudget = function () {
    const phases = [];

    $('.budget-phase-row').each(function () {
        phases.push({
            phase_name: $(this).attr('data-name'),
            description: $(this).attr('data-desc'),
            cost: parseFloat($(this).attr('data-cost')) || 0,
            received_amount: parseFloat($(this).attr('data-received')) || 0,
            remark: $(this).attr('data-method'),
            received_date: $(this).attr('data-date')
        });
    });

    const btn = $('#btn_save_budget');
    const currency = $('#budget_currency').val();
    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Synchronizing Assets...');

    $.ajax({
        url: 'ajax/projects/ajax_save_project_budget.php',
        method: 'POST',
        data: {
            project_id: currentBudgetId,
            phases: JSON.stringify(phases),
            currency: currency
        },
        success: function (response) {
            btn.prop('disabled', false).html('<i class="fa fa-save"></i> Execute Synchronization');
            if (response.success) {
                $('#projectBudgetModal').modal('hide');
                showPremiumAlert('Budget architecture synchronized successfully!');
                loadProjects();
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: function () {
            btn.prop('disabled', false).html('<i class="fa fa-save"></i> Execute Synchronization');
            Swal.fire('Connection Error', 'Network synchronization failed.', 'error');
        }
    });
}


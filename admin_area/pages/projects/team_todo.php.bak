<?php
if (!isset($con)) {
    include(__DIR__ . '/../../includes/db.php');
}

$project_id = isset($_GET['project_id']) ? intval($_GET['project_id']) : 0;

if ($project_id == 0) {
    echo "<script>window.location.href='index.php?projects';</script>";
    exit();
}

$get_project = "SELECT cp.*, c.name as client_name FROM client_projects cp JOIN clients c ON cp.client_id = c.id WHERE cp.id = $project_id";
$run_project = mysqli_query($con, $get_project);
$project = mysqli_fetch_assoc($run_project);

if (!$project) {
    echo "<script>window.location.href='index.php?projects';</script>";
    exit();
}

$assigned_employees = array_filter(explode(',', $project['assigned_employees']), function($id) {
    return !empty(trim($id));
});

?>

<div class="page-wrapper premium-ui-enabled" style="background: #f8fafc; min-height: calc(100vh - 60px);">
    <div class="page-header-premium" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 20px; margin-bottom: 30px;">
        <div style="display: flex; align-items: center; gap: 20px;">
            <h1 style="margin: 0; font-size: 22px; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 10px;">
                <i class="fa fa-list-alt" style="color: #6366f1;"></i> Team To-Do
            </h1>
            <div style="padding: 8px 16px; background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; font-weight: 700; color: #334155; display: flex; align-items: center; gap: 8px; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
                <i class="fa fa-building-o" style="color: #64748b;"></i> <?php echo htmlspecialchars($project['project_name']); ?>
            </div>
        </div>
        <div class="header-actions-premium" style="display: flex; gap: 16px; align-items: center;">
            <div style="position: relative;">
                <i class="fa fa-search" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 14px;"></i>
                <input type="text" id="task-search" placeholder="Search tasks..." style="width: 250px; padding: 10px 15px 10px 38px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 13px; color: #334155; font-weight: 500; outline: none; transition: 0.3s; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
            </div>
            <a href="index.php?view_projects&id=<?php echo $project['client_id']; ?>" class="btn-premium-add" style="background: #fff !important; color: #475569 !important; border: 1.5px solid #e2e8f0 !important; box-shadow: 0 1px 2px rgba(0,0,0,0.05) !important;">
                <i class="fa fa-arrow-left"></i> Back to Project
            </a>
        </div>
    </div>

    <div class="todo-board">
        <?php
        if (empty($assigned_employees)) {
            echo '<div style="text-align: center; width: 100%; padding: 50px; color: #64748b; font-weight: 600;">No employees assigned to this project.</div>';
        } else {
            foreach ($assigned_employees as $emp_id) {
                $emp_id = intval($emp_id);
                $get_emp = mysqli_query($con, "SELECT * FROM emp_list WHERE id = $emp_id");
                $emp = mysqli_fetch_assoc($get_emp);
                if (!$emp) continue;
                
                $emp_name = htmlspecialchars($emp['name']);
                $emp_job = htmlspecialchars($emp['job_title'] ?? 'Employee');
                $emp_img = !empty($emp['employee_image']) ? 'uploads/' . htmlspecialchars($emp['employee_image']) : null;
        ?>
            <div class="todo-column" data-emp-id="<?php echo $emp_id; ?>">
                <div class="todo-col-header">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <?php if ($emp_img && file_exists('../../' . $emp_img)) { ?>
                            <img src="<?php echo $emp_img; ?>" class="emp-avatar">
                        <?php } else { ?>
                            <div class="emp-avatar-fallback"><?php echo strtoupper(substr($emp_name, 0, 1)); ?></div>
                        <?php } ?>
                        <div>
                            <div class="emp-name"><?php echo $emp_name; ?></div>
                            <div class="emp-role"><?php echo $emp_job; ?></div>
                        </div>
                    </div>
                    <button class="icon-btn"><i class="fa fa-ellipsis-v"></i></button>
                </div>

                <div class="add-task-trigger" onclick="showAddTask(<?php echo $emp_id; ?>)">
                    <i class="fa fa-plus-circle" style="font-size: 16px; color: #94a3b8;"></i>
                    <span>Add a task</span>
                </div>
                
                <div class="add-task-form" id="add-form-<?php echo $emp_id; ?>" style="display: none;">
                    <input type="text" class="task-input" id="task-input-<?php echo $emp_id; ?>" placeholder="What needs to be done?">
                    <div style="display: flex; gap: 8px; margin-top: 10px; flex-wrap: wrap;">
                        <input type="date" class="task-date-input" id="task-date-<?php echo $emp_id; ?>">
                        <select class="task-priority-input" id="task-priority-<?php echo $emp_id; ?>">
                            <option value="Low">Low Priority</option>
                            <option value="Medium" selected>Medium Priority</option>
                            <option value="High">High Priority</option>
                        </select>
                        <button class="save-task-btn" onclick="saveTask(<?php echo $emp_id; ?>)">Add</button>
                        <button class="cancel-task-btn" onclick="hideAddTask(<?php echo $emp_id; ?>)">Cancel</button>
                    </div>
                </div>

                <div class="task-list" id="task-list-<?php echo $emp_id; ?>">
                    <div style="text-align: center; padding: 20px;"><i class="fa fa-spinner fa-spin" style="color: #cbd5e1;"></i></div>
                </div>
            </div>
        <?php 
            }
        } 
        ?>
    </div>
</div>

<style>
    .todo-board {
        display: flex;
        gap: 24px;
        overflow-x: auto;
        padding-bottom: 20px;
        align-items: flex-start;
    }

    .todo-column {
        min-width: 380px;
        max-width: 380px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    }

    .todo-col-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .emp-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        object-fit: cover;
    }

    .emp-avatar-fallback {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: #f1f5f9;
        color: #475569;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 16px;
    }

    .emp-name {
        font-weight: 800;
        font-size: 15px;
        color: #0f172a;
    }

    .emp-role {
        font-weight: 600;
        font-size: 12px;
        color: #64748b;
        margin-top: 2px;
    }

    .icon-btn {
        background: transparent;
        border: none;
        color: #94a3b8;
        cursor: pointer;
        font-size: 16px;
        padding: 5px;
        transition: 0.2s;
    }

    .icon-btn:hover {
        color: #475569;
    }

    .add-task-trigger {
        display: flex;
        align-items: center;
        gap: 12px;
        color: #64748b;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        padding: 10px 0;
        margin-bottom: 10px;
        transition: 0.2s;
    }

    .add-task-trigger:hover {
        color: #3b82f6;
    }

    .add-task-trigger:hover i {
        color: #3b82f6 !important;
    }

    .add-task-form {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 15px;
    }

    .task-input {
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 14px;
        font-weight: 500;
        outline: none;
        transition: 0.2s;
    }

    .task-input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .task-date-input, .task-priority-input {
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 6px 10px;
        font-size: 12px;
        font-weight: 600;
        color: #475569;
        outline: none;
    }

    .save-task-btn {
        background: #3b82f6;
        color: #fff;
        border: none;
        border-radius: 6px;
        padding: 6px 16px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
    }

    .cancel-task-btn {
        background: #fff;
        color: #64748b;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 6px 16px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
    }

    .task-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 0;
        border-bottom: 1px solid #f1f5f9;
        transition: 0.2s;
    }

    .task-item:last-child {
        border-bottom: none;
    }

    .task-checkbox {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        border: 2px solid #cbd5e1;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: 0.2s;
        flex-shrink: 0;
    }

    .task-checkbox:hover {
        border-color: #3b82f6;
    }

    .task-checkbox i {
        display: none;
        color: #fff;
        font-size: 12px;
    }

    .task-item.completed .task-checkbox {
        background: #3b82f6;
        border-color: #3b82f6;
    }

    .task-item.completed .task-checkbox i {
        display: block;
    }

    .task-name {
        font-size: 14px;
        font-weight: 600;
        color: #334155;
        flex: 1;
        transition: 0.2s;
    }

    .task-item.completed .task-name {
        text-decoration: line-through;
        color: #94a3b8;
    }

    .task-meta {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .date-badge {
        background: #eff6ff;
        color: #3b82f6;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 700;
    }

    .priority-flag {
        font-size: 12px;
    }
    .priority-High { color: #ef4444; }
    .priority-Medium { color: #f59e0b; }
    .priority-Low { color: #22c55e; }

</style>

<script>
    const projectId = <?php echo $project_id; ?>;

    $(document).ready(function() {
        // Load tasks for all columns
        $('.todo-column').each(function() {
            const empId = $(this).data('emp-id');
            loadTasks(empId);
        });

        // Search functionality
        $('#task-search').on('keyup', function() {
            const term = $(this).val().toLowerCase();
            $('.task-item').each(function() {
                const name = $(this).find('.task-name').text().toLowerCase();
                if (name.includes(term)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
    });

    function showAddTask(empId) {
        $(`#add-form-${empId}`).slideDown(200);
        $(`#task-input-${empId}`).focus();
    }

    function hideAddTask(empId) {
        $(`#add-form-${empId}`).slideUp(200);
        $(`#task-input-${empId}`).val('');
        $(`#task-date-${empId}`).val('');
    }

    function loadTasks(empId) {
        $.ajax({
            url: 'ajax/projects/ajax_get_team_todos.php',
            method: 'POST',
            data: { project_id: projectId, emp_id: empId },
            success: function(res) {
                if (res.success) {
                    renderTasks(empId, res.tasks);
                }
            }
        });
    }

    function renderTasks(empId, tasks) {
        const list = $(`#task-list-${empId}`);
        list.empty();
        
        if (tasks.length === 0) {
            list.html('<div style="color: #94a3b8; font-size: 13px; text-align: center; padding: 15px 0;">No tasks yet</div>');
            return;
        }

        tasks.forEach(task => {
            const isCompleted = task.status == 1;
            const itemClass = isCompleted ? 'task-item completed' : 'task-item';
            
            let dateBadge = '';
            if (task.due_date) {
                const due = new Date(task.due_date);
                const today = new Date();
                const tomorrow = new Date();
                tomorrow.setDate(tomorrow.getDate() + 1);
                
                let dateStr = due.toLocaleDateString('en-GB', { day: 'numeric', month: 'short' });
                if (due.toDateString() === today.toDateString()) {
                    dateStr = 'Today';
                } else if (due.toDateString() === tomorrow.toDateString()) {
                    dateStr = 'Tomorrow';
                }
                dateBadge = `<div class="date-badge">${dateStr}</div>`;
            }

            let priorityHtml = '';
            if (task.priority) {
                priorityHtml = `<div class="priority-flag priority-${task.priority}"><i class="fa fa-flag"></i> ${task.priority}</div>`;
            }

            const html = `
                <div class="${itemClass}" data-task-id="${task.id}">
                    <div class="task-checkbox" onclick="toggleTask(${task.id}, ${empId}, ${isCompleted ? 0 : 1})">
                        <i class="fa fa-check"></i>
                    </div>
                    <div class="task-name">${escapeHtml(task.task_name)}</div>
                    <div class="task-meta">
                        ${priorityHtml}
                        ${dateBadge}
                        <div class="dropdown">
                            <button class="icon-btn" data-toggle="dropdown"><i class="fa fa-ellipsis-v"></i></button>
                            <ul class="dropdown-menu dropdown-menu-right" style="border-radius: 12px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
                                <li><a href="#" onclick="deleteTask(${task.id}, ${empId}); return false;" style="color: #ef4444; font-weight: 600; padding: 10px 20px;"><i class="fa fa-trash-o" style="margin-right: 8px;"></i> Delete</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            `;
            list.append(html);
        });
    }

    function saveTask(empId) {
        const name = $(`#task-input-${empId}`).val().trim();
        const date = $(`#task-date-${empId}`).val();
        const priority = $(`#task-priority-${empId}`).val();

        if (!name) return;

        $.ajax({
            url: 'ajax/projects/ajax_add_team_todo.php',
            method: 'POST',
            data: {
                project_id: projectId,
                emp_id: empId,
                task_name: name,
                due_date: date,
                priority: priority
            },
            success: function(res) {
                if (res.success) {
                    hideAddTask(empId);
                    loadTasks(empId);
                } else {
                    Swal.fire("Error", "Could not add task.", "error");
                }
            }
        });
    }

    function toggleTask(taskId, empId, newStatus) {
        $.ajax({
            url: 'ajax/projects/ajax_toggle_team_todo.php',
            method: 'POST',
            data: { task_id: taskId, status: newStatus },
            success: function(res) {
                if (res.success) {
                    loadTasks(empId);
                }
            }
        });
    }

    function deleteTask(taskId, empId) {
        Swal.fire({
            title: 'Delete Task?',
            text: "This cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Yes, delete it'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'ajax/projects/ajax_delete_team_todo.php',
                    method: 'POST',
                    data: { task_id: taskId },
                    success: function(res) {
                        if (res.success) {
                            loadTasks(empId);
                        }
                    }
                });
            }
        });
    }

    function escapeHtml(unsafe) {
        return unsafe
             .replace(/&/g, "&amp;")
             .replace(/</g, "&lt;")
             .replace(/>/g, "&gt;")
             .replace(/"/g, "&quot;")
             .replace(/'/g, "&#039;");
    }
</script>

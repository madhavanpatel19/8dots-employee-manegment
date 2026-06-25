<?php
if (!isset($con)) { include(__DIR__ . '/../../includes/db.php'); }

if (isset($_GET['project_id'])) {
    $project_id = mysqli_real_escape_string($con, $_GET['project_id']);
    
    $get_docs = "SELECT * FROM project_documents WHERE project_id = '$project_id' ORDER BY created_at DESC";
    $run_docs = mysqli_query($con, $get_docs);
    
    if (mysqli_num_rows($run_docs) > 0) {
        echo '<div style="display: grid; gap: 16px;">';
        while ($doc = mysqli_fetch_assoc($run_docs)) {
            $doc_id = $doc['id'];
            $name = htmlspecialchars($doc['document_name']);
            $path = htmlspecialchars($doc['file_path']);
            $date = date('d M, Y', strtotime($doc['created_at']));
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            
            // Icon mapping
            $icon = 'fa-file-o';
            $icon_color = '#6366f1';
            $icon_bg = 'rgba(99, 102, 241, 0.08)';
            
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) { $icon = 'fa-file-image-o'; $icon_color = '#ec4899'; $icon_bg = 'rgba(236, 72, 153, 0.08)'; }
            elseif ($ext == 'pdf') { $icon = 'fa-file-pdf-o'; $icon_color = '#ef4444'; $icon_bg = 'rgba(239, 68, 68, 0.08)'; }
            elseif (in_array($ext, ['doc', 'docx'])) { $icon = 'fa-file-word-o'; $icon_color = '#2563eb'; $icon_bg = 'rgba(37, 99, 235, 0.08)'; }
            elseif (in_array($ext, ['xls', 'xlsx'])) { $icon = 'fa-file-excel-o'; $icon_color = '#16a34a'; $icon_bg = 'rgba(22, 163, 74, 0.08)'; }
            elseif ($ext == 'zip') { $icon = 'fa-file-archive-o'; $icon_color = '#ca8a04'; $icon_bg = 'rgba(202, 138, 4, 0.08)'; }
            
            echo '
            <div class="artifact-card-premium" style="background: #fff; border: 1.5px solid #f1f5f9; border-radius: 20px; padding: 18px 22px; display: flex; align-items: center; justify-content: space-between; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);" onmouseover="this.style.borderColor=\'#e2e8f0\'; this.style.transform=\'translateY(-2px)\'; this.style.boxShadow=\'0 10px 15px -3px rgba(0, 0, 0, 0.05)\'" onmouseout="this.style.borderColor=\'#f1f5f9\'; this.style.transform=\'translateY(0)\'; this.style.boxShadow=\'0 4px 6px -1px rgba(0, 0, 0, 0.02)\'">
                <div style="display: flex; align-items: center; gap: 20px;">
                    <div style="width: 54px; height: 54px; background: '.$icon_bg.'; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 22px; color: '.$icon_color.';">
                        <i class="fa '.$icon.'"></i>
                    </div>
                    <div>
                        <div style="font-weight: 800; color: #0f172a; font-size: 15px; letter-spacing: -0.2px;">'.$name.'</div>
                        <div style="display: flex; align-items: center; gap: 8px; margin-top: 4px;">
                            <span style="font-size: 11px; color: #94a3b8; font-weight: 750; text-transform: uppercase; letter-spacing: 0.5px;">'.$ext.' Artifact</span>
                            <span style="width: 4px; height: 4px; background: #cbd5e1; border-radius: 50%;"></span>
                            <span style="font-size: 11px; color: #64748b; font-weight: 600;">Recorded '.$date.'</span>
                        </div>
                    </div>
                </div>
                <div style="display: flex; gap: 10px;">
                    <a href="'.$path.'" target="_blank" class="btn-icon-premium" style="width: 38px; height: 38px; background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 12px; transition: 0.2s;" title="Open Artifact">
                        <i class="fa fa-external-link" style="color: #64748b; font-size: 14px;"></i>
                    </a>
                    <button onclick="deleteDoc('.$doc_id.', '.$project_id.')" class="btn-icon-premium" style="width: 38px; height: 38px; background: #fff5f5; border: 1.5px solid #ffe4e4; border-radius: 12px; transition: 0.2s;" title="Purge Artifact">
                        <i class="fa fa-trash-o" style="color: #ef4444; font-size: 15px;"></i>
                    </button>
                </div>
            </div>';
        }
        echo '</div>';
    } else {
        echo '
        <div style="text-align: center; padding: 40px 20px; color: #94a3b8;">
            <div style="width: 64px; height: 64px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                <i class="fa fa-folder-open-o" style="font-size: 28px;"></i>
            </div>
            <h4 style="font-weight: 800; color: #1e293b; margin-bottom: 5px;">Empty Repository</h4>
            <p style="font-size: 13px;">No documents have been attached to this project yet.</p>
        </div>';
    }
}
?>

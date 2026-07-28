<!-- Quick Follow-up Modal -->
<div id="followupModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 20px; border: none; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">
            <form method="POST">
                <div class="modal-header" style="background: var(--p-bg-color); color: var(--p-bg); padding: 25px 30px 20px 30px; border-bottom: 1px solid #f1f5f9; position: relative;">
                    <button type="button" data-dismiss="modal" aria-label="Close" style="position: absolute; right: 25px; top: 25px; background: var(--p-bg-color); color: #fff; border: none; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s; font-size: 14px;">
                        <i class="fa fa-times"></i>
                    </button>
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <div style="background:#fff; color:var(--p-bg-color); width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="fa fa-history" style="font-size: 18px;"></i>
                        </div>
                        <div>
                            <h4 class="modal-title" style="font-weight: 800; font-size: 20px; margin: 0; color: #fff;">Add Follow-up</h4>
                            <p style="margin: 4px 0 0 0; font-size: 14px; color: #94a3b8; font-weight: 500;">Record interaction for <span id="modalClientName" style="color: #64748b; font-weight: 700;"></span></p>
                        </div>
                    </div>
                </div>
                <div class="modal-body" style="padding: 25px; background: #fff;">
                    <input type="hidden" name="lead_id" id="modalLeadId">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Interaction Date</label>
                                <input type="date" name="followup_date" class="p-input-premium" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Method</label>
                                <select name="followup_method" class="p-input-premium">
                                    <option value="Phone">Phone</option>
                                    <option value="WhatsApp">WhatsApp</option>
                                    <option value="Email">Email</option>
                                    <option value="Meeting">Meeting</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group" style="margin-top: 15px;">
                        <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Follow-up Type</label>
                        <select name="followup_type" class="p-input-premium">
                            <option value="General Remark">General Remark</option>
                            <option value="After 2 Days">After 2 Days</option>
                            <option value="1 Month Before">1 Month Before</option>
                            <option value="Next Week">Next Week</option>
                            <option value="Urgent">Urgent</option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-top: 15px;">
                        <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Remark / Discussion</label>
                        <textarea name="remark" class="p-input-premium" rows="3" style="height: auto; min-height: 100px; padding: 15px;" placeholder="Enter what was discussed..."></textarea>
                    </div>

                    <div class="form-group" style="margin-top: 15px; padding: 15px; background: #f0fdf4; border-radius: 12px; border: 1px solid #dcfce7;">
                        <label style="font-weight: 700; color: #166534; margin-bottom: 8px; display: block;">Schedule Next Follow-up</label>
                        <input type="date" name="next_followup_date" class="p-input-premium" style="border-color: #bbf7d0;" min="<?php echo date('Y-m-d'); ?>">
                        <small style="color: #16a34a; margin-top: 5px; display: block;"><i class="fa fa-info-circle"></i> This will update the lead's main follow-up date.</small>
                    </div>
                </div>
                <div class="modal-footer" style="padding: 20px; background: #f8fafc; border-top: 1px solid #e2e8f0; text-align: right;">
                    <button type="button" class="btn-premium-cancel" data-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_quick_followup" class="btn-premium-add">
                        <i class="fa fa-save"></i> Save Follow-up
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
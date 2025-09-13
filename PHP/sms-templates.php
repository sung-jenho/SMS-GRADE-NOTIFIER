<!-- SMS Templates Tab -->
<div class="settings-card">
  <!-- Modern Header with Gradient -->
  <div class="modern-settings-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 2rem; border-radius: 12px; margin-bottom: 2rem;">
    <div class="d-flex align-items-center">
      <div class="header-icon-wrapper" style="background: rgba(255,255,255,0.2); padding: 1rem; border-radius: 12px; margin-right: 1.5rem;">
        <i class="bi bi-chat-text" style="font-size: 2rem;"></i>
      </div>
      <div>
        <h3 class="mb-1" style="font-weight: 600;">SMS Message Templates</h3>
        <p class="mb-0" style="opacity: 0.9;">Configure automated SMS messages for different grade ranges</p>
      </div>
    </div>
  </div>

  <!-- Placeholder Info Card -->
  <div class="info-card" style="background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%); border: none; border-radius: 12px; padding: 1.5rem; margin-bottom: 2rem;">
    <div class="d-flex align-items-start">
      <div class="info-icon" style="background: #2196f3; color: white; padding: 0.75rem; border-radius: 8px; margin-right: 1rem;">
        <i class="bi bi-lightbulb"></i>
      </div>
      <div>
        <h6 class="mb-2" style="color: #1976d2; font-weight: 600;">Template Placeholders</h6>
        <p class="mb-2" style="color: #424242;">Use these placeholders in your messages:</p>
        <div class="placeholder-tags">
          <span class="badge" style="background: #2196f3; margin-right: 0.5rem; padding: 0.5rem 0.75rem;">{student_name}</span>
          <span class="badge" style="background: #4caf50; margin-right: 0.5rem; padding: 0.5rem 0.75rem;">{grade}</span>
          <span class="badge" style="background: #ff9800; margin-right: 0.5rem; padding: 0.5rem 0.75rem;">{subject}</span>
        </div>
      </div>
    </div>
  </div>
  
  <div id="smsTemplatesContainer">
    <?php if (!empty($sms_templates)): ?>
      <?php foreach ($sms_templates as $template): ?>
        <div class="modern-template-card" style="background: white; border-radius: 16px; padding: 2rem; margin-bottom: 1.5rem; box-shadow: 0 4px 20px rgba(0,0,0,0.08); border: 1px solid #e0e7ff;">
          <div class="template-header" style="margin-bottom: 1.5rem;">
            <div class="d-flex justify-content-between align-items-center">
              <div class="template-info">
                <h6 class="template-title" style="color: #1e293b; font-weight: 600; margin-bottom: 0.25rem;">
                  <i class="bi bi-tag-fill me-2" style="color: #667eea;"></i>
                  <?= htmlspecialchars($template['template_name']) ?>
                </h6>
                <span class="grade-badge" style="background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.875rem; font-weight: 500;">
                  Grade Range: <?= htmlspecialchars($template['grade_range']) ?>
                </span>
              </div>
              <div class="template-status">
                <div class="form-check form-switch" style="margin: 0;">
                  <input class="form-check-input template-active" type="checkbox" 
                         data-template-id="<?= $template['id'] ?>" 
                         <?= $template['is_active'] ? 'checked' : '' ?>
                         style="width: 3rem; height: 1.5rem;">
                  <label class="form-check-label" style="color: #64748b; font-weight: 500; margin-left: 0.5rem;">
                    <?= $template['is_active'] ? 'Active' : 'Inactive' ?>
                  </label>
                </div>
              </div>
            </div>
          </div>
          
          <div class="template-content">
            <label class="modern-form-label" style="color: #374151; font-weight: 600; margin-bottom: 0.75rem; display: block;">
              <i class="bi bi-chat-square-text me-2" style="color: #667eea;"></i>
              Message Template
            </label>
            <textarea class="modern-form-control message-template" 
                      data-template-id="<?= $template['id'] ?>"
                      rows="4" 
                      style="border: 2px solid #e2e8f0; border-radius: 12px; padding: 1rem; font-family: 'Segoe UI', sans-serif; resize: vertical; transition: all 0.3s ease; background: #f8fafc;"
                      placeholder="Enter your SMS message template here..."><?= htmlspecialchars($template['message_template']) ?></textarea>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
  
  <!-- Save Button -->
  <div class="save-section" style="text-align: center; margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #e2e8f0;">
    <button type="button" class="modern-save-btn" id="saveTemplatesBtn" 
            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                   color: white; 
                   border: none; 
                   padding: 1rem 2.5rem; 
                   border-radius: 12px; 
                   font-weight: 600; 
                   font-size: 1rem;
                   box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
                   transition: all 0.3s ease;
                   cursor: pointer;">
      <i class="bi bi-check-circle me-2"></i>Save All Templates
    </button>
  </div>
</div>

<?php /** @var array $settings */ /** @var array $sms_templates */ /** @var array $user_data */ ?>

<!-- Settings Navigation Tabs -->
<div class="settings-container">
  <div class="modern-nav-container mb-3">
    <nav class="modern-nav" id="settingsTabs" role="tablist">
      <button class="modern-nav-item active" id="admin-profile-tab" data-bs-toggle="pill" data-bs-target="#admin-profile" type="button" role="tab">
        <i class="bi bi-person-circle"></i>
        <span>Admin Profile</span>
      </button>
      <button class="modern-nav-item" id="system-config-tab" data-bs-toggle="pill" data-bs-target="#system-config" type="button" role="tab">
        <i class="bi bi-gear"></i>
        <span>System Config</span>
      </button>
      <button class="modern-nav-item" id="sms-templates-tab" data-bs-toggle="pill" data-bs-target="#sms-templates" type="button" role="tab">
        <i class="bi bi-chat-text"></i>
        <span>SMS Templates</span>
      </button>
      <button class="modern-nav-item" id="database-tab" data-bs-toggle="pill" data-bs-target="#database" type="button" role="tab">
        <i class="bi bi-database"></i>
        <span>Database</span>
      </button>
      <button class="modern-nav-item" id="data-export-tab" data-bs-toggle="pill" data-bs-target="#data-export" type="button" role="tab">
        <i class="bi bi-download"></i>
        <span>Data Export</span>
      </button>
    </nav>
  </div>

  <div class="tab-content" id="settingsTabContent">
    <!-- Admin Profile Tab -->
    <div class="tab-pane fade show active" id="admin-profile" role="tabpanel">
      <?php include 'admin-profile.php'; ?>
    </div>

    <!-- System Configuration Tab -->
    <div class="tab-pane fade" id="system-config" role="tabpanel">
      <?php include 'system-config.php'; ?>
    </div>

    <!-- SMS Templates Tab -->
    <div class="tab-pane fade" id="sms-templates" role="tabpanel">
      <?php include 'sms-templates.php'; ?>
    </div>

    <!-- Database Management Tab -->
    <div class="tab-pane fade" id="database" role="tabpanel">
      <?php include 'database-management.php'; ?>
    </div>

    <!-- Data Export Tab -->
    <div class="tab-pane fade" id="data-export" role="tabpanel">
      <?php include 'data-export.php'; ?>
    </div>
  </div>
</div>

<!-- Loading Animation Overlay -->
<div id="loadingModal" class="loading-overlay">
    <div id="loadingAnimation" class="loading-animation"></div>
    <div class="loading-text" id="loadingMessage">Processing...</div>
    <div class="loading-subtext">Please wait while we process your request</div>
</div>

<style>
.settings-container {
  width: 100%;
  max-width: none;
}

.settings-card {
  background: #ffffff;
  border-radius: 20px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
  border: 1px solid rgba(0, 0, 0, 0.04);
  padding: 2rem;
  margin-bottom: 2rem;
}

.template-item {
  background: #f8f9fa;
  transition: all 0.3s ease;
}

.template-item:hover {
  background: #e9ecef;
}

.card {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

/* Loading overlay styles */
.loading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.7);
  backdrop-filter: blur(8px);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  opacity: 0;
  visibility: hidden;
  transition: all 0.3s ease;
}

.loading-overlay.show {
  opacity: 1;
  visibility: visible;
}

.loading-animation {
  width: 120px;
  height: 120px;
  margin-bottom: 24px;
}

.loading-text {
  color: #ffffff;
  font-size: 1.1rem;
  font-weight: 600;
  text-align: center;
  margin-bottom: 8px;
}

.loading-subtext {
  color: rgba(255, 255, 255, 0.8);
  font-size: 0.875rem;
  font-weight: 400;
  text-align: center;
}

/* Modern Navigation Styles */
.modern-nav-container {
  background: #f8f9fa;
  border-radius: 16px;
  padding: 8px;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
  border: 1px solid rgba(0, 0, 0, 0.06);
  max-width: 800px;
  margin: 0 auto;
}

.modern-nav {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 4px;
  background: transparent;
  border-radius: 12px;
  overflow-x: auto;
  scrollbar-width: none;
  -ms-overflow-style: none;
}

.modern-nav::-webkit-scrollbar {
  display: none;
}

.modern-nav-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 16px 20px;
  border: none;
  background: transparent;
  border-radius: 12px;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  cursor: pointer;
  min-width: 120px;
  position: relative;
  color: #374151;
  font-weight: 500;
  font-size: 0.875rem;
}

.modern-nav-item i {
  font-size: 1.5rem;
  margin-bottom: 6px;
  color: #6b7280;
  transition: all 0.3s ease;
}

.modern-nav-item span {
  font-size: 0.85rem;
  font-weight: 500;
  color: #6b7280;
  transition: all 0.3s ease;
  white-space: nowrap;
}

.modern-nav-item:hover {
  background: rgba(251, 146, 60, 0.08);
  transform: translateY(-1px);
}

.modern-nav-item:hover i,
.modern-nav-item:hover span {
  color: #fb923c;
}

.modern-nav-item.active {
  background: #ffffff;
  box-shadow: 0 2px 8px rgba(251, 146, 60, 0.15);
  transform: translateY(-1px);
}

.modern-nav-item.active i,
.modern-nav-item.active span {
  color: #fb923c;
}

.modern-nav-item:focus {
  outline: none;
  box-shadow: 0 0 0 3px rgba(251, 146, 60, 0.2);
}

/* Dark mode styles */
.dark-mode .modern-nav-container {
  background: #1f2937;
  border-color: rgba(255, 255, 255, 0.1);
}

.dark-mode .modern-nav-item {
  color: #e5e7eb;
}

.dark-mode .modern-nav-item i,
.dark-mode .modern-nav-item span {
  color: #9ca3af;
}

.dark-mode .modern-nav-item:hover {
  background: rgba(251, 146, 60, 0.15);
}

.dark-mode .modern-nav-item:hover i,
.dark-mode .modern-nav-item:hover span {
  color: #fdba74;
}

.dark-mode .modern-nav-item.active {
  background: #374151;
  box-shadow: 0 2px 8px rgba(251, 146, 60, 0.25);
}

.dark-mode .modern-nav-item.active i,
.dark-mode .modern-nav-item.active span {
  color: #fdba74;
}

/* Responsive design */
@media (max-width: 768px) {
  .modern-nav-item {
    min-width: 80px;
    padding: 10px 12px;
  }
  
  .modern-nav-item i {
    font-size: 1.1rem;
  }
  
  .modern-nav-item span {
    font-size: 0.7rem;
  }
}

/* Modern Profile Container Styles */
.modern-profile-container {
  background: #ffffff;
  border-radius: 24px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
  border: 1px solid rgba(0, 0, 0, 0.04);
}

.profile-header-section {
  background: linear-gradient(135deg, #fb923c 0%, #fdba74 100%);
  padding: 2rem;
  position: relative;
}

.profile-avatar-section {
  display: flex;
  align-items: center;
  gap: 1.5rem;
}

.modern-profile-picture-container {
  position: relative;
  cursor: pointer;
  flex-shrink: 0;
}

.modern-profile-picture {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  object-fit: cover;
  border: 4px solid rgba(255, 255, 255, 0.3);
  transition: all 0.3s ease;
}

.modern-profile-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.7);
  border-radius: 50%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: white;
  opacity: 0;
  transition: opacity 0.3s ease;
  font-size: 0.75rem;
  font-weight: 500;
}

.modern-profile-picture-container:hover .modern-profile-overlay {
  opacity: 1;
}

.modern-profile-overlay i {
  font-size: 1.25rem;
  margin-bottom: 4px;
}

.profile-info-preview {
  color: white;
  flex: 1;
}

.profile-name-display {
  font-size: 1.75rem;
  font-weight: 700;
  margin: 0 0 0.25rem 0;
  color: white;
}

.profile-email-display {
  font-size: 1rem;
  margin: 0 0 0.75rem 0;
  opacity: 0.9;
  color: white;
}

.profile-status-badge {
  background: rgba(255, 255, 255, 0.2);
  color: white;
  padding: 0.375rem 0.75rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 600;
  backdrop-filter: blur(10px);
}

.profile-form-section {
  padding: 2rem;
}

.section-header {
  margin-bottom: 2rem;
}

.section-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 0.5rem 0;
}

.section-subtitle {
  color: #6b7280;
  margin: 0;
  font-size: 0.875rem;
}

.form-group-section {
  margin-bottom: 2rem;
}

.form-section-title {
  font-size: 1rem;
  font-weight: 600;
  color: #374151;
  margin: 0 0 1rem 0;
  padding-bottom: 0.5rem;
  border-bottom: 2px solid #f3f4f6;
}

.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
}

.form-field.full-width {
  grid-column: 1 / -1;
}

.modern-form-label {
  display: flex;
  align-items: center;
  font-weight: 500;
  color: #374151;
  margin-bottom: 0.5rem;
  font-size: 0.875rem;
}

.modern-form-control {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  font-size: 0.875rem;
  transition: all 0.3s ease;
  background: #ffffff;
}

.modern-form-control:focus {
  outline: none;
  border-color: #fb923c;
  box-shadow: 0 0 0 3px rgba(251, 146, 60, 0.1);
}

.modern-form-control.readonly {
  background: #f9fafb;
  color: #6b7280;
  cursor: not-allowed;
}

.security-notice {
  background: #fef3c7;
  border: 1px solid #fbbf24;
  border-radius: 12px;
  padding: 0.75rem 1rem;
  margin-bottom: 1.5rem;
  display: flex;
  align-items: center;
  color: #92400e;
  font-size: 0.875rem;
}

.form-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  margin-top: 2rem;
  padding-top: 2rem;
  border-top: 1px solid #e5e7eb;
}

.modern-btn-primary {
  background: linear-gradient(135deg, #fb923c 0%, #fdba74 100%);
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 12px;
  font-weight: 600;
  font-size: 0.875rem;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
}

.modern-btn-primary:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(251, 146, 60, 0.3);
}

.modern-btn-secondary {
  background: #f3f4f6;
  color: #374151;
  border: 1px solid #d1d5db;
  padding: 0.75rem 1.5rem;
  border-radius: 12px;
  font-weight: 500;
  font-size: 0.875rem;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
}

.modern-btn-secondary:hover {
  background: #e5e7eb;
  transform: translateY(-1px);
}

/* Dark mode styles */
.dark-mode .modern-profile-container {
  background: #1f2937;
  border-color: rgba(255, 255, 255, 0.1);
}

.dark-mode .section-title {
  color: #f9fafb;
}

.dark-mode .section-subtitle {
  color: #9ca3af;
}

.dark-mode .form-section-title {
  color: #e5e7eb;
  border-bottom-color: #374151;
}

.dark-mode .modern-form-label {
  color: #e5e7eb;
}

.dark-mode .modern-form-control {
  background: #374151;
  border-color: #4b5563;
  color: #f9fafb;
}

.dark-mode .modern-form-control:focus {
  border-color: #fdba74;
  box-shadow: 0 0 0 3px rgba(253, 186, 116, 0.1);
}

.dark-mode .modern-form-control.readonly {
  background: #2d3748;
  color: #9ca3af;
}

.dark-mode .security-notice {
  background: #451a03;
  border-color: #92400e;
  color: #fbbf24;
}

.dark-mode .form-actions {
  border-top-color: #374151;
}

.dark-mode .modern-btn-secondary {
  background: #374151;
  color: #e5e7eb;
  border-color: #4b5563;
}

.dark-mode .modern-btn-secondary:hover {
  background: #4b5563;
}

/* Responsive design */
@media (max-width: 768px) {
  .profile-avatar-section {
    flex-direction: column;
    text-align: center;
    gap: 1rem;
  }
  
  .form-grid {
    grid-template-columns: 1fr;
  }
  
  .form-actions {
    flex-direction: column;
  }
  
  .modern-profile-picture {
    width: 80px;
    height: 80px;
  }
  
  .profile-name-display {
    font-size: 1.5rem;
  }
}

/* Modern Configuration Container Styles */
.modern-config-container {
  background: #ffffff;
  border-radius: 24px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
  border: 1px solid rgba(0, 0, 0, 0.04);
}

.config-header-section {
  background: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%);
  padding: 2rem;
  position: relative;
}

.config-header-content {
  display: flex;
  align-items: center;
  gap: 1.5rem;
}

.config-icon-wrapper {
  width: 80px;
  height: 80px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  backdrop-filter: blur(10px);
  flex-shrink: 0;
}

.config-icon-wrapper i {
  font-size: 2rem;
  color: white;
}

.config-header-info {
  color: white;
  flex: 1;
}

.config-title {
  font-size: 1.75rem;
  font-weight: 700;
  margin: 0 0 0.25rem 0;
  color: white;
}

.config-subtitle {
  font-size: 1rem;
  margin: 0;
  opacity: 0.9;
  color: white;
}

.config-form-section {
  padding: 2rem;
}

.config-group-section {
  margin-bottom: 2.5rem;
}

.config-section-title {
  font-size: 1rem;
  font-weight: 600;
  color: #374151;
  margin: 0 0 1.5rem 0;
  padding-bottom: 0.75rem;
  border-bottom: 2px solid #f3f4f6;
  display: flex;
  align-items: center;
}

.config-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2rem;
}

.config-field {
  display: flex;
  flex-direction: column;
}

.modern-config-label {
  display: flex;
  align-items: center;
  font-weight: 500;
  color: #374151;
  margin-bottom: 0.5rem;
  font-size: 0.875rem;
}

.modern-config-select,
.modern-config-control {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  font-size: 0.875rem;
  transition: all 0.3s ease;
  background: #ffffff;
}

.modern-config-select:focus,
.modern-config-control:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.input-with-unit {
  position: relative;
  display: flex;
  align-items: center;
}

.input-unit {
  position: absolute;
  right: 1rem;
  color: #6b7280;
  font-size: 0.875rem;
  font-weight: 500;
  pointer-events: none;
}

.config-help-text {
  color: #6b7280;
  font-size: 0.75rem;
  margin-top: 0.5rem;
}

.config-toggle-section {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  background: #f8fafc;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
}

.toggle-info {
  flex: 1;
}

.modern-toggle-wrapper {
  position: relative;
  flex-shrink: 0;
}

.modern-toggle-input {
  opacity: 0;
  width: 0;
  height: 0;
}

.modern-toggle-label {
  display: inline-block;
  width: 50px;
  height: 26px;
  background: #cbd5e1;
  border-radius: 26px;
  position: relative;
  cursor: pointer;
  transition: background 0.3s ease;
}

.modern-toggle-slider {
  position: absolute;
  top: 3px;
  left: 3px;
  width: 20px;
  height: 20px;
  background: white;
  border-radius: 50%;
  transition: transform 0.3s ease;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.modern-toggle-input:checked + .modern-toggle-label {
  background: #fb923c;
}

.modern-toggle-input:checked + .modern-toggle-label .modern-toggle-slider {
  transform: translateX(24px);
}

.system-status-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
}

.status-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1rem;
  background: #f8fafc;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
}

.status-icon {
  width: 40px;
  height: 40px;
  background: #e0f2fe;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.status-icon i {
  font-size: 1.25rem;
  color: #0369a1;
}

.status-info {
  display: flex;
  flex-direction: column;
}

.status-label {
  font-size: 0.75rem;
  color: #6b7280;
  font-weight: 500;
}

.status-value {
  font-size: 0.875rem;
  color: #374151;
  font-weight: 600;
}

.status-value.connected {
  color: #059669;
}

.config-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  margin-top: 2rem;
  padding-top: 2rem;
  border-top: 1px solid #e5e7eb;
}

/* Dark mode styles for config */
.dark-mode .modern-config-container {
  background: #1f2937;
  border-color: rgba(255, 255, 255, 0.1);
}

.dark-mode .config-section-title {
  color: #e5e7eb;
  border-bottom-color: #374151;
}

.dark-mode .modern-config-label {
  color: #e5e7eb;
}

.dark-mode .modern-config-select,
.dark-mode .modern-config-control {
  background: #374151;
  border-color: #4b5563;
  color: #f9fafb;
}

.dark-mode .modern-config-select:focus,
.dark-mode .modern-config-control:focus {
  border-color: #60a5fa;
  box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.1);
}

.dark-mode .config-help-text {
  color: #9ca3af;
}

.dark-mode .config-toggle-section {
  background: #374151;
  border-color: #4b5563;
}

.dark-mode .status-item {
  background: #374151;
  border-color: #4b5563;
}

.dark-mode .status-icon {
  background: #1e3a8a;
}

.dark-mode .status-icon i {
  color: #60a5fa;
}

.dark-mode .status-label {
  color: #9ca3af;
}

.dark-mode .status-value {
  color: #e5e7eb;
}

.dark-mode .config-actions {
  border-top-color: #374151;
}

/* Responsive design for config */
@media (max-width: 768px) {
  .config-header-content {
    flex-direction: column;
    text-align: center;
    gap: 1rem;
  }
  
  .config-grid {
    grid-template-columns: 1fr;
  }
  
  .system-status-grid {
    grid-template-columns: 1fr;
  }
  
  .config-toggle-section {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }
  
  .config-actions {
    flex-direction: column;
  }
}
</style>

<script src="../JAVASCRIPTS/settings-manager.js"></script>

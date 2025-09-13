<!-- System Configuration Tab -->
<div class="modern-config-container">
  <!-- Configuration Header Section -->
  <div class="config-header-section">
    <div class="config-header-content">
      <div class="config-icon-wrapper">
        <i class="bi bi-gear-fill"></i>
      </div>
      <div class="config-header-info">
        <h4 class="config-title">System Configuration</h4>
        <p class="config-subtitle">Configure system-wide settings and preferences</p>
      </div>
    </div>
  </div>

  <!-- Configuration Form Section -->
  <div class="config-form-section">
    <form id="systemConfigForm" class="modern-config-form">
      <!-- General Settings -->
      <div class="config-group-section">
        <h6 class="config-section-title">
          <i class="bi bi-globe me-2"></i>Regional Settings
        </h6>
        <div class="config-grid">
          <div class="config-field">
            <label for="timezone" class="modern-config-label">
              <i class="bi bi-clock me-2"></i>System Timezone
            </label>
            <select class="modern-config-select" id="timezone" name="timezone">
              <option value="Asia/Manila" <?= ($settings['timezone'] ?? 'Asia/Manila') === 'Asia/Manila' ? 'selected' : '' ?>>Asia/Manila (UTC+8)</option>
              <option value="UTC" <?= ($settings['timezone'] ?? '') === 'UTC' ? 'selected' : '' ?>>UTC (UTC+0)</option>
              <option value="America/New_York" <?= ($settings['timezone'] ?? '') === 'America/New_York' ? 'selected' : '' ?>>America/New_York (UTC-5)</option>
              <option value="Europe/London" <?= ($settings['timezone'] ?? '') === 'Europe/London' ? 'selected' : '' ?>>Europe/London (UTC+0)</option>
            </select>
            <small class="config-help-text">Set the default timezone for the system</small>
          </div>
          
          <div class="config-field">
            <label for="dateFormat" class="modern-config-label">
              <i class="bi bi-calendar3 me-2"></i>Date Format
            </label>
            <select class="modern-config-select" id="dateFormat" name="date_format">
              <option value="Y-m-d" <?= ($settings['date_format'] ?? 'Y-m-d') === 'Y-m-d' ? 'selected' : '' ?>>YYYY-MM-DD</option>
              <option value="m/d/Y" <?= ($settings['date_format'] ?? '') === 'm/d/Y' ? 'selected' : '' ?>>MM/DD/YYYY</option>
              <option value="d/m/Y" <?= ($settings['date_format'] ?? '') === 'd/m/Y' ? 'selected' : '' ?>>DD/MM/YYYY</option>
              <option value="F j, Y" <?= ($settings['date_format'] ?? '') === 'F j, Y' ? 'selected' : '' ?>>Month DD, YYYY</option>
            </select>
            <small class="config-help-text">Choose how dates are displayed throughout the system</small>
          </div>
        </div>
      </div>

      <!-- Backup Settings -->
      <div class="config-group-section">
        <h6 class="config-section-title">
          <i class="bi bi-shield-check me-2"></i>Backup & Maintenance
        </h6>
        <div class="config-grid">
          <div class="config-field">
            <label for="backupRetention" class="modern-config-label">
              <i class="bi bi-archive me-2"></i>Backup Retention Period
            </label>
            <div class="input-with-unit">
              <input type="number" class="modern-config-control" id="backupRetention" name="backup_retention" 
                     value="<?= htmlspecialchars($settings['backup_retention'] ?? '30') ?>" min="1" max="365">
              <span class="input-unit">days</span>
            </div>
            <small class="config-help-text">Number of days to keep backup files (1-365 days)</small>
          </div>
          
          <div class="config-field">
            <div class="config-toggle-section">
              <div class="toggle-info">
                <label for="autoBackup" class="modern-config-label">
                  <i class="bi bi-arrow-repeat me-2"></i>Automatic Backups
                </label>
                <small class="config-help-text">Enable daily automatic database backups</small>
              </div>
              <div class="modern-toggle-wrapper">
                <input class="modern-toggle-input" type="checkbox" id="autoBackup" name="auto_backup" 
                       <?= ($settings['auto_backup'] ?? '0') === '1' ? 'checked' : '' ?>>
                <label class="modern-toggle-label" for="autoBackup">
                  <span class="modern-toggle-slider"></span>
                </label>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- System Status -->
      <div class="config-group-section">
        <h6 class="config-section-title">
          <i class="bi bi-info-circle me-2"></i>System Information
        </h6>
        <div class="system-status-grid">
          <div class="status-item">
            <div class="status-icon">
              <i class="bi bi-server"></i>
            </div>
            <div class="status-info">
              <span class="status-label">Database Status</span>
              <span class="status-value" id="dbStatusValue">
                <i class="bi bi-hourglass-split me-1"></i>Checking...
              </span>
            </div>
          </div>
          <div class="status-item">
            <div class="status-icon">
              <i class="bi bi-hdd"></i>
            </div>
            <div class="status-info">
              <span class="status-label">Storage Usage</span>
              <span class="status-value">~<?= round(disk_free_space('.') / 1024 / 1024 / 1024, 1) ?>GB Free</span>
            </div>
          </div>
          <div class="status-item">
            <div class="status-icon">
              <i class="bi bi-clock-history"></i>
            </div>
            <div class="status-info">
              <span class="status-label">Last Backup</span>
              <span class="status-value">Today, 02:00 AM</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="config-actions">
        <button type="button" class="modern-btn-secondary" onclick="document.getElementById('systemConfigForm').reset();">
          <i class="bi bi-arrow-clockwise me-2"></i>Reset to Defaults
        </button>
        <button type="submit" class="modern-btn-primary">
          <i class="bi bi-check-circle me-2"></i>Save Configuration
        </button>
      </div>
    </form>
  </div>
</div>

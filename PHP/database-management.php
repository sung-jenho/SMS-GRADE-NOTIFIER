<!-- Database Management Tab -->
<div class="settings-card">
  <!-- Modern Header with Gradient -->
  <div class="modern-settings-header" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; padding: 2rem; border-radius: 12px; margin-bottom: 2rem;">
    <div class="d-flex align-items-center">
      <div class="header-icon-wrapper" style="background: rgba(255,255,255,0.2); padding: 1rem; border-radius: 12px; margin-right: 1.5rem;">
        <i class="bi bi-database" style="font-size: 2rem;"></i>
      </div>
      <div>
        <h3 class="mb-1" style="font-weight: 600;">Database Management</h3>
        <p class="mb-0" style="opacity: 0.9;">Backup, restore, and manage your system database</p>
      </div>
    </div>
  </div>

  <!-- Action Cards -->
  <div class="row g-4 mb-4">
    <div class="col-md-6">
      <div class="modern-action-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 16px; padding: 2rem; text-align: center; box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3); transition: all 0.3s ease;">
        <div class="action-icon" style="background: rgba(255,255,255,0.2); width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
          <i class="bi bi-cloud-download" style="font-size: 2.5rem;"></i>
        </div>
        <h5 class="mb-2" style="font-weight: 600;">Create Backup</h5>
        <p class="mb-3" style="opacity: 0.9; font-size: 0.95rem;">Generate a complete database backup file for safekeeping</p>
        <button type="button" class="modern-action-btn" id="createBackupBtn" 
                style="background: rgba(255,255,255,0.2); color: white; border: 2px solid rgba(255,255,255,0.3); padding: 0.75rem 1.5rem; border-radius: 25px; font-weight: 600; transition: all 0.3s ease; cursor: pointer;">
          <i class="bi bi-cloud-download me-2"></i>Create Backup
        </button>
      </div>
    </div>
    
    <div class="col-md-6">
      <div class="modern-action-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; border-radius: 16px; padding: 2rem; text-align: center; box-shadow: 0 8px 25px rgba(245, 87, 108, 0.3); transition: all 0.3s ease;">
        <div class="action-icon" style="background: rgba(255,255,255,0.2); width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
          <i class="bi bi-cloud-upload" style="font-size: 2.5rem;"></i>
        </div>
        <h5 class="mb-2" style="font-weight: 600;">Restore Database</h5>
        <p class="mb-3" style="opacity: 0.9; font-size: 0.95rem;">Restore your database from a previously created backup file</p>
        <input type="file" id="restoreFile" accept=".sql" style="display: none;">
        <button type="button" class="modern-action-btn" id="restoreBackupBtn"
                style="background: rgba(255,255,255,0.2); color: white; border: 2px solid rgba(255,255,255,0.3); padding: 0.75rem 1.5rem; border-radius: 25px; font-weight: 600; transition: all 0.3s ease; cursor: pointer;">
          <i class="bi bi-cloud-upload me-2"></i>Restore Backup
        </button>
      </div>
    </div>
  </div>
  
  <!-- Recent Backups Section -->
  <div class="backups-section" style="background: white; border-radius: 16px; padding: 2rem; box-shadow: 0 4px 20px rgba(0,0,0,0.08); border: 1px solid #e0e7ff;">
    <div class="section-header" style="margin-bottom: 1.5rem;">
      <h5 style="color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">
        <i class="bi bi-clock-history me-2" style="color: #11998e;"></i>
        Recent Backups
      </h5>
      <p style="color: #64748b; margin: 0; font-size: 0.95rem;">View and manage your database backup files</p>
    </div>
    
    <div class="table-responsive">
      <table class="modern-table" style="width: 100%; border-collapse: separate; border-spacing: 0;">
        <thead>
          <tr style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);">
            <th style="padding: 1rem; border: none; color: #374151; font-weight: 600; border-radius: 8px 0 0 8px;">
              <i class="bi bi-file-earmark-text me-2"></i>Filename
            </th>
            <th style="padding: 1rem; border: none; color: #374151; font-weight: 600;">
              <i class="bi bi-hdd me-2"></i>Size
            </th>
            <th style="padding: 1rem; border: none; color: #374151; font-weight: 600;">
              <i class="bi bi-calendar me-2"></i>Created
            </th>
            <th style="padding: 1rem; border: none; color: #374151; font-weight: 600; border-radius: 0 8px 8px 0; text-align: center;">
              <i class="bi bi-gear me-2"></i>Actions
            </th>
          </tr>
        </thead>
        <tbody id="backupsList" style="background: white;">
          <!-- Backup files will be loaded here -->
        </tbody>
      </table>
    </div>
    
    <div class="empty-state" id="emptyBackupsState" style="text-align: center; padding: 3rem 1rem; color: #64748b; display: none;">
      <i class="bi bi-inbox" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 1rem;"></i>
      <h6 style="color: #475569; margin-bottom: 0.5rem;">No backups found</h6>
      <p style="margin: 0; font-size: 0.9rem;">Create your first backup to get started</p>
    </div>
  </div>
</div>

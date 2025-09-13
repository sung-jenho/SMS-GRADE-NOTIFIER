<!-- Admin Profile Tab -->
<div class="modern-profile-container">
  <!-- Profile Header Section -->
  <div class="profile-header-section">
    <div class="profile-avatar-section">
      <div class="modern-profile-picture-container">
        <img src="<?= !empty($user_data['profile_picture']) ? '../uploads/profiles/' . htmlspecialchars($user_data['profile_picture']) : '../assets/default-avatar.svg' ?>" 
             alt="Profile Picture" class="modern-profile-picture" id="profilePreview">
        <div class="modern-profile-overlay">
          <i class="bi bi-camera-fill"></i>
          <span>Change Photo</span>
        </div>
        <input type="file" id="profilePicture" name="profile_picture" accept="image/*" style="display: none;" form="adminProfileForm">
      </div>
      <div class="profile-info-preview">
        <h4 class="profile-name-display"><?= htmlspecialchars($user_data['full_name'] ?? 'Administrator') ?></h4>
        <p class="profile-email-display"><?= htmlspecialchars($user_data['email'] ?? 'admin@ctucc.edu.ph') ?></p>
        <span class="profile-status-badge">Administrator</span>
      </div>
    </div>
  </div>

  <!-- Profile Form Section -->
  <div class="profile-form-section">
    <div class="section-header">
      <h5 class="section-title">
        <i class="bi bi-person-gear me-2"></i>Profile Information
      </h5>
      <p class="section-subtitle">Update your personal information and account settings</p>
    </div>

    <form id="adminProfileForm" class="modern-form" method="POST" action="update_admin_profile.php" enctype="multipart/form-data">
      <!-- Basic Information -->
      <div class="form-group-section">
        <h6 class="form-section-title">Basic Information</h6>
        <div class="form-grid">
          <div class="form-field">
            <label for="fullName" class="modern-form-label">
              <i class="bi bi-person me-2"></i>Full Name
            </label>
            <input type="text" class="modern-form-control" id="fullName" name="full_name" 
                   value="<?= htmlspecialchars($user_data['full_name'] ?? '') ?>" required>
          </div>
          <div class="form-field">
            <label for="username" class="modern-form-label">
              <i class="bi bi-at me-2"></i>Username
            </label>
            <input type="text" class="modern-form-control" id="username" name="username" 
                   value="<?= htmlspecialchars($user_data['username'] ?? '') ?>" required>
            <small class="config-help-text">Username is used for login authentication</small>
          </div>
          <div class="form-field full-width">
            <label for="email" class="modern-form-label">
              <i class="bi bi-envelope me-2"></i>Email Address
            </label>
            <input type="email" class="modern-form-control" id="email" name="email" 
                   value="<?= htmlspecialchars($user_data['email'] ?? '') ?>">
          </div>
        </div>
      </div>

      <!-- Security Settings -->
      <div class="form-group-section">
        <h6 class="form-section-title">Security Settings</h6>
        <div class="security-notice">
          <i class="bi bi-shield-check me-2"></i>
          <span>Current password is only required for password or username changes</span>
        </div>
        <div class="form-grid">
          <div class="form-field full-width">
            <label for="currentPassword" class="modern-form-label">
              <i class="bi bi-key me-2"></i>Current Password
            </label>
            <input type="password" class="modern-form-control" id="currentPassword" name="current_password" 
                   placeholder="Only required for password/username changes">
          </div>
          <div class="form-field">
            <label for="newPassword" class="modern-form-label">
              <i class="bi bi-lock me-2"></i>New Password
            </label>
            <input type="password" class="modern-form-control" id="newPassword" name="new_password" 
                   placeholder="Leave blank to keep current password">
          </div>
          <div class="form-field">
            <label for="confirmPassword" class="modern-form-label">
              <i class="bi bi-lock-fill me-2"></i>Confirm New Password
            </label>
            <input type="password" class="modern-form-control" id="confirmPassword" name="confirm_password" 
                   placeholder="Confirm new password">
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="form-actions">
        <button type="button" class="modern-btn-secondary" onclick="document.getElementById('adminProfileForm').reset();">
          <i class="bi bi-arrow-clockwise me-2"></i>Reset Changes
        </button>
        <button type="submit" class="modern-btn-primary">
          <i class="bi bi-check-circle me-2"></i>Update Profile
        </button>
      </div>
    </form>
  </div>
</div>

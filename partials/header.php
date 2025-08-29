<?php /** Header with hamburger and title group */ ?>
<header class="main-header">
  <div class="header-left">
    <button class="header-action-btn menu-btn" type="button" aria-label="Open menu" data-bs-toggle="offcanvas" data-bs-target="#mainSidebar" aria-controls="mainSidebar">
      <span class="bi bi-list"></span>
    </button>
    <div class="header-logo"><img src="assets/ctu-logo.png" alt="CTU Logo" class="header-logo-img"></div>
    <div class="header-title-group">
      <div class="header-title-row">
        <div class="header-title">SMS Grade Notifier</div>
        <span class="status-badge">System Active</span>
      </div>
      <div class="header-subtitle">For college students in CTU-Consolacion.</div>
    </div>
  </div>
  <div class="header-actions">
    <input type="checkbox" id="darkModeToggle" class="theme-switch-input" aria-label="Toggle dark mode" />
    <label for="darkModeToggle" class="theme-switch" aria-hidden="true">
      <i class="bi bi-sun-fill" aria-hidden="true"></i>
      <span class="switch-handle"></span>
      <i class="bi bi-moon-stars-fill" aria-hidden="true"></i>
    </label>
    
    <div class="user-menu">
      <div class="user-info">
        <span class="user-avatar"><?= substr($_SESSION['full_name'] ?? 'A', 0, 1) ?></span>
        <span class="user-name"><?= htmlspecialchars($_SESSION['full_name'] ?? 'Admin') ?></span>
      </div>
      <a href="logout.php" class="logout-btn" title="Logout">
        <i class="bi bi-box-arrow-right"></i>
      </a>
    </div>
  </div>
</header>


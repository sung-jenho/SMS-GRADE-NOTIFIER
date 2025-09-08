<?php /** Header with hamburger and title group */ ?>
<header class="main-header">
  <div class="header-left">
    <button class="header-action-btn menu-btn" type="button" aria-label="Open menu" data-bs-toggle="offcanvas" data-bs-target="#mainSidebar" aria-controls="mainSidebar">
      <span class="bi bi-list"></span>
    </button>
    <div class="header-logo"><img src="../assets/ctu-logo.png" alt="CTU Logo" class="header-logo-img"></div>
    <div class="header-title-group">
      <div class="header-title-row">
        <div class="header-title">CROZONO</div>
      </div>
      
    </div>
  </div>
  <div class="header-actions">
    <button type="button" id="darkModeToggle" class="sleek-theme-toggle" aria-label="Toggle dark mode">
      <i class="bi bi-sun-fill theme-icon"></i>
    </button>

    <?php
      require_once __DIR__ . '/helpers.php';
      $avatarPath = get_avatar_path();
    ?>
    <div class="user-menu dropdown">
      <button class="avatar-dropdown user-avatar-btn" type="button" id="userMenuDropdown" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Open user menu">
        <img src="<?= $avatarPath ?>" alt="Admin Avatar" class="user-avatar-img" />
      </button>
      <ul class="dropdown-menu dropdown-menu-end user-menu-dropdown" aria-labelledby="userMenuDropdown">
        <li><h6 class="dropdown-header">Account</h6></li>
        <li>
          <a class="dropdown-item logout-link" id="logoutLink" href="logout.php" role="button">
            <i class="bi bi-box-arrow-right me-2"></i>
            <span>Logout</span>
          </a>
        </li>
      </ul>
    </div>
  </div>
</header>


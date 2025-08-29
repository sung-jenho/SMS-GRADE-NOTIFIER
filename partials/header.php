<?php /** Header with hamburger and title group */ ?>
<header class="main-header">
  <div class="header-left">
    <button class="header-action-btn menu-btn" type="button" aria-label="Open menu" data-bs-toggle="offcanvas" data-bs-target="#mainSidebar" aria-controls="mainSidebar">
      <span class="bi bi-list"></span>
    </button>
    <div class="header-logo"><img src="assets/ctu-logo.png" alt="CTU Logo" class="header-logo-img"></div>
    <div class="header-title-group">
      <div class="header-title-row">
        <div class="header-title">HIBALAN</div>
      </div>
      
    </div>
  </div>
  <div class="header-actions">
    <input type="checkbox" id="darkModeToggle" class="theme-switch-input" aria-label="Toggle dark mode" />
    <label for="darkModeToggle" class="theme-switch" aria-hidden="true">
      <i class="bi bi-sun-fill" aria-hidden="true"></i>
      <span class="switch-handle"></span>
      <i class="bi bi-moon-stars-fill" aria-hidden="true"></i>
    </label>

    <?php
      $png = __DIR__ . '/../assets/sir-greg.jpg';
      $jpg = __DIR__ . '/../assets/sir-greg.jpg';
      if (file_exists($png)) {
        $avatarPath = 'assets/sir-greg.jpg';
      } elseif (file_exists($jpg)) {
        $avatarPath = 'assets/sir-greg.jpg';
      } else {
        $avatarPath = 'assets/ctu-logo.png';
      }
    ?>
    <div class="user-menu dropdown">
      <button class="avatar-dropdown user-avatar-btn" type="button" id="userMenuDropdown" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Open user menu">
        <img src="<?= $avatarPath ?>" alt="Admin Avatar" class="user-avatar-img" />
      </button>
      <ul class="dropdown-menu dropdown-menu-end user-menu-dropdown" aria-labelledby="userMenuDropdown">
        <li><h6 class="dropdown-header">Account</h6></li>
        <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
      </ul>
    </div>
  </div>
</header>


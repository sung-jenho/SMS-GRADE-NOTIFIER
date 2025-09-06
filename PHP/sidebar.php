<?php /** Off-canvas sidebar navigation */ ?>
<div class="offcanvas offcanvas-start custom-offcanvas" tabindex="-1" id="mainSidebar" aria-labelledby="mainSidebarLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="mainSidebarLabel">SMS Grade System</h5>
  </div>
  <div class="offcanvas-body">
    <nav class="sidebar-nav" aria-label="Primary">
      <a class="sidebar-link<?php if($section=='overview') echo ' active'; ?>" href="?section=overview">
        <i class="bi bi-grid-1x2"></i>
        <span>Dashboard</span>
      </a>
      <a class="sidebar-link<?php if($section=='students') echo ' active'; ?>" href="?section=students">
        <i class="bi bi-people"></i>
        <span>Students</span>
      </a>
      <a class="sidebar-link<?php if($section=='grades') echo ' active'; ?>" href="?section=grades">
        <i class="bi bi-mortarboard"></i>
        <span>Grades</span>
      </a>
      <a class="sidebar-link<?php if($section=='notifications') echo ' active'; ?>" href="?section=notifications">
        <i class="bi bi-chat-dots"></i>
        <span>SMS Logs</span>
      </a>
      <a class="sidebar-link<?php if($section=='settings') echo ' active'; ?>" href="?section=settings">
        <i class="bi bi-gear"></i>
        <span>Settings</span>
      </a>
    </nav>
    <div class="sidebar-footer">
      <div class="sidebar-user">
        <?php
          require_once __DIR__ . '/helpers.php';
          $sidebarAvatar = get_avatar_path();
        ?>
        <img src="<?= $sidebarAvatar ?>" alt="Profile" class="sidebar-avatar-img" />
        <div class="user-meta">
          <div class="user-name"><?= htmlspecialchars($_SESSION['full_name'] ?? 'Administrator') ?></div>
          <div class="user-email">Prof. Greg Vestil</div>
        </div>
      </div>
    </div>
  </div>
</div>


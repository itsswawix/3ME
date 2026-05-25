<?php
// Ensure session is started if not already and headers are not sent
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}

$sb_userId = $_SESSION['user_id'] ?? 'USER-ADMIN-001';
$sb_userEmail = $_SESSION['user_email'] ?? 'admin@3me.com';
$sb_userName = $_SESSION['user_name'] ?? 'System Administrator';
$sb_userRole = $_SESSION['user_role'] ?? 'Admin';

// Calculate initials
$sb_initials = '';
$sb_nameParts = explode(' ', $sb_userName);
foreach ($sb_nameParts as $part) {
    if (!empty($part)) {
        $sb_initials .= strtoupper($part[0]);
    }
}
if (strlen($sb_initials) > 2) {
    $sb_initials = substr($sb_initials, 0, 2);
}
if (empty($sb_initials)) {
    $sb_initials = 'US';
}

// Check session color or generate one dynamically
$sb_colors = ['#4f46e5', '#7c3aed', '#db2777', '#dc2626', '#ea580c', '#16a34a', '#0891b2'];
$sb_color = $_SESSION['user_color'] ?? '';
if (empty($sb_color)) {
    $sb_color = $sb_colors[array_sum(str_split(ord($sb_userId[0] ?? 'U'))) % count($sb_colors)];
}
?>
<!-- sidebar.php — clean include version -->
<style>
/* ═══════════════════════════════════════════════
   NOVACORE SIDEBAR — matches dashboard light theme
═══════════════════════════════════════════════ */
#nc-sidebar {
  width: 240px;
  min-width: 240px;
  height: 100vh;
  position: sticky;
  top: 0;
  display: flex;
  flex-direction: column;
  background: rgba(255,255,255,0.75);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border-right: 1px solid rgba(255,255,255,0.9);
  box-shadow: 4px 0 24px rgba(99,102,241,0.06);
  transition: width 0.35s cubic-bezier(0.4,0,0.2,1),
              min-width 0.35s cubic-bezier(0.4,0,0.2,1);
  overflow: visible;
  z-index: 200;
  flex-shrink: 0;
}
#nc-sidebar.nc-collapsed {
  width: 66px;
  min-width: 66px;
}
.nc-sidebar-inner {
  display: flex;
  flex-direction: column;
  height: 100%;
  overflow: hidden;
  border-radius: inherit;
}
.nc-brand {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 18px 14px 14px;
  border-bottom: 1px solid rgba(99,102,241,0.08);
  flex-shrink: 0;
  min-height: 62px;
  position: relative;
}
.nc-logo {
  width: 34px;
  height: 34px;
  border-radius: 10px;
  background: linear-gradient(135deg,#4f46e5,#7c3aed);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  box-shadow: 0 4px 12px rgba(79,70,229,0.25);
}
.nc-logo img {
  height: 16px;
  width: auto;
  filter: brightness(0) invert(1);
}
.nc-logo-fallback {
  font-family: 'Outfit', sans-serif;
  font-weight: 600;
  font-size: 13px;
  color: #fff;
  letter-spacing: -0.3px;
}
.nc-brand-name {
  font-family: 'Outfit', sans-serif;
  font-weight: 600;
  font-size: 16px;
  color: #1e293b;
  white-space: nowrap;
  letter-spacing: -0.2px;
  transition: opacity 0.25s, transform 0.25s;
}
#nc-sidebar.nc-collapsed .nc-brand-name {
  opacity: 0;
  transform: translateX(-8px);
  pointer-events: none;
}
#nc-toggle {
  position: absolute;
  top: 18px;
  right: -12px;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background: white;
  border: 1px solid #e2e8f0;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  z-index: 300;
  color: #94a3b8;
  font-size: 9px;
  transition: background 0.2s, border-color 0.2s, color 0.2s;
}
#nc-toggle:hover {
  background: #4f46e5;
  border-color: #4f46e5;
  color: white;
}
#nc-toggle i {
  transition: transform 0.35s cubic-bezier(0.4,0,0.2,1);
}
#nc-sidebar.nc-collapsed #nc-toggle i {
  transform: rotate(180deg);
}
.nc-nav {
  flex: 1;
  overflow-y: auto;
  overflow-x: hidden;
  padding: 12px 8px;
  scrollbar-width: none;
}
.nc-nav::-webkit-scrollbar { display: none; }
.nc-section-label {
  font-size: 9.5px;
  font-weight: 600;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: #94a3b8;
  padding: 0 8px;
  margin: 6px 0 4px;
  white-space: nowrap;
  transition: opacity 0.25s;
}
#nc-sidebar.nc-collapsed .nc-section-label { opacity: 0; }
.nc-divider {
  height: 1px;
  background: rgba(99,102,241,0.08);
  margin: 8px 8px;
}
.nc-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 8px 8px;
  border-radius: 10px;
  color: #475569;
  text-decoration: none;
  font-size: 13px;
  font-weight: 500;
  position: relative;
  transition: background 0.18s, color 0.18s;
  white-space: nowrap;
  margin-bottom: 1px;
  cursor: pointer;
}
.nc-item:hover {
  background: rgba(99,102,241,0.07);
  color: #3730a3;
}
.nc-item.nc-active {
  background: linear-gradient(135deg, rgba(79,70,229,0.12), rgba(124,58,237,0.08));
  color: #4f46e5;
}
.nc-icon {
  width: 30px;
  height: 30px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 13px;
  flex-shrink: 0;
  transition: background 0.18s;
  color: #64748b;
}
.nc-item:hover .nc-icon { background: rgba(99,102,241,0.08); color: #4f46e5; }
.nc-item.nc-active .nc-icon { background: rgba(79,70,229,0.14); color: #4f46e5; }
.nc-item.nc-active::after {
  content: '';
  position: absolute;
  right: 0;
  top: 50%;
  transform: translateY(-50%);
  width: 3px;
  height: 16px;
  background: linear-gradient(180deg,#4f46e5,#7c3aed);
  border-radius: 3px 0 0 3px;
}
.nc-item-text {
  transition: opacity 0.25s, transform 0.25s;
}
#nc-sidebar.nc-collapsed .nc-item-text {
  opacity: 0;
  transform: translateX(-6px);
  pointer-events: none;
}
.nc-tooltip {
  position: absolute;
  left: calc(100% + 12px);
  top: 50%;
  transform: translateY(-50%);
  background: #1e293b;
  color: #f1f5f9;
  font-size: 11.5px;
  padding: 5px 11px;
  border-radius: 8px;
  white-space: nowrap;
  opacity: 0;
  pointer-events: none;
  z-index: 9999;
  transition: opacity 0.15s;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.nc-tooltip::before {
  content: '';
  position: absolute;
  right: 100%;
  top: 50%;
  transform: translateY(-50%);
  border: 5px solid transparent;
  border-right-color: #1e293b;
}
#nc-sidebar.nc-collapsed .nc-item:hover .nc-tooltip { opacity: 1; }
.nc-profile-section {
  padding: 10px 8px 12px;
  border-top: 1px solid rgba(99,102,241,0.08);
  flex-shrink: 0;
}
.nc-profile-trigger {
  display: flex;
  align-items: center;
  gap: 9px;
  padding: 8px;
  border-radius: 10px;
  cursor: pointer;
  transition: background 0.18s;
}
.nc-profile-trigger:hover { background: rgba(99,102,241,0.07); }
.nc-avatar {
  width: 32px;
  height: 32px;
  border-radius: 9px;
  background: linear-gradient(135deg,#4f46e5,#7c3aed);
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: 'Outfit', sans-serif;
  font-weight: 600;
  font-size: 11px;
  color: white;
  flex-shrink: 0;
  box-shadow: 0 2px 8px rgba(79,70,229,0.25);
}
.nc-profile-text {
  flex: 1;
  overflow: hidden;
  transition: opacity 0.25s, transform 0.25s;
}
#nc-sidebar.nc-collapsed .nc-profile-text {
  opacity: 0;
  transform: translateX(-6px);
  pointer-events: none;
}
.nc-profile-name {
  font-size: 12.5px;
  font-weight: 600;
  color: #1e293b;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.nc-profile-role {
  font-size: 10.5px;
  color: #64748b;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  margin-top: 1px;
}
.nc-profile-chevron {
  color: #94a3b8;
  font-size: 9px;
  transition: transform 0.2s, opacity 0.25s;
  flex-shrink: 0;
}
#nc-sidebar.nc-collapsed .nc-profile-chevron { opacity: 0; }
#nc-popout {
  position: fixed;
  z-index: 2000;
  width: 220px;
  background: rgba(255,255,255,0.98);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255,255,255,0.9);
  border-radius: 16px;
  padding: 6px;
  opacity: 0;
  visibility: hidden;
  transform: translateY(10px);
  transition: opacity 0.2s, visibility 0.2s, transform 0.25s cubic-bezier(0.34,1.56,0.64,1);
  box-shadow: 0 20px 40px rgba(79,70,229,0.12), 0 4px 16px rgba(0,0,0,0.06);
  bottom: 72px;
  left: 256px;
}
#nc-popout.nc-open {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
}
.nc-popout-user {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 10px 12px;
  border-bottom: 1px solid #f1f5f9;
  margin-bottom: 4px;
}
.nc-popout-avatar {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  background: linear-gradient(135deg,#4f46e5,#7c3aed);
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: 'Outfit', sans-serif;
  font-weight: 600;
  font-size: 12px;
  color: white;
  flex-shrink: 0;
}
.nc-popout-name {
  font-size: 13px;
  font-weight: 600;
  color: #0f172a;
}
.nc-popout-email {
  font-size: 10.5px;
  color: #94a3b8;
  margin-top: 1px;
}
.nc-popout-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 8px 10px;
  border-radius: 8px;
  color: #475569;
  font-size: 12.5px;
  font-weight: 500;
  text-decoration: none;
  cursor: pointer;
  transition: background 0.15s, color 0.15s;
}
.nc-popout-item:hover { background: #f8fafc; color: #1e293b; }
.nc-popout-item i { width: 15px; text-align: center; font-size: 11px; color: #6366f1; }
.nc-popout-item.nc-danger { color: #ef4444; }
.nc-popout-item.nc-danger:hover { background: #fef2f2; color: #dc2626; }
.nc-popout-item.nc-danger i { color: #ef4444; }
.nc-popout-divider {
  height: 1px;
  background: #f1f5f9;
  margin: 4px 0;
}
#nc-overlay {
  display: none;
  position: fixed;
  inset: 0;
  z-index: 1500;
}
</style>

<!-- SIDEBAR MARKUP -->
<aside id="nc-sidebar">
  <div class="nc-sidebar-inner">
    <div class="nc-brand">
      <div class="nc-logo">
        <img src="assets/images/logo.png" alt="Logo"
             onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
        <span class="nc-logo-fallback" style="display:none">3ME</span>
      </div>
      <span class="nc-brand-name">3ME Manp. Serv.</span>
    </div>

    <nav class="nc-nav">
      <p class="nc-section-label">Main</p>

      <a href="dashboard.php" class="nc-item nc-active">
        <span class="nc-icon"><i class="fas fa-th-large"></i></span>
        <span class="nc-item-text">Dashboard</span>
        <span class="nc-tooltip">Dashboard</span>
      </a>
      <div class="nc-divider"></div>
      <p class="nc-section-label">Employee & Applicant Tracking</p>
      <a href="recruitment.php" class="nc-item">
        <span class="nc-icon"><i class="fas fa-user-plus"></i></span>
        <span class="nc-item-text">Recruitment</span>
        <span class="nc-tooltip">Recruitment Flow</span>
      </a>
      <a href="onboarding.php" class="nc-item">
        <span class="nc-icon"><i class="fas fa-clipboard-list"></i></span>
        <span class="nc-item-text">Onboarding</span>
        <span class="nc-tooltip">Onboarding</span>
      </a>
      <a href="employee.php" class="nc-item">
        <span class="nc-icon"><i class="fas fa-users"></i></span>
        <span class="nc-item-text">Employee</span>
        <span class="nc-tooltip">Employee</span>
      </a>
      <div class="nc-divider"></div>
      <p class="nc-section-label">Jobs</p>

      <a href="requisition.php" class="nc-item">
        <span class="nc-icon"><i class="fas fa-file-alt"></i></span>
        <span class="nc-item-text">Requisition</span>
        <span class="nc-tooltip">Requisition</span>
      </a>

      <div class="nc-divider"></div>
      <p class="nc-section-label">Workforce</p>

      <a href="attendance.php" class="nc-item">
        <span class="nc-icon"><i class="fas fa-clock"></i></span>
        <span class="nc-item-text">Attendance Evaluation</span>
        <span class="nc-tooltip">AttendanceEvaluation</span>
      </a>
      <a href="leave.php" class="nc-item">
        <span class="nc-icon"><i class="fas fa-calendar-check"></i></span>
        <span class="nc-item-text">Leave &amp; Monitoring</span>
        <span class="nc-tooltip">Leave &amp; Monitoring</span>
      </a>

      <div class="nc-divider"></div>
      <p class="nc-section-label">Performance</p>

      <a href="performance.php" class="nc-item">
        <span class="nc-icon"><i class="fas fa-chart-bar"></i></span>
        <span class="nc-item-text">Offenses</span>
        <span class="nc-tooltip">Offenses</span>
      </a>

      <div class="nc-divider"></div>
      <p class="nc-section-label">System</p>

      <a href="masterdata.php" class="nc-item">
        <span class="nc-icon"><i class="fas fa-database"></i></span>
        <span class="nc-item-text">Master Data</span>
        <span class="nc-tooltip">Master Data</span>
      </a>
    </nav>

    <div class="nc-profile-section">
      <div class="nc-profile-trigger" id="nc-profile-trigger">
        <div class="nc-avatar" style="background: <?php echo $sb_color; ?>;"><?php echo htmlspecialchars($sb_initials); ?></div>
        <div class="nc-profile-text">
          <div class="nc-profile-name"><?php echo htmlspecialchars($sb_userName); ?></div>
          <div class="nc-profile-role"><?php echo htmlspecialchars($sb_userRole); ?></div>
        </div>
        <i class="fas fa-chevron-up nc-profile-chevron" id="nc-profile-chevron"></i>
      </div>
    </div>
  </div>
  <button id="nc-toggle" title="Toggle sidebar">
    <i class="fas fa-chevron-left" id="nc-toggle-icon"></i>
  </button>
</aside>

<div id="nc-popout">
  <div class="nc-popout-user">
    <div class="nc-popout-avatar" style="background: <?php echo $sb_color; ?>;"><?php echo htmlspecialchars($sb_initials); ?></div>
    <div>
      <div class="nc-popout-name"><?php echo htmlspecialchars($sb_userName); ?></div>
      <div class="nc-popout-email"><?php echo htmlspecialchars($sb_userEmail); ?></div>
    </div>
  </div>
  <a href="profile.php" class="nc-popout-item"><i class="fas fa-id-card"></i> My Profile</a>
  <?php if ($sb_userRole === 'Admin'): ?>
  <a href="users.php" class="nc-popout-item"><i class="fas fa-users-cog"></i> User Management</a>
  <?php endif; ?>
  <div class="nc-popout-divider"></div>
  <a href="logout.php" class="nc-popout-item nc-danger"><i class="fas fa-door-open"></i> Sign out</a>
</div>
<div id="nc-overlay"></div>

<script>
(function () {
  'use strict';
  var sidebar   = document.getElementById('nc-sidebar');
  var toggleBtn = document.getElementById('nc-toggle');
  var chevron   = document.getElementById('nc-profile-chevron');
  var trigger   = document.getElementById('nc-profile-trigger');
  var popout    = document.getElementById('nc-popout');
  var overlay   = document.getElementById('nc-overlay');
  var navItems  = document.querySelectorAll('.nc-item');
  var KEY       = 'novacore_sb_v3';

  function setCollapsed(v) {
    v ? sidebar.classList.add('nc-collapsed') : sidebar.classList.remove('nc-collapsed');
    localStorage.setItem(KEY, v ? '1' : '0');
    reposition();
  }

  function reposition() {
    var r = sidebar.getBoundingClientRect();
    popout.style.left = (r.right + 10) + 'px';
  }

  function openPopout() {
    reposition();
    popout.classList.add('nc-open');
    overlay.style.display = 'block';
    chevron.style.transform = 'rotate(180deg)';
  }

  function closePopout() {
    popout.classList.remove('nc-open');
    overlay.style.display = 'none';
    chevron.style.transform = '';
  }

  function initActive() {
    var path = window.location.pathname.split('/').pop() || 'dashboard.php';
    var matched = false;
    navItems.forEach(function (item) {
      var href = item.getAttribute('href') || '';
      if (href && path && href.indexOf(path) !== -1) {
        item.classList.add('nc-active');
        matched = true;
      } else {
        item.classList.remove('nc-active');
      }
    });
    if (!matched) {
      var first = document.querySelector('.nc-item');
      if (first) first.classList.add('nc-active');
    }
  }

  toggleBtn.addEventListener('click', function (e) {
    e.stopPropagation();
    setCollapsed(!sidebar.classList.contains('nc-collapsed'));
    closePopout();
  });

  trigger.addEventListener('click', function (e) {
    e.stopPropagation();
    popout.classList.contains('nc-open') ? closePopout() : openPopout();
  });

  overlay.addEventListener('click', closePopout);
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closePopout(); });
  document.addEventListener('click', function (e) {
    if (popout.classList.contains('nc-open') &&
        !trigger.contains(e.target) && !popout.contains(e.target)) closePopout();
  });

  navItems.forEach(function (item) {
    item.addEventListener('click', function (e) {
      navItems.forEach(function (i) { i.classList.remove('nc-active'); });
      this.classList.add('nc-active');
      if (this.getAttribute('href') === '#') e.preventDefault();
    });
  });

  window.addEventListener('resize', function () {
    if (popout.classList.contains('nc-open')) closePopout();
  });

  var saved = localStorage.getItem(KEY);
  setCollapsed(saved === '1');
  initActive();
  reposition();
})();
</script>
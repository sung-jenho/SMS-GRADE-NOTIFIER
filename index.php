<?php
// index.php - Main page for SMS Grade Notification System
// Simple PHP web interface for managing students, subjects, and grades

$mysqli = new mysqli("localhost", "root", "", "sms_grades");
if ($mysqli->connect_errno) {
    die("Failed to connect to MySQL: " . $mysqli->connect_error);
}

function fetch_students($mysqli) {
    $result = $mysqli->query("SELECT * FROM students");
    return $result->fetch_all(MYSQLI_ASSOC);
}

function fetch_subjects($mysqli) {
    $result = $mysqli->query("SELECT * FROM subjects");
    return $result->fetch_all(MYSQLI_ASSOC);
}

function fetch_grades($mysqli) {
    $sql = "SELECT grades.id, students.name, students.student_number, subjects.subject_code, subjects.subject_title, grades.grade, grades.last_updated FROM grades JOIN students ON grades.student_id = students.id JOIN subjects ON grades.subject_id = subjects.id ORDER BY grades.last_updated DESC";
    $result = $mysqli->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

$students = fetch_students($mysqli);
$subjects = fetch_subjects($mysqli);
$grades = fetch_grades($mysqli);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CTU-CC ADMIN</title>
    <link rel="icon" type="image/png" href="assets/ctu-logo.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            font-family: 'Inter', Arial, sans-serif;
            background: linear-gradient(120deg, #e0e7ef 0%, #f8fafc 100%);
            min-height: 100vh;
        }
        .navbar {
            background: #2563eb;
            box-shadow: 0 2px 12px rgba(37,99,235,0.07);
        }
        .navbar-brand, .nav-link, .navbar-text {
            color: #fff !important;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .main-card {
            border: none;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(37,99,235,0.08), 0 1.5px 6px rgba(0,0,0,0.03);
            overflow: hidden;
        }
        .card-header.bg-primary {
            background: linear-gradient(90deg, #2563eb 60%, #60a5fa 100%) !important;
            font-size: 1.25rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .table-responsive {
            margin-bottom: 2rem;
        }
        .table {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .table thead {
            background: #f1f5f9;
        }
        .table-hover tbody tr:hover {
            background: #f3f7fa;
            transition: background 0.2s;
        }
        .form-section {
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 4px 32px rgba(37,99,235,0.04);
    padding: 2.5rem 2rem 2rem 2rem;
    margin-top: 2rem;
    border: none;
}
.form-section h2 {
    font-size: 2rem;
    font-weight: 600;
    letter-spacing: -0.5px;
    margin-bottom: 2rem;
    color: #1e293b;
}
.form-label {
    font-weight: 400;
    color: #2563eb;
    font-size: 1rem;
    margin-bottom: 0.25rem;
    letter-spacing: 0.1px;
}
.form-control, .form-select {
    border: none;
    border-radius: 8px;
    background: #f7fafd;
    box-shadow: none;
    font-size: 1rem;
    padding: 0.75rem 1rem;
    transition: box-shadow 0.2s, border 0.2s;
}
.form-control:focus, .form-select:focus {
    box-shadow: 0 0 0 2px #60a5fa33;
    outline: none;
    background: #fff;
}
.btn-primary {
    background: linear-gradient(90deg, #2563eb 60%, #60a5fa 100%);
    border: none;
    font-weight: 500;
    font-size: 1.1rem;
    letter-spacing: 0.1px;
    padding: 0.6rem 2.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(37,99,235,0.08);
    transition: background 0.2s, box-shadow 0.2s;
}
.btn-primary:hover, .btn-primary:focus {
    background: linear-gradient(90deg, #1d4ed8 60%, #38bdf8 100%);
    box-shadow: 0 4px 16px rgba(37,99,235,0.15);
}
@media (max-width: 768px) {
    .form-section {
        padding: 1rem 0.5rem;
    }
}
        h1, h2, h4 {
            color: #1e293b;
        }
        @media (max-width: 768px) {
            .form-section {
                padding: 1rem 0.5rem;
            }
            .main-card {
                border-radius: 10px;
            }
        }
    .dark-mode {
    background: linear-gradient(120deg, #181e29 0%, #232b3b 100%) !important;
    color: #e5e7eb;
}
.dark-mode .navbar {
    background: #111827 !important;
    box-shadow: 0 2px 16px rgba(0,0,0,0.12);
}
.dark-mode .navbar-brand, .dark-mode .nav-link, .dark-mode .navbar-text {
    color: #fff !important;
}
.dark-mode .form-section, .dark-mode .card, .dark-mode .table {
    background: #232b3b !important;
    color: #e5e7eb;
    box-shadow: 0 6px 32px rgba(0,0,0,0.16);
    border: none;
}
.dark-mode .form-label {
    color: #60a5fa;
}
.dark-mode .form-control, .dark-mode .form-select {
    background: #181e29;
    color: #e5e7eb;
    border: none;
}
.dark-mode .form-control:focus, .dark-mode .form-select:focus {
    background: #232b3b;
    color: #fff;
    box-shadow: 0 0 0 2px #2563eb55;
}
.dark-mode .btn-primary {
    background: linear-gradient(90deg, #2563eb 60%, #60a5fa 100%);
    color: #fff;
    box-shadow: 0 2px 12px rgba(37,99,235,0.18);
}
.dark-mode .btn-primary:hover, .dark-mode .btn-primary:focus {
    background: linear-gradient(90deg, #1d4ed8 60%, #38bdf8 100%);
}
.dark-mode h1, .dark-mode h2, .dark-mode h4, .dark-mode th {
    color: #fff;
}
.dark-mode .table-hover tbody tr:hover {
    background: #1a2233;
}
.dark-mode .table-light, .dark-mode thead {
    background: #181e29 !important;
    color: #e5e7eb;
}
.theme-toggle {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 38px;
    width: 38px;
    border-radius: 10px;
    border: 1px solid rgba(255,255,255,0.35);
    background: transparent;
    color: #fff;
    transition: background 0.2s, border-color 0.2s, box-shadow 0.2s;
}
.theme-toggle .bi { font-size: 1.1rem; }
.theme-toggle:hover, .theme-toggle:focus {
    background: rgba(255,255,255,0.12);
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}
.dark-mode .theme-toggle {
    border-color: rgba(255,255,255,0.18);
    background: rgba(255,255,255,0.03);
    color: #e5e7eb;
}
.dark-mode .theme-toggle:hover, .dark-mode .theme-toggle:focus {
    background: rgba(255,255,255,0.08);
}

/* Smooth theme transitions */
:root { --theme-transition-speed: 240ms; }
.theme-fab {
    position: fixed;
    top: 16px;
    right: 16px;
    z-index: 1040;
    border: 1px solid rgba(0,0,0,0.08);
    background: rgba(255,255,255,0.65);
    backdrop-filter: saturate(180%) blur(8px);
    -webkit-backdrop-filter: saturate(180%) blur(8px);
    color: #0f172a;
}
.dark-mode .theme-fab {
    background: rgba(17,24,39,0.6);
    border-color: rgba(255,255,255,0.18);
    color: #e5e7eb;
}
.theme-transition, .theme-transition * {
    transition: background-color var(--theme-transition-speed) ease,
                color var(--theme-transition-speed) ease,
                border-color var(--theme-transition-speed) ease,
                box-shadow var(--theme-transition-speed) ease;
}
/* Icon animation and image fade */
.theme-toggle .bi {
    transition: transform var(--theme-transition-speed) ease, opacity var(--theme-transition-speed) ease;
}
.theme-toggle.spin .bi { transform: rotate(180deg); }
.icon-fade { opacity: 0; }
.theme-transition img, .theme-transition .navbar-brand img {
    transition: opacity var(--theme-transition-speed) ease, filter var(--theme-transition-speed) ease;
}
.dark-mode img { filter: brightness(0.9) contrast(1.05); }
</style>
</head>
<body>
<!-- Floating dark-mode toggle (navbar removed) -->
<div class="theme-fab-wrap">
  <button id="darkModeToggle" class="btn theme-toggle theme-fab" aria-label="Toggle dark mode">
    <span id="darkModeIcon" class="bi bi-sun"></span>
  </button>
  
</div>
<script>
  // Dark mode toggle logic with system preference + icon animation
  function setDarkMode(on, persist = true) {
    document.body.classList.toggle('dark-mode', on);
    if (persist) localStorage.setItem('darkMode', on ? '1' : '0');
  }

  function updateIconClass(on) {
    const icon = document.getElementById('darkModeIcon');
    icon.className = on ? 'bi bi-moon' : 'bi bi-sun';
  }

  function animateIconSwap(on) {
    const btn = document.getElementById('darkModeToggle');
    const icon = document.getElementById('darkModeIcon');
    btn.classList.add('spin');
    icon.classList.add('icon-fade');
    setTimeout(function() { // halfway through fade, swap glyph
      updateIconClass(on);
      icon.classList.remove('icon-fade');
      setTimeout(function(){ btn.classList.remove('spin'); }, 180);
    }, 120);
  }

  document.addEventListener('DOMContentLoaded', function() {
    const darkModeBtn = document.getElementById('darkModeToggle');
    const stored = localStorage.getItem('darkMode');
    const media = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)');
    let isDark = stored === '1' ? true : stored === '0' ? false : (media ? media.matches : false);

    // Apply theme and set correct icon without animation on first load
    setDarkMode(isDark, false);
    updateIconClass(isDark);

    // If user hasn't chosen a preference, follow system changes
    if (stored === null && media && media.addEventListener) {
      media.addEventListener('change', function(e) {
        document.body.classList.add('theme-transition');
        setDarkMode(e.matches, false);
        animateIconSwap(e.matches);
        setTimeout(function(){ document.body.classList.remove('theme-transition'); }, 260);
      });
    }

    darkModeBtn.onclick = function() {
      // Animate theme change only on toggle
      document.body.classList.add('theme-transition');
      const next = !document.body.classList.contains('dark-mode');
      setDarkMode(next, true);
      animateIconSwap(next);
      setTimeout(function(){ document.body.classList.remove('theme-transition'); }, 260);
    };
  });
</script>
<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <div class="form-section">
                <h2 class="mb-3">Add/Update Grade</h2>
                <form action="update_grade.php" method="post" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Student</label>
                        <select name="student_id" class="form-select" required>
                            <option value="">Select...</option>
                            <?php foreach (
                                $students as $s): ?>
                                <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?> (<?= htmlspecialchars($s['student_number']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Subject</label>
                        <select name="subject_id" class="form-select" required>
                            <option value="">Select...</option>
                            <?php foreach ($subjects as $sub): ?>
                                <option value="<?= $sub['id'] ?>"><?= htmlspecialchars($sub['subject_code']) ?> - <?= htmlspecialchars($sub['subject_title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Grade</label>
                        <input type="number" name="grade" class="form-control" required min="0" max="5" step="0.01">
                    </div>
                    <div class="col-md-2 align-self-end">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Student Name</th>
                                    <th>Student #</th>
                                    <th>Subject Code</th>
                                    <th>Subject Title</th>
                                    <th>Grade</th>
                                    <th>Last Updated</th>
                                </tr>
                            </thead>
                            <tbody>
        <?php foreach ($grades as $g): ?>
            <tr>
                <td><?= htmlspecialchars($g['name']) ?></td>
                <td><?= htmlspecialchars($g['student_number']) ?></td>
                <td><?= htmlspecialchars($g['subject_code']) ?></td>
                <td><?= htmlspecialchars($g['subject_title']) ?></td>
                <td><?= htmlspecialchars($g['grade']) ?></td>
                <td><?= htmlspecialchars($g['last_updated']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</div>
</div>
</div>
</body>
</html>

<?php /** @var array $students */ ?>
<div class="dashboard-header d-flex align-items-center">
    <h2 class="me-3 mb-0">Students</h2>
  </div>
  <div class="table-responsive">
    <table class="table table-hover table-striped table-bordered align-middle">
      <thead class="table-light">
        <tr>
          <th>Name</th>
          <th>Student #</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($students as $s): ?>
        <tr>
          <td><?= htmlspecialchars($s['name']) ?></td>
          <td><?= htmlspecialchars($s['student_number']) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>



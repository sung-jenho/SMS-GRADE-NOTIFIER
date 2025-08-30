<?php /** @var array $students */ /** @var array $subjects */ /** @var array $grades */ ?>
<div class="form-section">
  <form action="update_grade.php" method="post" class="row g-3">
    <div class="col-md-3">
      <label class="form-label">Student</label>
      <select name="student_id" class="form-select" required>
        <option value="">Select...</option>
        <?php foreach ($students as $s): ?>
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
<div class="card mt-4">
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



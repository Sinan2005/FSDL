<?php
require 'db.php';

$student = null;
$success = "";
$error   = "";
$search_roll = isset($_GET['roll_no']) ? trim($_GET['roll_no']) : (isset($_POST['search_roll']) ? trim($_POST['search_roll']) : '');

// ─────────────────────────────────────────────
// SEARCH student
// ─────────────────────────────────────────────
if (!empty($search_roll)) {
    $safe = mysqli_real_escape_string($conn, $search_roll);
    $res  = mysqli_query($conn, "SELECT * FROM students WHERE roll_no='$safe'");
    if (mysqli_num_rows($res) > 0) {
        $student = mysqli_fetch_assoc($res);
    } else {
        $error = "No student found with Roll No: " . htmlspecialchars($search_roll);
    }
}

// ─────────────────────────────────────────────
// UPDATE student
// ─────────────────────────────────────────────
if (isset($_POST['update'])) {
    $roll    = trim(mysqli_real_escape_string($conn, $_POST['roll_no']));
    $first   = trim(mysqli_real_escape_string($conn, $_POST['first_name']));
    $last    = trim(mysqli_real_escape_string($conn, $_POST['last_name']));
    $contact = trim(mysqli_real_escape_string($conn, $_POST['contact']));
    $new_pwd = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    // PHP Validation
    if (empty($first) || empty($last) || empty($contact)) {
        $error = "First name, Last name and Contact are required.";
    } elseif (!preg_match('/^[A-Za-z ]+$/', $first) || !preg_match('/^[A-Za-z ]+$/', $last)) {
        $error = "Names must contain only letters.";
    } elseif (!preg_match('/^[0-9]{10}$/', $contact)) {
        $error = "Contact must be exactly 10 digits.";
    } elseif (!empty($new_pwd) && strlen($new_pwd) < 6) {
        $error = "New password must be at least 6 characters.";
    } elseif (!empty($new_pwd) && $new_pwd !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        if (!empty($new_pwd)) {
            $hashed = password_hash($new_pwd, PASSWORD_DEFAULT);
            $sql = "UPDATE students SET first_name='$first', last_name='$last',
                    contact='$contact', password='$hashed' WHERE roll_no='$roll'";
        } else {
            $sql = "UPDATE students SET first_name='$first', last_name='$last',
                    contact='$contact' WHERE roll_no='$roll'";
        }
        if (mysqli_query($conn, $sql)) {
            $success = "✅ Student record updated successfully!";
            // Re-fetch updated student
            $res2   = mysqli_query($conn, "SELECT * FROM students WHERE roll_no='$roll'");
            $student = mysqli_fetch_assoc($res2);
            $search_roll = $roll;
        } else {
            $error = "DB Error: " . mysqli_error($conn);
        }
    }

    // Keep student populated on error
    if ($error) {
        $safe = mysqli_real_escape_string($conn, $roll);
        $res3 = mysqli_query($conn, "SELECT * FROM students WHERE roll_no='$safe'");
        $student = mysqli_fetch_assoc($res3);
        $search_roll = $roll;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Update Student – Student Registration System</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=DM+Sans:wght@300;400;500;600&display=swap');

  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --navy: #0f1b2d; --blue: #1a56db; --sky: #60a5fa;
    --cream: #f8f5ef; --gold: #f59e0b; --red: #ef4444; --green: #10b981;
    --border: #d1d5db; --shadow: 0 4px 24px rgba(15,27,45,.12);
  }

  body { font-family:'DM Sans',sans-serif; background:var(--cream); color:var(--navy); min-height:100vh; }

  header {
    background:var(--navy); color:#fff; padding:0 40px;
    display:flex; align-items:center; justify-content:space-between;
    height:70px; box-shadow:0 2px 16px rgba(0,0,0,.3);
  }
  header h1 { font-family:'DM Serif Display',serif; font-size:1.4rem; }
  header a  { color:var(--sky); text-decoration:none; font-size:.88rem; transition:color .2s; }
  header a:hover { color:#fff; }

  .container { max-width:820px; margin:0 auto; padding:36px 40px; }

  .card { background:#fff; border-radius:12px; box-shadow:var(--shadow); padding:32px; margin-bottom:24px; }
  .card-title { font-family:'DM Serif Display',serif; font-size:1.2rem; margin-bottom:22px;
                padding-bottom:14px; border-bottom:1px solid var(--border); }

  .search-row { display:flex; gap:12px; align-items:flex-end; flex-wrap:wrap; }
  .form-group { display:flex; flex-direction:column; gap:6px; }
  .form-group.flex1 { flex:1; min-width:200px; }

  .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:18px; }
  .form-group.full { grid-column:span 2; }

  label { font-size:.82rem; font-weight:600; letter-spacing:.3px; color:#374151; text-transform:uppercase; }

  input[type=text],input[type=password],input[type=tel] {
    padding:10px 14px; border:1.5px solid var(--border); border-radius:8px;
    font-family:'DM Sans',sans-serif; font-size:.95rem; outline:none; transition:border-color .2s,box-shadow .2s;
  }
  input:focus { border-color:var(--blue); box-shadow:0 0 0 3px rgba(26,86,219,.1); }
  input.error-field { border-color:var(--red); }
  input[readonly] { background:#f9fafb; color:#6b7280; cursor:default; }

  .field-error { font-size:.78rem; color:var(--red); margin-top:2px; }

  .btn { padding:11px 26px; border:none; border-radius:8px; font-family:'DM Sans',sans-serif;
         font-size:.92rem; font-weight:600; cursor:pointer; transition:all .2s;
         display:inline-flex; align-items:center; gap:6px; }
  .btn-primary { background:var(--blue); color:#fff; }
  .btn-primary:hover { background:#1447c2; transform:translateY(-1px); box-shadow:0 4px 12px rgba(26,86,219,.3); }
  .btn-warning { background:var(--gold); color:#fff; }
  .btn-warning:hover { background:#d97706; }
  .btn-outline { background:transparent; border:2px solid var(--navy); color:var(--navy); }
  .btn-outline:hover { background:var(--navy); color:#fff; }

  .alert { padding:13px 18px; border-radius:8px; margin-bottom:22px; font-weight:500; font-size:.93rem; }
  .alert-success { background:#d1fae5; color:#065f46; border:1px solid #a7f3d0; }
  .alert-error   { background:#fee2e2; color:#991b1b; border:1px solid #fecaca; }

  .student-badge {
    display:inline-flex; align-items:center; gap:8px;
    background:#eff6ff; border:1px solid #bfdbfe; border-radius:8px;
    padding:8px 16px; margin-bottom:20px; font-size:.9rem; color:var(--blue);
  }
  .note { font-size:.82rem; color:#6b7280; margin-top:6px; }

  @media(max-width:650px) {
    .form-grid { grid-template-columns:1fr; }
    .form-group.full { grid-column:span 1; }
    .container { padding:20px 16px; }
    header { padding:0 16px; }
  }
</style>
</head>
<body>

<header>
  <h1>✏️ Update Student Record</h1>
  <a href="index.php">← Back to Dashboard</a>
</header>

<div class="container">

  <?php if ($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
  <?php elseif ($error && !$student): ?>
    <div class="alert alert-error">❌ <?= $error ?></div>
  <?php endif; ?>

  <!-- SEARCH FORM -->
  <div class="card">
    <div class="card-title">🔍 Find Student by Roll No</div>
    <form method="GET" action="update.php">
      <div class="search-row">
        <div class="form-group flex1">
          <label>Roll No / ID</label>
          <input type="text" name="roll_no" placeholder="e.g. CS2024001"
                 value="<?= htmlspecialchars($search_roll) ?>" required>
        </div>
        <button type="submit" class="btn btn-warning">🔍 Search</button>
        <a href="index.php" class="btn btn-outline">Cancel</a>
      </div>
    </form>
  </div>

  <!-- UPDATE FORM (shown only when student found) -->
  <?php if ($student): ?>
  <div class="card">
    <div class="card-title">Update Details</div>

    <?php if ($error && $student): ?>
      <div class="alert alert-error">❌ <?= $error ?></div>
    <?php endif; ?>

    <div class="student-badge">
      🎓 Editing: <strong><?= htmlspecialchars($student['first_name'].' '.$student['last_name']) ?></strong>
      &nbsp;|&nbsp; Roll No: <strong><?= htmlspecialchars($student['roll_no']) ?></strong>
    </div>

    <form method="POST" action="update.php" id="updForm" novalidate>
      <input type="hidden" name="roll_no"     value="<?= htmlspecialchars($student['roll_no']) ?>">
      <input type="hidden" name="search_roll" value="<?= htmlspecialchars($student['roll_no']) ?>">

      <div class="form-grid">

        <div class="form-group">
          <label>First Name *</label>
          <input type="text" name="first_name" id="u_first"
                 value="<?= htmlspecialchars(isset($_POST['first_name']) ? $_POST['first_name'] : $student['first_name']) ?>">
          <span class="field-error" id="err_first"></span>
        </div>

        <div class="form-group">
          <label>Last Name *</label>
          <input type="text" name="last_name" id="u_last"
                 value="<?= htmlspecialchars(isset($_POST['last_name']) ? $_POST['last_name'] : $student['last_name']) ?>">
          <span class="field-error" id="err_last"></span>
        </div>

        <div class="form-group">
          <label>Roll No (Read-only)</label>
          <input type="text" value="<?= htmlspecialchars($student['roll_no']) ?>" readonly>
        </div>

        <div class="form-group">
          <label>Contact Number *</label>
          <input type="tel" name="contact" id="u_contact"
                 value="<?= htmlspecialchars(isset($_POST['contact']) ? $_POST['contact'] : $student['contact']) ?>">
          <span class="field-error" id="err_contact"></span>
          <span class="note">Update your 10-digit mobile number</span>
        </div>

        <div class="form-group">
          <label>New Password <small style="text-transform:none;font-weight:400">(leave blank to keep current)</small></label>
          <input type="password" name="new_password" id="u_pwd" placeholder="New password (optional)">
          <span class="field-error" id="err_pwd"></span>
        </div>

        <div class="form-group">
          <label>Confirm New Password</label>
          <input type="password" name="confirm_password" id="u_cpwd" placeholder="Repeat new password">
          <span class="field-error" id="err_cpwd"></span>
        </div>

        <div class="form-group full" style="margin-top:8px; display:flex; gap:12px; flex-wrap:wrap;">
          <button type="submit" name="update" class="btn btn-primary" onclick="return validateUpdate()">
            💾 Save Changes
          </button>
          <a href="index.php" class="btn btn-outline">Cancel</a>
        </div>
      </div>
    </form>
  </div>
  <?php endif; ?>

</div>

<script>
function validateUpdate() {
  let valid = true;
  document.querySelectorAll('.field-error').forEach(e => e.textContent='');
  document.querySelectorAll('input:not([readonly])').forEach(i => i.classList.remove('error-field'));

  const first   = document.getElementById('u_first');
  const last    = document.getElementById('u_last');
  const contact = document.getElementById('u_contact');
  const pwd     = document.getElementById('u_pwd');
  const cpwd    = document.getElementById('u_cpwd');

  if (!first.value.trim()) {
    document.getElementById('err_first').textContent = 'Required.'; first.classList.add('error-field'); valid=false;
  } else if (!/^[A-Za-z ]+$/.test(first.value.trim())) {
    document.getElementById('err_first').textContent = 'Letters only.'; first.classList.add('error-field'); valid=false;
  }
  if (!last.value.trim()) {
    document.getElementById('err_last').textContent = 'Required.'; last.classList.add('error-field'); valid=false;
  } else if (!/^[A-Za-z ]+$/.test(last.value.trim())) {
    document.getElementById('err_last').textContent = 'Letters only.'; last.classList.add('error-field'); valid=false;
  }
  if (!/^[0-9]{10}$/.test(contact.value.trim())) {
    document.getElementById('err_contact').textContent = 'Enter a valid 10-digit number.';
    contact.classList.add('error-field'); valid=false;
  }
  if (pwd.value && pwd.value.length < 6) {
    document.getElementById('err_pwd').textContent = 'Min 6 characters.'; pwd.classList.add('error-field'); valid=false;
  }
  if (pwd.value && pwd.value !== cpwd.value) {
    document.getElementById('err_cpwd').textContent = 'Passwords do not match.'; cpwd.classList.add('error-field'); valid=false;
  }
  return valid;
}
</script>
</body>
</html>

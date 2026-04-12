<?php
require 'db.php';

$success = "";
$error = "";

// ─────────────────────────────────────────────
// INSERT
// ─────────────────────────────────────────────
if (isset($_POST['insert'])) {
    $first     = trim(mysqli_real_escape_string($conn, $_POST['first_name']));
    $last      = trim(mysqli_real_escape_string($conn, $_POST['last_name']));
    $roll      = trim(mysqli_real_escape_string($conn, $_POST['roll_no']));
    $password  = $_POST['password'];
    $confirm   = $_POST['confirm_password'];
    $contact   = trim(mysqli_real_escape_string($conn, $_POST['contact']));

    // PHP Validation
    if (empty($first) || empty($last) || empty($roll) || empty($password) || empty($contact)) {
        $error = "All fields are required.";
    } elseif (!preg_match('/^[A-Za-z ]+$/', $first) || !preg_match('/^[A-Za-z ]+$/', $last)) {
        $error = "First and Last name must contain only letters.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } elseif (!preg_match('/^[0-9]{10}$/', $contact)) {
        $error = "Contact number must be exactly 10 digits.";
    } else {
        // Check if Roll No already exists
        $check = mysqli_query($conn, "SELECT roll_no FROM students WHERE roll_no='$roll'");
        if (mysqli_num_rows($check) > 0) {
            $error = "Roll No / ID '$roll' already exists.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO students (first_name, last_name, roll_no, password, contact)
                    VALUES ('$first','$last','$roll','$hashed','$contact')";
            if (mysqli_query($conn, $sql)) {
                $success = "✅ Student registered successfully!";
            } else {
                $error = "DB Error: " . mysqli_error($conn);
            }
        }
    }
}

// ─────────────────────────────────────────────
// DELETE
// ─────────────────────────────────────────────
if (isset($_POST['delete'])) {
    $roll = trim(mysqli_real_escape_string($conn, $_POST['del_roll']));
    if (empty($roll)) {
        $error = "Please enter a Roll No to delete.";
    } else {
        $check = mysqli_query($conn, "SELECT roll_no FROM students WHERE roll_no='$roll'");
        if (mysqli_num_rows($check) == 0) {
            $error = "No student found with Roll No: $roll";
        } else {
            mysqli_query($conn, "DELETE FROM students WHERE roll_no='$roll'");
            $success = "🗑️ Student with Roll No '$roll' deleted successfully.";
        }
    }
}

// Fetch all students
$students = mysqli_query($conn, "SELECT * FROM students ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Registration System</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=DM+Sans:wght@300;400;500;600&display=swap');

  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --navy:   #0f1b2d;
    --blue:   #1a56db;
    --sky:    #60a5fa;
    --cream:  #f8f5ef;
    --gold:   #f59e0b;
    --red:    #ef4444;
    --green:  #10b981;
    --border: #d1d5db;
    --shadow: 0 4px 24px rgba(15,27,45,.12);
  }

  body {
    font-family: 'DM Sans', sans-serif;
    background: var(--cream);
    color: var(--navy);
    min-height: 100vh;
  }

  /* ── HEADER ── */
  header {
    background: var(--navy);
    color: #fff;
    padding: 0 40px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 70px;
    position: sticky; top: 0; z-index: 100;
    box-shadow: 0 2px 16px rgba(0,0,0,.3);
  }
  header h1 { font-family: 'DM Serif Display', serif; font-size: 1.5rem; letter-spacing: .3px; }
  header span { font-size: .85rem; color: var(--sky); }

  /* ── NAV TABS ── */
  .tabs {
    display: flex; gap: 4px;
    background: #fff;
    border-bottom: 2px solid var(--border);
    padding: 0 40px;
  }
  .tab-btn {
    padding: 14px 22px;
    border: none; background: none;
    font-family: 'DM Sans', sans-serif; font-size: .9rem; font-weight: 500;
    cursor: pointer; color: #6b7280;
    border-bottom: 3px solid transparent; margin-bottom: -2px;
    transition: all .2s;
  }
  .tab-btn.active { color: var(--blue); border-bottom-color: var(--blue); }
  .tab-btn:hover:not(.active) { color: var(--navy); background: var(--cream); }

  /* ── SECTIONS ── */
  .section { display: none; padding: 36px 40px; max-width: 1100px; margin: 0 auto; }
  .section.active { display: block; animation: fadeIn .3s ease; }
  @keyframes fadeIn { from { opacity:0; transform:translateY(6px) } to { opacity:1; transform:none } }

  /* ── CARDS ── */
  .card {
    background: #fff;
    border-radius: 12px;
    box-shadow: var(--shadow);
    padding: 32px;
    margin-bottom: 28px;
  }
  .card-title {
    font-family: 'DM Serif Display', serif;
    font-size: 1.25rem;
    margin-bottom: 24px;
    padding-bottom: 14px;
    border-bottom: 1px solid var(--border);
    color: var(--navy);
  }

  /* ── FORM GRID ── */
  .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
  .form-group { display: flex; flex-direction: column; gap: 6px; }
  .form-group.full { grid-column: span 2; }

  label { font-size: .82rem; font-weight: 600; letter-spacing: .3px; color: #374151; text-transform: uppercase; }

  input[type=text], input[type=password], input[type=tel], input[type=number] {
    padding: 10px 14px;
    border: 1.5px solid var(--border);
    border-radius: 8px;
    font-family: 'DM Sans', sans-serif; font-size: .95rem;
    transition: border-color .2s, box-shadow .2s;
    outline: none;
  }
  input:focus { border-color: var(--blue); box-shadow: 0 0 0 3px rgba(26,86,219,.1); }
  input.error-field { border-color: var(--red); }

  .field-error { font-size: .78rem; color: var(--red); margin-top: 2px; }

  /* ── BUTTONS ── */
  .btn {
    padding: 11px 26px; border: none; border-radius: 8px;
    font-family: 'DM Sans', sans-serif; font-size: .92rem; font-weight: 600;
    cursor: pointer; transition: all .2s; display: inline-flex; align-items: center; gap: 6px;
  }
  .btn-primary { background: var(--blue); color: #fff; }
  .btn-primary:hover { background: #1447c2; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(26,86,219,.3); }
  .btn-danger  { background: var(--red);  color: #fff; }
  .btn-danger:hover  { background: #dc2626; transform: translateY(-1px); }
  .btn-warning { background: var(--gold); color: #fff; }
  .btn-warning:hover { background: #d97706; transform: translateY(-1px); }

  /* ── ALERTS ── */
  .alert {
    padding: 13px 18px; border-radius: 8px; margin-bottom: 22px;
    font-weight: 500; font-size: .93rem;
  }
  .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
  .alert-error   { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

  /* ── TABLE ── */
  .table-wrap { overflow-x: auto; border-radius: 10px; border: 1px solid var(--border); }
  table { width: 100%; border-collapse: collapse; font-size: .9rem; }
  thead { background: var(--navy); color: #fff; }
  th { padding: 13px 16px; text-align: left; font-weight: 600; font-size: .8rem; letter-spacing: .5px; text-transform: uppercase; }
  td { padding: 12px 16px; border-bottom: 1px solid #f3f4f6; }
  tr:last-child td { border-bottom: none; }
  tr:hover td { background: #f0f4ff; }
  .badge {
    display: inline-block; padding: 3px 10px; border-radius: 20px;
    font-size: .75rem; font-weight: 600;
    background: #dbeafe; color: var(--blue);
  }
  .action-link {
    color: var(--blue); text-decoration: none; font-weight: 600; font-size: .85rem;
    padding: 4px 10px; border-radius: 5px; border: 1px solid #bfdbfe;
    transition: all .15s;
  }
  .action-link:hover { background: var(--blue); color: #fff; }
  .empty-state { text-align: center; padding: 48px; color: #9ca3af; }
  .empty-state span { font-size: 2.5rem; display: block; margin-bottom: 10px; }

  /* ── INLINE DELETE ROW ── */
  .del-row { display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap; }
  .del-row .form-group { flex: 1; min-width: 200px; }

  @media(max-width:650px) {
    .form-grid { grid-template-columns: 1fr; }
    .form-group.full { grid-column: span 1; }
    .section { padding: 20px 16px; }
    header { padding: 0 16px; }
    .tabs { padding: 0 8px; }
  }
</style>
</head>
<body>

<header>
  <h1>🎓 Student Registration System</h1>
  <span>PHP CRUD &nbsp;|&nbsp; phpMyAdmin</span>
</header>

<div class="tabs">
  <button class="tab-btn active" onclick="showTab('register',this)">📝 Register</button>
  <button class="tab-btn" onclick="showTab('delete',this)">🗑️ Delete</button>
  <button class="tab-btn" onclick="showTab('update',this)">✏️ Update</button>
  <button class="tab-btn" onclick="showTab('view',this)">📋 View All</button>
</div>

<!-- ════════════════════════════════════════════
     1. REGISTER
════════════════════════════════════════════ -->
<div id="register" class="section active">

  <?php if ($success && isset($_POST['insert'])): ?>
    <div class="alert alert-success"><?= $success ?></div>
  <?php elseif ($error && isset($_POST['insert'])): ?>
    <div class="alert alert-error">❌ <?= $error ?></div>
  <?php endif; ?>

  <div class="card">
    <div class="card-title">New Student Registration</div>
    <form method="POST" id="regForm" novalidate>
      <div class="form-grid">

        <div class="form-group">
          <label>First Name *</label>
          <input type="text" name="first_name" id="first_name" placeholder="e.g. Ravi"
                 value="<?= isset($_POST['insert']) ? htmlspecialchars($_POST['first_name']) : '' ?>">
          <span class="field-error" id="err_first"></span>
        </div>

        <div class="form-group">
          <label>Last Name *</label>
          <input type="text" name="last_name" id="last_name" placeholder="e.g. Sharma"
                 value="<?= isset($_POST['insert']) ? htmlspecialchars($_POST['last_name']) : '' ?>">
          <span class="field-error" id="err_last"></span>
        </div>

        <div class="form-group">
          <label>Roll No / ID *</label>
          <input type="text" name="roll_no" id="roll_no" placeholder="e.g. CS2024001"
                 value="<?= isset($_POST['insert']) ? htmlspecialchars($_POST['roll_no']) : '' ?>">
          <span class="field-error" id="err_roll"></span>
        </div>

        <div class="form-group">
          <label>Contact Number *</label>
          <input type="tel" name="contact" id="contact" placeholder="10-digit mobile"
                 value="<?= isset($_POST['insert']) ? htmlspecialchars($_POST['contact']) : '' ?>">
          <span class="field-error" id="err_contact"></span>
        </div>

        <div class="form-group">
          <label>Password *</label>
          <input type="password" name="password" id="password" placeholder="Min 6 characters">
          <span class="field-error" id="err_pwd"></span>
        </div>

        <div class="form-group">
          <label>Confirm Password *</label>
          <input type="password" name="confirm_password" id="confirm_password" placeholder="Repeat password">
          <span class="field-error" id="err_cpwd"></span>
        </div>

        <div class="form-group full" style="margin-top:8px;">
          <button type="submit" name="insert" class="btn btn-primary" onclick="return validateForm()">
            ➕ Register Student
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- ════════════════════════════════════════════
     2. DELETE
════════════════════════════════════════════ -->
<div id="delete" class="section">

  <?php if ($success && isset($_POST['delete'])): ?>
    <div class="alert alert-success"><?= $success ?></div>
  <?php elseif ($error && isset($_POST['delete'])): ?>
    <div class="alert alert-error">❌ <?= $error ?></div>
  <?php endif; ?>

  <div class="card">
    <div class="card-title">Delete Student Record</div>
    <form method="POST" onsubmit="return confirmDelete()">
      <div class="del-row">
        <div class="form-group">
          <label>Roll No / ID to Delete *</label>
          <input type="text" name="del_roll" id="del_roll" placeholder="Enter exact Roll No" style="width:280px">
        </div>
        <button type="submit" name="delete" class="btn btn-danger">🗑️ Delete Student</button>
      </div>
    </form>
  </div>

  <!-- Show current records for reference -->
  <div class="card">
    <div class="card-title">Current Students (Reference)</div>
    <?php
    $all = mysqli_query($conn, "SELECT id, first_name, last_name, roll_no, contact FROM students ORDER BY id DESC");
    if (mysqli_num_rows($all) == 0): ?>
      <div class="empty-state"><span>📭</span>No student records found.</div>
    <?php else: ?>
      <div class="table-wrap">
        <table>
          <thead><tr><th>#</th><th>Roll No</th><th>First Name</th><th>Last Name</th><th>Contact</th></tr></thead>
          <tbody>
          <?php $i=1; while($r = mysqli_fetch_assoc($all)): ?>
            <tr>
              <td><?= $i++ ?></td>
              <td><span class="badge"><?= htmlspecialchars($r['roll_no']) ?></span></td>
              <td><?= htmlspecialchars($r['first_name']) ?></td>
              <td><?= htmlspecialchars($r['last_name']) ?></td>
              <td><?= htmlspecialchars($r['contact']) ?></td>
            </tr>
          <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- ════════════════════════════════════════════
     3. UPDATE  (redirects to update.php)
════════════════════════════════════════════ -->
<div id="update" class="section">
  <div class="card">
    <div class="card-title">Update Student Details</div>
    <p style="margin-bottom:20px; color:#6b7280;">Search a student by their Roll No / ID to update their details.</p>
    <form method="GET" action="update.php">
      <div class="del-row">
        <div class="form-group">
          <label>Roll No / ID *</label>
          <input type="text" name="roll_no" placeholder="Enter Roll No to search" style="width:280px" required>
        </div>
        <button type="submit" class="btn btn-warning">🔍 Search & Update</button>
      </div>
    </form>
  </div>
</div>

<!-- ════════════════════════════════════════════
     4. VIEW ALL
════════════════════════════════════════════ -->
<div id="view" class="section">
  <div class="card">
    <div class="card-title">All Student Records</div>
    <?php
    $all2 = mysqli_query($conn, "SELECT * FROM students ORDER BY id DESC");
    if (mysqli_num_rows($all2) == 0): ?>
      <div class="empty-state"><span>📭</span>No students registered yet. Go to Register tab!</div>
    <?php else: ?>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>#</th><th>Roll No</th><th>First Name</th>
              <th>Last Name</th><th>Contact</th><th>Action</th>
            </tr>
          </thead>
          <tbody>
          <?php $i=1; while($r = mysqli_fetch_assoc($all2)): ?>
            <tr>
              <td><?= $i++ ?></td>
              <td><span class="badge"><?= htmlspecialchars($r['roll_no']) ?></span></td>
              <td><?= htmlspecialchars($r['first_name']) ?></td>
              <td><?= htmlspecialchars($r['last_name']) ?></td>
              <td><?= htmlspecialchars($r['contact']) ?></td>
              <td>
                <a href="update.php?roll_no=<?= urlencode($r['roll_no']) ?>" class="action-link">✏️ Edit</a>
              </td>
            </tr>
          <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- ════════════════════════════════════════════
     JAVASCRIPT – Client-side Validation
════════════════════════════════════════════ -->
<script>
// Tab switching
function showTab(id, btn) {
  document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  document.getElementById(id).classList.add('active');
  btn.classList.add('active');
}

// Auto-switch tab if there was a POST error/success
<?php
if (isset($_POST['delete']))  echo "showTab('delete',  document.querySelectorAll('.tab-btn')[1]);";
if (isset($_POST['insert']))  echo "showTab('register',document.querySelectorAll('.tab-btn')[0]);";
?>

// Registration form validation
function validateForm() {
  let valid = true;

  const fields = {
    first_name:       { id:'err_first',   regex:/^[A-Za-z ]+$/, msg:'Only letters allowed.' },
    last_name:        { id:'err_last',    regex:/^[A-Za-z ]+$/, msg:'Only letters allowed.' },
    roll_no:          { id:'err_roll',    regex:/\S+/,           msg:'Roll No cannot be empty.' },
    contact:          { id:'err_contact', regex:/^[0-9]{10}$/,   msg:'Enter a valid 10-digit number.' },
  };

  // Clear previous errors
  document.querySelectorAll('.field-error').forEach(e => e.textContent = '');
  document.querySelectorAll('input').forEach(i => i.classList.remove('error-field'));

  for (const [name, rule] of Object.entries(fields)) {
    const el  = document.getElementById(name);
    const val = el.value.trim();
    if (!val) {
      document.getElementById(rule.id).textContent = 'This field is required.';
      el.classList.add('error-field');
      valid = false;
    } else if (!rule.regex.test(val)) {
      document.getElementById(rule.id).textContent = rule.msg;
      el.classList.add('error-field');
      valid = false;
    }
  }

  // Password checks
  const pwd  = document.getElementById('password');
  const cpwd = document.getElementById('confirm_password');
  if (pwd.value.length < 6) {
    document.getElementById('err_pwd').textContent = 'Password must be at least 6 characters.';
    pwd.classList.add('error-field');
    valid = false;
  }
  if (pwd.value !== cpwd.value) {
    document.getElementById('err_cpwd').textContent = 'Passwords do not match.';
    cpwd.classList.add('error-field');
    valid = false;
  }

  return valid;
}

// Delete confirmation
function confirmDelete() {
  const roll = document.getElementById('del_roll').value.trim();
  if (!roll) { alert('Please enter a Roll No first.'); return false; }
  return confirm('Are you sure you want to delete student with Roll No: ' + roll + '?\nThis cannot be undone!');
}
</script>
</body>
</html>

<?php
session_start();
if(!isset($_SESSION['admin'])) header("Location: admin_login.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Student | RFID System</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <nav><h2>RFID Attendance System – Add Student</h2></nav>

  <div class="container">
    <form id="addForm" method="POST">
      <label>Roll No:</label>
      <input type="text" name="roll_no" required>

      <label>Student Name:</label>
      <input type="text" name="name" required>

      <label>Class Name:</label>
      <input type="text" name="class_name" required>

      <label>RFID UID:</label>
      <input type="text" name="rfid_uid" id="rfid_uid" placeholder="Scan card or wait…" readonly required>

      <button type="submit">Save Student</button>
    </form>
  </div>

  <script>
    // Poll server for new scanned UID every 2 seconds
    async function pollRFID() {
      const r = await fetch("api/new_scans.php");
      const j = await r.json();
      if (j.length) {
        document.getElementById("rfid_uid").value = j[0].rfid_uid;
      }
    }
    setInterval(pollRFID, 2000);

    // Send form to backend
    document.getElementById("addForm").onsubmit = async (e) => {
      e.preventDefault();
      const f = new FormData(e.target);
      const r = await fetch("api/add_student.php", {method:"POST", body:f});
      const j = await r.json();
      if (j.success) alert("✅ Student added successfully!");
      else alert("❌ Error: " + j.message);
    };
  </script>
</body>
</html>

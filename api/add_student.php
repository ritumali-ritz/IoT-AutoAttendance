<?php
require __DIR__ . '/db_connect.php';

$roll  = $conn->real_escape_string($_POST['roll_no'] ?? '');
$name  = $conn->real_escape_string($_POST['name'] ?? '');
$class = $conn->real_escape_string($_POST['class_name'] ?? '');
$uid   = strtoupper(trim($conn->real_escape_string($_POST['rfid_uid'] ?? '')));

if(!$roll || !$name || !$class || !$uid){
  echo json_encode(['success'=>false,'message'=>'All fields required']);
  exit;
}

// Check duplicate UID
$chk = $conn->query("SELECT id FROM students WHERE rfid_tag='{$uid}'");
if($chk->num_rows){
  echo json_encode(['success'=>false,'message'=>'Card already assigned!']);
  exit;
}

// Insert student
$q = $conn->query("INSERT INTO students (roll_no, name, class_name, rfid_tag) 
                   VALUES ('{$roll}','{$name}','{$class}','{$uid}')");

if($q){
  $conn->query("UPDATE scans SET processed=1 WHERE rfid_uid='{$uid}'");
  echo json_encode(['success'=>true]);
}else{
  echo json_encode(['success'=>false,'message'=>$conn->error]);
}
?>

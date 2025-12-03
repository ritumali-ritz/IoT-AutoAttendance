<?php
header('Content-Type: application/json');
require __DIR__ . '/db_connect.php';

$data = null;
$ctype = $_SERVER['CONTENT_TYPE'] ?? '';
if(stripos($ctype,'application/json') !== false){
  $data = json_decode(file_get_contents('php://input'), true);
} else {
  $data = $_POST;
}

$uid = isset($data['rfid_uid']) ? trim($data['rfid_uid']) : '';
$roll = isset($data['roll_no']) ? trim($data['roll_no']) : '';
if(!$uid || !$roll){ http_response_code(400); echo json_encode(['success'=>false,'message'=>'rfid_uid and roll_no required']); exit; }

$uid_esc = $conn->real_escape_string($uid);
$roll_esc = $conn->real_escape_string($roll);

// Assign card to student
$u = $conn->query("UPDATE students SET card_uid = '{$uid_esc}' WHERE roll_no = '{$roll_esc}'");
if(!$u){ http_response_code(500); echo json_encode(['success'=>false,'message'=>$conn->error]); exit; }

// Insert attendance if not present today
$date = date('Y-m-d');
$chk = $conn->query("SELECT COUNT(*) AS cnt FROM attendance WHERE roll_no='{$roll_esc}' AND date='{$date}'");
$c = 0; if($chk){ $r = $chk->fetch_assoc(); $c = intval($r['cnt']); }
if($c === 0){
  $time = date('H:i:s');
  $ins = $conn->query("INSERT INTO attendance (roll_no,date,time,status) VALUES ('{$roll_esc}','{$date}','{$time}','Present')");
  if(!$ins){ http_response_code(500); echo json_encode(['success'=>false,'message'=>$conn->error]); exit; }
}

// Mark scans processed for this uid
$conn->query("UPDATE scans SET processed=1 WHERE rfid_uid='{$uid_esc}'");

echo json_encode(['success'=>true,'mapped'=>true,'attendance_created'=>($c===0)]);
?>

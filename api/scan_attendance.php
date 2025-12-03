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
if(!$uid){ http_response_code(400); echo json_encode(['success'=>false,'message'=>'rfid_uid required']); exit; }

$uid_esc = $conn->real_escape_string($uid);

// Try to find a student with this card_uid
$res = $conn->query("SELECT roll_no, name FROM students WHERE card_uid='{$uid_esc}' LIMIT 1");
if($res && $row = $res->fetch_assoc()){
  $roll = $conn->real_escape_string($row['roll_no']);
  $date = date('Y-m-d');
  $time = date('H:i:s');
  // prevent duplicate attendance entries for same day
  $chk = $conn->query("SELECT COUNT(*) AS cnt FROM attendance WHERE roll_no='{$roll}' AND date='{$date}'");
  $c = 0; if($chk){ $r = $chk->fetch_assoc(); $c = intval($r['cnt']); }
  if($c === 0){
    $ins = $conn->query("INSERT INTO attendance (roll_no,date,time,status) VALUES ('{$roll}','{$date}','{$time}','Present')");
    if(!$ins){ http_response_code(500); echo json_encode(['success'=>false,'message'=>$conn->error]); exit; }
  }
  // mark any matching scans processed
  $conn->query("UPDATE scans SET processed=1 WHERE rfid_uid='{$uid_esc}'");
  echo json_encode(['success'=>true,'mapped'=>true,'roll_no'=>$roll,'message'=>($c===0?'attendance recorded':'already recorded')]);
  exit;
} else {
  // Not mapped to a student yet: insert scan into scans table so admin can assign later
  $ins = $conn->query("INSERT INTO scans (rfid_uid, processed) VALUES ('{$uid_esc}',0)");
  if($ins) echo json_encode(['success'=>true,'mapped'=>false,'message'=>'scan saved']);
  else { http_response_code(500); echo json_encode(['success'=>false,'message'=>$conn->error]); }
  exit;
}

?>

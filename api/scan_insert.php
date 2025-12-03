<?php
// Accepts POST (json or form) with 'rfid_uid' and inserts into scans table.
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

$uid = $conn->real_escape_string($uid);
$q = $conn->query("INSERT INTO scans (rfid_uid, processed) VALUES ('{$uid}',0)");
if($q) echo json_encode(['success'=>true]);
else { http_response_code(500); echo json_encode(['success'=>false,'message'=>$conn->error]); }

?>

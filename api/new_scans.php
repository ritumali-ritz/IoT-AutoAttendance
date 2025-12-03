<?php
require __DIR__ . '/db_connect.php';
$r = $conn->query("SELECT rfid_uid, received_at FROM scans WHERE processed=0 ORDER BY id DESC LIMIT 5");
$data = [];
if($r){
	while($row = $r->fetch_assoc()) $data[] = $row;
}
header('Content-Type: application/json');
echo json_encode($data);
?>

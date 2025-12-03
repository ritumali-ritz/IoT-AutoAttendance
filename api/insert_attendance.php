<?php
header('Content-Type: application/json');
$d=json_decode(file_get_contents('php://input'),true);
require 'db.php';

if(!$d) { echo json_encode(['success'=>false,'message'=>'no input']); exit; }

 $roll=$mysqli->real_escape_string($d['roll_no']); $cid=intval($d['class_id']); $time = date('H:i:s'); $date=date('Y-m-d'); $status='Present'; // ensure student exists
$mysqli->query("INSERT IGNORE INTO students (roll_no,name,class_id) VALUES ('{$roll}','Student_{$roll}',{$cid})"); $mysqli->query("INSERT INTO attendance (roll_no,date,time,status) VALUES ('{$roll}','{$date}','{$time}','{$status}')"); echo json_encode(['success'=>true]);
?>
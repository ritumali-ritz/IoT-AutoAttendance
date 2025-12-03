<?php
header('Content-Type: application/json');
require 'sms_helper.php';

$method = $_SERVER['REQUEST_METHOD'];
$input = null;
if($method === 'POST'){
  $input = json_decode(file_get_contents('php://input'), true);
} else {
  // allow GET for quick tests: ?to=whatsapp:+91...&message=Hello
  $input = ['to'=>isset($_GET['to'])?$_GET['to']:null,'message'=>isset($_GET['message'])?$_GET['message']:null];
}

if(empty($input['to']) || empty($input['message'])){
  http_response_code(400);
  echo json_encode(['success'=>false,'message'=>'Provide `to` and `message` (GET or POST JSON). Example: ?to=whatsapp:+9198...&message=Hi']);
  exit;
}

$ok = send_sms($input['to'], $input['message']);
if($ok){
  echo json_encode(['success'=>true,'message'=>'Sent (or queued)']);
} else {
  http_response_code(500);
  echo json_encode(['success'=>false,'message'=>'Send failed (check sms_config and Twilio account)']);
}

?>

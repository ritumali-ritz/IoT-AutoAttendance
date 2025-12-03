<?php
// Fast2SMS-only helper
function send_sms($to, $message){
  $cfg = include __DIR__ . '/sms_config.php';
  $fast = isset($cfg['fast2sms']) ? $cfg['fast2sms'] : [];
  return send_via_fast2sms($to, $message, $fast);
}

function send_via_fast2sms($to, $message, $cfg){
  $apiKey = isset($cfg['api_key']) ? $cfg['api_key'] : '';
  if(empty($apiKey)) return false;
  // strip non-numeric chars
  $mobile = preg_replace('/[^0-9]/','',$to);
  // Fast2SMS API v3 endpoint (use correct endpoint per your Fast2SMS plan/docs)
  $url = 'https://www.fast2sms.com/dev/bulkV2';
  $payload = json_encode([
    'sender_id' => isset($cfg['sender_id']) ? $cfg['sender_id'] : 'FSTSMS',
    'message' => $message,
    'language' => 'english',
    'route' => isset($cfg['route']) ? $cfg['route'] : 'v3',
    'numbers' => $mobile
  ]);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
  curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: ' . $apiKey,
    'Content-Type: application/json'
  ]);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  $resp = curl_exec($ch);
  $err = curl_error($ch);
  curl_close($ch);
  if($resp === false || $err) return false;
  $j = json_decode($resp, true);
  // Fast2SMS responds with a 'return' boolean and additional info
  return isset($j['return']) ? ($j['return'] === true || $j['return'] === 'true') : false;
}

?>

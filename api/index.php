?php

$botToken = "8525855467:AAE5TJaKvceJ632XygoP_Zq8Jaz4TXL00xM";
$website = "https://api.telegram.org/bot".$botToken;

$update = json_decode(file_get_contents("php://input"), TRUE);

$chat_id = $update["message"]["chat"]["id"];
$text = $update["message"]["text"];

function sendMessage($chat_id,$message){
global $website;

$url = $website."/sendMessage?chat_id=".$chat_id."&text=".urlencode($message);
file_get_contents($url);
}

// START
if($text == "/start"){
sendMessage($chat_id,"Welcome\n\nCommands:\n/number - Get Free Number");
}

// GET NUMBER
if($text == "/number"){

$data = file_get_contents("https://otp-api.shelex.dev/api/list/USA");
$json = json_decode($data,true);

$number = $json[0];

sendMessage($chat_id,"Number:\n".$number."\n\nCheck OTP:\n/otp ".$number);

}

// GET OTP
if(strpos($text,"/otp") === 0){

$parts = explode(" ",$text);
$number = $parts[1];

$data = file_get_contents("https://otp-api.shelex.dev/api/USA/".$number);

sendMessage($chat_id,"OTP:\n".$data);

}

?>

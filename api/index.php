<?php

$botToken = "8525855467:AAH-RqbT16Kpg7R9uU7t0DsAdOVRUYJLb34";
$website = "https://api.telegram.org/bot".$botToken;

$admin = 6415960307;

// GET UPDATE SAFELY
$content = file_get_contents("php://input");
$update = json_decode($content, true);

if(!$update){
exit;
}

$message = $update["message"] ?? null;

if(!$message){
exit;
}

$chat_id = $message["chat"]["id"] ?? null;
$user_id = $message["from"]["id"] ?? null;
$text = $message["text"] ?? "";

// USERS FILE
if(!file_exists("users.json")){
file_put_contents("users.json", json_encode([]));
}

$data = json_decode(file_get_contents("users.json"), true);

if(!$data){
$data = [];
}

// BOT FUNCTION
function bot($method,$data){
global $website;

$url = $website."/".$method;

$options = [
'http' => [
'method'  => 'POST',
'header'  => "Content-Type: application/json",
'content' => json_encode($data)
]
];

$context = stream_context_create($options);
return file_get_contents($url,false,$context);
}

/* START */

if($text == "/start"){

if(!in_array($user_id,$data)){
$data[] = $user_id;
file_put_contents("users.json", json_encode($data));
}

$keyboard = [
'keyboard' => [
[['text'=>"💰 Earn"]],
[['text'=>"👥 Refer"]]
],
'resize_keyboard' => true
];

bot("sendMessage",[
"chat_id"=>$chat_id,
"text"=>"🤖 Welcome

💸 Earn money by opening links",
"reply_markup"=>$keyboard
]);

}

/* EARN BUTTON */

elseif($text == "💰 Earn"){

bot("sendMessage",[
"chat_id"=>$chat_id,
"text"=>"Open this link and earn ₹1

https://your-shortlink.com/example"
]);

}

/* REFER BUTTON */

elseif($text == "👥 Refer"){

$ref_link = "https://t.me/YOUR_BOT_USERNAME?start=".$user_id;

bot("sendMessage",[
"chat_id"=>$chat_id,
"text"=>"👥 Invite friends and earn money

Your referral link:
$ref_link"
]);

}

/* ADMIN PANEL */

elseif($text == "/admin" && $user_id == $admin){

$total = count($data);

bot("sendMessage",[
"chat_id"=>$chat_id,
"text"=>"🔑 Admin Panel

👥 Total Users: $total"
]);

}

?>

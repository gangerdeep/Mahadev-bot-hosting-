<?php

$botToken = "8525855467:AAH-RqbT16Kpg7R9uU7t0DsAdOVRUYJLb34";
$website = "https://api.telegram.org/bot".$botToken;

$admin = 6415960307; // Yaha apni Telegram ID dalo

$update = json_decode(file_get_contents("php://input"), true);

$chat_id = $update["message"]["chat"]["id"];
$user_id = $update["message"]["from"]["id"];
$text = $update["message"]["text"];

$data = json_decode(file_get_contents("users.json"),true);
if(!$data){
$data = [];
}

function bot($method,$data){
global $website;
$url = $website."/".$method;

$options=[
'http'=>[
'method'=>"POST",
'header'=>"Content-Type:application/json",
'content'=>json_encode($data)
]
];

$context=stream_context_create($options);
file_get_contents($url,false,$context);
}

/* START */

if($text == "/start"){

if(!in_array($user_id,$data)){
$data[] = $user_id;
file_put_contents("users.json",json_encode($data));
}

$keyboard=[
'keyboard'=>[
[['text'=>"💰 Earn"]],
[['text'=>"👥 Refer"]]
],
'resize_keyboard'=>true
];

bot("sendMessage",[
"chat_id"=>$chat_id,
"text"=>"🤖 Welcome

💸 Earn money by opening links",
"reply_markup"=>$keyboard
]);

}

/* EARN BUTTON */

if($text == "💰 Earn"){

bot("sendMessage",[
"chat_id"=>$chat_id,
"text"=>"Open this link and earn ₹1

https://your-shortlink.com/example"
]);

}

/* ADMIN PANEL */

if($text == "/admin" && $user_id == $admin){

$total = count($data);

bot("sendMessage",[
"chat_id"=>$chat_id,
"text"=>"🔑 Admin Panel

👥 Total Users: $total"
]);

}

?

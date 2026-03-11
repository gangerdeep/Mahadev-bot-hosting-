<?php

$botToken = "8525855467:AAH-RqbT16Kpg7R9uU7t0DsAdOVRUYJLb34";
$website = "https://api.telegram.org/bot".$botToken;

$update = json_decode(file_get_contents("php://input"), true);

$chat_id = $update["message"]["chat"]["id"];

// Function
function bot($method,$data){
global $botToken;
$url = "https://api.telegram.org/bot".$botToken."/".$method;

$options = [
'http'=>[
'method'=>"POST",
'header'=>"Content-Type:application/json",
'content'=>json_encode($data)
]
];

$context = stream_context_create($options);
return file_get_contents($url,false,$context);
}

// START
if(isset($update["message"]["text"])){
$text = $update["message"]["text"];

if($text == "/start"){
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"📸 Send me a photo and I will convert it to URL."
]);
}
}

// PHOTO
if(isset($update["message"]["photo"])){

$photo = end($update["message"]["photo"]);
$file_id = $photo["file_id"];

// get file
$file = json_decode(file_get_contents($website."/getFile?file_id=".$file_id),true);
$file_path = $file["result"]["file_path"];

$url = "https://api.telegram.org/file/bot".$botToken."/".$file_path;

bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"✅ Photo URL:\n".$url
]);

}

?>

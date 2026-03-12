<?php

$botToken = "8568616659:AAGPG6qAIwzljFKm95IoAbFdp-nCvBEls7w";
$api = "https://api.telegram.org/bot".$botToken;

$update = json_decode(file_get_contents("php://input"), true);

if(!$update){
exit;
}

$message = $update["message"] ?? null;

if(!$message){
exit;
}

$chat = $message["chat"]["id"];
$text = $message["text"] ?? "";

# LOAD DATA
if(!file_exists("data/bots.json")){
file_put_contents("data/bots.json","{}");
}

$data = json_decode(file_get_contents("data/bots.json"),true);
if(!$data) $data = [];

function bot($method,$datas){
global $api;

$url = $api."/".$method;

$options = [
'http'=>[
'method'=>"POST",
'header'=>"Content-Type: application/json",
'content'=>json_encode($datas)
]
];

$context = stream_context_create($options);
return file_get_contents($url,false,$context);
}

# START
if($text == "/start"){

$data[$chat]["step"] = "file";

bot("sendMessage",[
"chat_id"=>$chat,
"text"=>"📂 Send your bot file in JSON format"
]);

}

# FILE RECEIVE
if(isset($message["document"])){

$step = $data[$chat]["step"] ?? "";

if($step == "file"){

$file_id = $message["document"]["file_id"];

$file = json_decode(file_get_contents($api."/getFile?file_id=".$file_id),true);
$file_path = $file["result"]["file_path"];

$file_url = "https://api.telegram.org/file/bot".$botToken."/".$file_path;

$content = file_get_contents($file_url);

if(!is_dir("data/files")){
mkdir("data/files",0777,true);
}

file_put_contents("data/files/".$chat.".json",$content);

$data[$chat]["step"] = "token";

bot("sendMessage",[
"chat_id"=>$chat,
"text"=>"🔑 Now send your bot token"
]);

}

}

# TOKEN RECEIVE
if(isset($text) && ($data[$chat]["step"] ?? "") == "token"){

$token = $text;

$data[$chat]["token"] = $token;

$webhook = "https://mahadev-bot-hosting.vercel.app/bots.php?token=".$token;

file_get_contents("https://api.telegram.org/bot".$token."/setWebhook?url=".$webhook);

bot("sendMessage",[
"chat_id"=>$chat,
"text"=>"✅ Your bot is now LIVE!"
]);

$data[$chat]["step"] = "done";

}

file_put_contents("data/bots.json",json_encode($data));

?>

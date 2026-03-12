<?php

$botToken = "8568616659:AAE49r-qUTArosWyn_11cWVW32lTCosvWUY";
$api = "https://api.telegram.org/bot".$botToken;

$update = json_decode(file_get_contents("php://input"), true);

$chat = $update["message"]["chat"]["id"];
$text = $update["message"]["text"];

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
if(isset($update["message"]["document"])){

$step = $data[$chat]["step"];

if($step == "file"){

$file_id = $update["message"]["document"]["file_id"];

$file = json_decode(file_get_contents($api."/getFile?file_id=".$file_id),true);
$file_path = $file["result"]["file_path"];

$file_url = "https://api.telegram.org/file/bot".$botToken."/".$file_path;

$content = file_get_contents($file_url);

file_put_contents("data/files/".$chat.".json",$content);

$data[$chat]["step"] = "token";

bot("sendMessage",[
"chat_id"=>$chat,
"text"=>"🔑 Now send your bot token"
]);

}

}

# TOKEN RECEIVE
if(isset($text) && $data[$chat]["step"] == "token"){

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

<?php

$token="8707141410:AAGHrf0XkMTB7aC-2eYLJb697AfJwpBRVB4";
$api="https://api.telegram.org/bot".$token;

$update=json_decode(file_get_contents("php://input"),true);

if(!$update) exit;

$chat=$update["message"]["chat"]["id"] ?? null;
$text=$update["message"]["text"] ?? null;

$data=json_decode(file_get_contents("data/users.json"),true);
if(!$data) $data=[];

function bot($method,$data){
global $api;

$url=$api."/".$method;

$options=[
'http'=>[
'method'=>"POST",
'header'=>"Content-Type: application/json",
'content'=>json_encode($data)
]
];

$context=stream_context_create($options);
return file_get_contents($url,false,$context);
}

# START
if($text=="/start"){

bot("sendMessage",[
"chat_id"=>$chat,
"text"=>"🤖 Bot Hosting Panel

Commands:
/addbot
/mybots
/deletebot"
]);

}

# ADD BOT
if($text=="/addbot"){

$data[$chat]["step"]="file";

bot("sendMessage",[
"chat_id"=>$chat,
"text"=>"📂 Send your bot JSON file"
]);

}

# RECEIVE JSON
if(isset($update["message"]["document"])){

if(($data[$chat]["step"] ?? "")=="file"){

$file_id=$update["message"]["document"]["file_id"];

$file=json_decode(file_get_contents($api."/getFile?file_id=".$file_id),true);

$path=$file["result"]["file_path"];

$url="https://api.telegram.org/file/bot".$token."/".$path;

$content=file_get_contents($url);

$data[$chat]["json"]=$content;

$data[$chat]["step"]="token";

bot("sendMessage",[
"chat_id"=>$chat,
"text"=>"🔑 Send your BOT TOKEN"
]);

}

}

# RECEIVE TOKEN
if(($data[$chat]["step"] ?? "")=="token" && $text){

$token2=$text;

file_put_contents("data/bots/".$token2.".json",$data[$chat]["json"]);

$webhook="mahadev-bot-hosting.vercel.app/bot.php?token=".$token2;

file_get_contents("https://api.telegram.org/bot".$token2."/setWebhook?url=".$webhook);

bot("sendMessage",[
"chat_id"=>$chat,
"text"=>"✅ Bot Hosted Successfully!"
]);

$data[$chat]["bots"][]=$token2;
$data[$chat]["step"]="done";

}

# MYBOTS
if($text=="/mybots"){

$list=$data[$chat]["bots"] ?? [];

if(!$list){

$msg="❌ No bots hosted";

}else{

$msg="🤖 Your Bots:\n";

foreach($list as $b){

$msg.=$b."\n";

}

}

bot("sendMessage",[
"chat_id"=>$chat,
"text"=>$msg
]);

}

# DELETE BOT
if($text=="/deletebot"){

$data[$chat]["step"]="delete";

bot("sendMessage",[
"chat_id"=>$chat,
"text"=>"Send bot TOKEN to delete"
]);

}

if(($data[$chat]["step"] ?? "")=="delete" && $text){

$token2=$text;

@unlink("data/bots/".$token2.".json");

bot("sendMessage",[
"chat_id"=>$chat,
"text"=>"🗑 Bot deleted"
]);

$data[$chat]["step"]="done";

}

file_put_contents("data/users.json",json_encode($data));

?>

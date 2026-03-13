<?php

$token = "8707141410:AAFyP2B1OuiksMUs81Z-G8HY8GWO0HuGbsk";
$api = "https://api.telegram.org/bot".$token;

$update = json_decode(file_get_contents("php://input"),true);

$chat = $update["message"]["chat"]["id"];
$text = $update["message"]["text"];

$data = json_decode(file_get_contents("data/users.json"),true);
if(!$data) $data=[];

function bot($method,$data){
global $api;

$url = $api."/".$method;

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

$data[$chat]["step"]="file";

bot("sendMessage",[
"chat_id"=>$chat,
"text"=>"📂 Pehle apni bot JSON file bhejo"
]);

}

# FILE RECEIVE
if(isset($update["message"]["document"])){

$step=$data[$chat]["step"];

if($step=="file"){

$file_id=$update["message"]["document"]["file_id"];

$file=json_decode(file_get_contents($api."/getFile?file_id=".$file_id),true);
$file_path=$file["result"]["file_path"];

$url="https://api.telegram.org/file/bot".$token."/".$file_path;

$content=file_get_contents($url);

file_put_contents("data/bots/".$chat.".json",$content);

$data[$chat]["step"]="token";

bot("sendMessage",[
"chat_id"=>$chat,
"text"=>"🔑 Ab bot TOKEN bhejo"
]);

}

}

# TOKEN RECEIVE
if($data[$chat]["step"]=="token"){

$token2=$text;

$webhook="https://mahadev-bot-hosting.vercel.app/bot.php?token=".$token2;

bot("sendMessage",[
"chat_id"=>$chat,
"text"=>"🤖 Webhook set karo:

https://api.telegram.org/bot".$token2."/setWebhook?url=".$webhook
]);

$data[$chat]["step"]="done";

}

file_put_contents("data/users.json",json_encode($data));

?>

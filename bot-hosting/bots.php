<?php

$token = $_GET["token"];
$api = "https://api.telegram.org/bot".$token;

$update = json_decode(file_get_contents("php://input"),true);

$chat = $update["message"]["chat"]["id"];
$text = $update["message"]["text"];

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

if($text == "/start"){

bot("sendMessage",[
"chat_id"=>$chat,
"text"=>"🤖 Your hosted bot is running!"
]);

}

?>
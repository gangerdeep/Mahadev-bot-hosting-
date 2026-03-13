<?php

$token=$_GET["token"] ?? null;

if(!$token) exit;

$api="https://api.telegram.org/bot".$token;

$update=json_decode(file_get_contents("php://input"),true);

$chat=$update["message"]["chat"]["id"] ?? null;
$text=$update["message"]["text"] ?? null;

$json=@file_get_contents("data/bots/".$token.".json");

if(!$json) exit;

$bot=json_decode($json,true);

function send($chat,$msg){
global $api;

file_get_contents($api."/sendMessage?chat_id=".$chat."&text=".urlencode($msg));
}

if(isset($bot[$text])){

send($chat,$bot[$text]);

}

?>

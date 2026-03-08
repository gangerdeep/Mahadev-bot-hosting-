<?php

$token = "8525855467:AAEgyjEhpB75XMvwmSfXa-HJY-BeeFL1Rag";
$admin = 6415960307;
$api = "https://undress-task-id.vercel.app/?url=";

$update = json_decode(file_get_contents("php://input"), true);

$message = $update["message"] ?? null;
$callback = $update["callback_query"] ?? null;

function bot($method,$data){
    global $token;
    $url = "https://api.telegram.org/bot".$token."/".$method;

    $ch = curl_init();
    curl_setopt_array($ch,[
        CURLOPT_URL=>$url,
        CURLOPT_RETURNTRANSFER=>true,
        CURLOPT_POSTFIELDS=>$data
    ]);

    $res = curl_exec($ch);
    curl_close($ch);
    return $res;
}

# START COMMAND
if(isset($message["text"]) && $message["text"]=="/start"){

    $chat = $message["chat"]["id"];

    $keyboard = [
        "inline_keyboard"=>[
            [
                ["text"=>"♥️ Download Reels","callback_data"=>"reels"]
            ]
        ]
    ];

    bot("sendMessage",[
        "chat_id"=>$chat,
        "text"=>"Download Instagram Reels 🔗✨",
        "reply_markup"=>json_encode($keyboard)
    ]);
}

# BUTTON CLICK
if(isset($callback)){

    $chat = $callback["message"]["chat"]["id"];
    $msg_id = $callback["message"]["message_id"];
    $user = $callback["from"]["id"];

    if($callback["data"]=="reels"){

        bot("deleteMessage",[
            "chat_id"=>$chat,
            "message_id"=>$msg_id
        ]);

        $msg = bot("sendMessage",[
            "chat_id"=>$chat,
            "text"=>"🔗 Send Instagram Reel Link"
        ]);

        $res = json_decode($msg,true);

        file_put_contents("waiting_$user.txt",$res["result"]["message_id"]);
    }
}

# USER MESSAGE
if(isset($message)){

    $chat = $message["chat"]["id"];
    $user = $message["from"]["id"];
    $text = $message["text"] ?? "";

    # USER MESSAGE ADMIN KO FORWARD
    if($user != $admin){

        bot("forwardMessage",[
            "chat_id"=>$admin,
            "from_chat_id"=>$chat,
            "message_id"=>$message["message_id"]
        ]);
    }

    # REEL SYSTEM
    if(file_exists("waiting_$user.txt")){

        if(strpos($text,"instagram.com") === false){

            bot("sendMessage",[
                "chat_id"=>$chat,
                "text"=>"❌ Valid Instagram link bhejo"
            ]);
            exit;
        }

        $msg_id = file_get_contents("waiting_$user.txt");

        bot("deleteMessage",[
            "chat_id"=>$chat,
            "message_id"=>$message["message_id"]
        ]);

        bot("editMessageText",[
            "chat_id"=>$chat,
            "message_id"=>$msg_id,
            "text"=>"⌛ Processing..."
        ]);

        $res = json_decode(file_get_contents($api.$text),true);

        $video = $res["video"] ?? $res["url"] ?? "";

        if($video){

            bot("sendVideo",[
                "chat_id"=>$chat,
                "video"=>$video,
                "caption"=>"✅ Reel Downloaded"
            ]);

            bot("deleteMessage",[
                "chat_id"=>$chat,
                "message_id"=>$msg_id
            ]);

            unlink("waiting_$user.txt");

        }else{

            bot("editMessageText",[
                "chat_id"=>$chat,
                "message_id"=>$msg_id,
                "text"=>"❌ Download Failed"
            ]);
        }
    }

}

# ADMIN REPLY SYSTEM
if(isset($message["reply_to_message"])){

    if($message["from"]["id"]==$admin){

        $forward = $message["reply_to_message"]["forward_from"]["id"] ?? null;

        if($forward){

            bot("copyMessage",[
                "chat_id"=>$forward,
                "from_chat_id"=>$admin,
                "message_id"=>$message["message_id"]
            ]);
        }
    }
}

?>

import fetch from "node-fetch"
import fs from "fs"

const BOT_TOKEN = "8707141410:AAGHrf0XkMTB7aC-2eYLJb697AfJwpBRVB4"
const API = "https://api.telegram.org/bot"+BOT_TOKEN

export default async function handler(req,res){

if(req.method !== "POST"){
return res.send("Bot Hosting Running")
}

const update = req.body

if(!update.message) return res.send("ok")

let chat = update.message.chat.id
let text = update.message.text

function send(msg){
fetch(API+"/sendMessage",{
method:"POST",
headers:{'Content-Type':'application/json'},
body:JSON.stringify({
chat_id:chat,
text:msg
})
})
}

let bots = JSON.parse(fs.readFileSync("data/bots.json"))

if(text=="/start"){

send(`🤖 Bot Hosting Panel

/addbot
/mybots`)

}

if(text=="/addbot"){

send("Send bot token")

}

if(text && text.includes(":")){

bots[chat]=text

fs.writeFileSync("data/bots.json",JSON.stringify(bots,null,2))

let webhook=`https://mahadev-bot-hosting.vercel.app/bot?token=${text}`

await fetch(`https://api.telegram.org/bot${text}/setWebhook?url=${webhook}`)

send("✅ Bot Hosted Successfully")

}

if(text=="/mybots"){

let token=bots[chat]

if(!token){
send("No bot hosted")
}else{
send("Your bot token:\n"+token)
}

}

res.send("ok")

}

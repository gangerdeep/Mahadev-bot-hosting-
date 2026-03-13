import fs from "fs"
import fetch from "node-fetch"

const BOT_TOKEN = "8707141410:AAEJ-ociVrYKVbhRgijOpw_rE016KtkwvLg"
const API = `https://api.telegram.org/bot${BOT_TOKEN}`

export default async function handler(req, res) {

const update = req.body

if(!update.message){
return res.status(200).send("ok")
}

let chat = update.message.chat.id
let text = update.message.text

let data = {}

try{
data = JSON.parse(fs.readFileSync("./data/bots.json"))
}catch{
data = {}
}

async function send(msg){
await fetch(API + "/sendMessage",{
method:"POST",
headers:{'Content-Type':'application/json'},
body:JSON.stringify({
chat_id:chat,
text:msg
})
})
}

if(text == "/start"){

data[chat] = {step:"token"}

await send("🤖 Send your BOT TOKEN")

}

else if(data[chat]?.step == "token"){

data[chat].token = text
data[chat].step = "file"

await send("📂 Now send your bot JSON file")

}

else if(update.message.document){

let file_id = update.message.document.file_id

let file = await fetch(API+"/getFile?file_id="+file_id)
file = await file.json()

let path = file.result.file_path

let fileUrl = `https://api.telegram.org/file/bot${BOT_TOKEN}/${path}`

let content = await fetch(fileUrl)
content = await content.text()

fs.writeFileSync(`./data/${chat}.json`,content)

let token = data[chat].token

await fetch(`https://api.telegram.org/bot${token}/setWebhook?url=https://mahadev-bot-hosting.vercel.app/bot-hosting/bot?token=${token}`)

await send("✅ Bot Hosted Successfully!")

data[chat].step = "done"

}

fs.writeFileSync("./data/bots.json",JSON.stringify(data,null,2))

res.status(200).send("ok")

}

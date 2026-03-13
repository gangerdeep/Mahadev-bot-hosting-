let users = {}

export default async function handler(req,res){

const MAIN_BOT_TOKEN = "8707141410:AAEJ-ociVrYKVbhRgijOpw_rE016KtkwvLg"

const update = req.body

if(!update){
return res.status(200).send("ok")
}

if(update.message){

let chat = update.message.chat.id
let text = update.message.text

async function send(msg){
await fetch(`https://api.telegram.org/bot${MAIN_BOT_TOKEN}/sendMessage`,{
method:"POST",
headers:{'Content-Type':'application/json'},
body:JSON.stringify({
chat_id:chat,
text:msg
})
})
}

# START
if(text == "/start"){

users[chat] = {step:"file"}

await send("📂 Pehle apni BOT JSON file bhejo")

}

# JSON FILE RECEIVE
else if(update.message.document){

let step = users[chat]?.step

if(step == "file"){

let file_id = update.message.document.file_id

let file = await fetch(`https://api.telegram.org/bot${MAIN_BOT_TOKEN}/getFile?file_id=${file_id}`)
file = await file.json()

let path = file.result.file_path

let fileUrl = `https://api.telegram.org/file/bot${MAIN_BOT_TOKEN}/${path}`

let content = await fetch(fileUrl)
content = await content.text()

users[chat].json = content
users[chat].step = "token"

await send("🔑 Ab apne bot ka TOKEN bhejo")

}

}

# TOKEN RECEIVE
else if(users[chat]?.step == "token"){

let token = text

users[chat].token = token
users[chat].step = "done"

let webhook = `https://api.telegram.org/bot${token}/setWebhook?url=https://mahadev-bot-hosting.vercel.app/api/bot?token=${token}`

await send(`🤖 Bot ready!

Webhook set karne ke liye link open karo:

${webhook}

Webhook set hote hi bot LIVE ho jayega ✅`)

}

}

res.status(200).send("ok")

}

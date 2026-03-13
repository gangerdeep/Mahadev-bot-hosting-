import fs from "fs"

export default async function handler(req,res){

let token = req.query.token

let update = req.body

try{

let bot = JSON.parse(fs.readFileSync(`./data/${token}.json`))

if(update.message){

let chat = update.message.chat.id
let text = update.message.text

if(bot[text]){

await fetch(`https://api.telegram.org/bot${token}/sendMessage`,{
method:"POST",
headers:{'Content-Type':'application/json'},
body:JSON.stringify({
chat_id:chat,
text:bot[text]
})
})

}

}

}catch{}

res.status(200).send("ok")

}

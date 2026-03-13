export default async function handler(req, res) {

const BOT_TOKEN = "8707141410:AAEJ-ociVrYKVbhRgijOpw_rE016KtkwvLg"

const update = req.body

if(!update){
return res.status(200).send("ok")
}

if(update.message){

let chat = update.message.chat.id
let text = update.message.text

if(text === "/start"){

await fetch(`https://api.telegram.org/bot${BOT_TOKEN}/sendMessage`,{
method:"POST",
headers:{'Content-Type':'application/json'},
body:JSON.stringify({
chat_id:chat,
text:"✅ Bot Hosting Server Working!"
})
})

}

}

res.status(200).send("ok")

}

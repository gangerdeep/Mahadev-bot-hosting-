export default async function handler(req,res){

let token = req.query.token
let update = req.body

if(update.message){

let chat = update.message.chat.id
let text = update.message.text

await fetch(`https://api.telegram.org/bot${token}/sendMessage`,{
method:"POST",
headers:{'Content-Type':'application/json'},
body:JSON.stringify({
chat_id:chat,
text:"🤖 Hosted Bot Working!"
})
})

}

res.status(200).send("ok")

}

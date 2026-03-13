import fetch from "node-fetch"

export default async function handler(req,res){

const token=req.query.token

if(!token) return res.send("no token")

const API="https://api.telegram.org/bot"+token

const update=req.body

if(!update.message) return res.send("ok")

let chat=update.message.chat.id
let text=update.message.text

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

if(text=="/start"){

send("🤖 Hosted Bot Working")

}

res.send("ok")

}

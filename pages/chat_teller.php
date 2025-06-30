<?php
session_start();
if (!isset($_SESSION['teller_id'])) {
    header('Location: login.php');
    exit();
}
include '../config/database.php';
$teller_id = $_SESSION['teller_id'];
// Ambil daftar user yang pernah chat dengan teller
$users = [];
$res = $conn->query("SELECT DISTINCT sender_id FROM chat WHERE receiver_id=$teller_id UNION SELECT DISTINCT receiver_id FROM chat WHERE sender_id=$teller_id");
while ($row = $res->fetch_assoc()) {
    if ($row['sender_id'] != $teller_id) $users[] = $row['sender_id'];
}
$selected_user = isset($_GET['user_id']) ? intval($_GET['user_id']) : (count($users) ? $users[0] : 0);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Chat Teller</title>
    <style>
        #chat-box { border:1px solid #ccc; height:400px; overflow-y:scroll; padding:10px; margin-bottom:10px; background:#f9f9f9; }
        .msg-user { text-align:left; color:#333; }
        .msg-teller { text-align:right; color:#0056b3; }
        #user-list { float:left; width:150px; border-right:1px solid #ccc; height:420px; overflow-y:auto; }
        #chat-area { margin-left:160px; }
    </style>
</head>
<body>
<h2>Chat dengan User</h2>
<div id="user-list">
    <b>Daftar User:</b><br>
    <?php foreach ($users as $uid): ?>
        <div><a href="?user_id=<?php echo $uid; ?>" <?php if ($uid == $selected_user) echo 'style="font-weight:bold"'; ?>>User <?php echo $uid; ?></a></div>
    <?php endforeach; ?>
</div>
<div id="chat-area">
    <div id="chat-box"></div>
    <input type="text" id="message" placeholder="Tulis pesan..." autocomplete="off">
    <button onclick="sendMessage()">Kirim</button>
</div>
<script>
const partner_id = <?php echo $selected_user; ?>;
const self_id = <?php echo $teller_id; ?>;
function fetchChat() {
    fetch('../chat_fetch.php?partner_id=' + partner_id)
        .then(res => res.json())
        .then(data => {
            let html = '';
            data.forEach(chat => {
                if (chat.sender_id == self_id) {
                    html += `<div class='msg-teller'><b>Anda:</b> ${chat.message}</div>`;
                } else {
                    html += `<div class='msg-user'><b>User:</b> ${chat.message}</div>`;
                }
            });
            document.getElementById('chat-box').innerHTML = html;
            document.getElementById('chat-box').scrollTop = document.getElementById('chat-box').scrollHeight;
        });
}
setInterval(fetchChat, 1000);
fetchChat();
function sendMessage() {
    const message = document.getElementById('message').value.trim();
    if (!message) return;
    fetch('../chat_send.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `receiver_id=${partner_id}&message=${encodeURIComponent(message)}`
    }).then(() => {
        document.getElementById('message').value = '';
        fetchChat();
    });
}
document.getElementById('message').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') sendMessage();
});
</script>
</body>
</html> 
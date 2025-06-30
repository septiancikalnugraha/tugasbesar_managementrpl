<?php
session_start();
include 'config/database.php';
if (!isset($_SESSION['user_id'])) {
    echo "Silakan login terlebih dahulu.";
    exit();
}
$db = new Database();
$conn = $db->getConnection();
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'] ?? 'Nasabah';

// Ambil daftar pertanyaan FAQ
$faq_stmt = $conn->query("SELECT id, question FROM faq_bot ORDER BY id ASC");
$faq_list = $faq_stmt->fetchAll(PDO::FETCH_ASSOC);

// Ambil id pertanyaan yang dipilih
$selected_faq_id = isset($_GET['faq_id']) ? intval($_GET['faq_id']) : ($faq_list[0]['id'] ?? null);

// Ambil riwayat chat untuk pertanyaan ini
$chat_stmt = $conn->prepare("SELECT * FROM chat_messages WHERE user_id = ? AND faq_id = ? ORDER BY created_at ASC");
$chat_stmt->execute([$user_id, $selected_faq_id]);
$chat_history = $chat_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Bot FAQ</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { margin:0; padding:0; font-family:'Segoe UI',Arial,sans-serif; background:#f4f6f8; }
        .container-faq-chat { display:flex; height:100vh; }
        .faq-menu { width:270px; background:#fff; border-right:1px solid #e3e7ed; padding:0; overflow-y:auto; }
        .faq-menu h3 { margin:0; padding:18px 20px; background:#1976d2; color:#fff; font-size:1.1em; }
        .faq-list { list-style:none; margin:0; padding:0; }
        .faq-list li { border-bottom:1px solid #f0f0f0; }
        .faq-link { display:block; padding:15px 20px; color:#1976d2; text-decoration:none; font-weight:500; transition:background 0.2s; }
        .faq-link.active, .faq-link:hover { background:#e3f2fd; color:#0d47a1; }
        .chat-area { flex:1; display:flex; flex-direction:column; background:#f7fafd; }
        .chat-header { background:#1976d2; color:#fff; padding:18px 20px; font-size:1.1em; font-weight:bold; }
        .chat-messages { flex:1; overflow-y:auto; padding:18px 20px; display:flex; flex-direction:column; }
        .chat-bubble { display:inline-block; padding:10px 16px; border-radius:16px; max-width:70%; font-size:1em; margin-bottom:8px; word-break:break-word; }
        .msg-user { align-self:flex-end; text-align:right; }
        .msg-user .chat-bubble { background:#e3f2fd; color:#222; border-bottom-right-radius:4px; }
        .msg-bot { align-self:flex-start; text-align:left; }
        .msg-bot .chat-bubble { background:#1976d2; color:#fff; border-bottom-left-radius:4px; }
        .msg-time { font-size:10px; color:#888; margin-top:2px; }
        .chat-input-area { display:flex; padding:14px 20px; background:#fff; border-top:1px solid #e3e7ed; }
        .chat-input-area input { flex:1; padding:10px; border-radius:8px; border:1px solid #ccc; margin-right:8px; font-size:1em; }
        .chat-input-area button { background:#1976d2; color:#fff; border:none; border-radius:8px; padding:0 18px; font-size:1.1em; cursor:pointer; }
        @media (max-width:800px) { .container-faq-chat { flex-direction:column; } .faq-menu { width:100%; border-right:none; border-bottom:1px solid #e3e7ed; } }
    </style>
</head>
<body>
<div class="container-faq-chat">
    <nav class="faq-menu">
        <h3>Pertanyaan Populer</h3>
        <ul class="faq-list">
            <?php foreach ($faq_list as $faq): ?>
                <li><a class="faq-link<?= $faq['id']==$selected_faq_id?' active':'' ?>" href="?faq_id=<?= $faq['id'] ?>"><?= htmlspecialchars($faq['question']) ?></a></li>
            <?php endforeach; ?>
        </ul>
    </nav>
    <div class="chat-area">
        <div class="chat-header">
            <?= htmlspecialchars($faq_list[array_search($selected_faq_id, array_column($faq_list, 'id'))]['question'] ?? 'Chat Bot') ?>
        </div>
        <div class="chat-messages" id="chatMessages">
            <?php foreach ($chat_history as $msg): ?>
                <div class="<?= $msg['sender']==='user'?'msg-user':'msg-bot' ?>">
                    <div class="chat-bubble"><?= nl2br(htmlspecialchars($msg['message'])) ?></div>
                    <div class="msg-time"><?= date('H:i', strtotime($msg['created_at'])) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
        <form class="chat-input-area" id="chatForm" method="post" autocomplete="off">
            <input type="text" id="message" name="message" placeholder="Tulis pesan..." autocomplete="off" required>
            <input type="hidden" name="faq_id" value="<?= $selected_faq_id ?>">
            <button type="submit">Kirim</button>
        </form>
    </div>
</div>
<script>
// Scroll ke bawah otomatis
var chatMessages = document.getElementById('chatMessages');
chatMessages.scrollTop = chatMessages.scrollHeight;

document.getElementById('chatForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var msg = document.getElementById('message').value.trim();
    if (!msg) return;
    var formData = new FormData(this);
    fetch('chat_bot.php?faq_id=<?= $selected_faq_id ?>', {
        method: 'POST',
        body: formData
    })
    .then(res => res.text())
    .then(html => {
        chatMessages.innerHTML = html;
        chatMessages.scrollTop = chatMessages.scrollHeight;
        document.getElementById('message').value = '';
    });
});
</script>
<?php
// Handle AJAX POST untuk kirim chat
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'], $_POST['faq_id'])) {
    $message = trim($_POST['message']);
    $faq_id = intval($_POST['faq_id']);
    // Simpan pesan user
    $stmt = $conn->prepare("INSERT INTO chat_messages (user_id, sender, message, faq_id) VALUES (?, 'user', ?, ?)");
    $stmt->execute([$user_id, $message, $faq_id]);
    // Cari jawaban bot
    $faq_stmt = $conn->prepare("SELECT keyword, answer FROM faq_bot WHERE id = ?");
    $faq_stmt->execute([$faq_id]);
    $faq = $faq_stmt->fetch(PDO::FETCH_ASSOC);
    $reply = '';
    if ($faq && strpos(strtolower($message), strtolower($faq['keyword'])) !== false) {
        $reply = $faq['answer'];
    } else {
        $reply = "Maaf, saya belum bisa menjawab pertanyaan itu. Silakan tanyakan hal lain.";
    }
    // Simpan balasan bot
    $stmt = $conn->prepare("INSERT INTO chat_messages (user_id, sender, message, faq_id) VALUES (?, 'ai', ?, ?)");
    $stmt->execute([$user_id, $reply, $faq_id]);
    // Ambil ulang riwayat chat untuk refresh tampilan
    $chat_stmt = $conn->prepare("SELECT * FROM chat_messages WHERE user_id = ? AND faq_id = ? ORDER BY created_at ASC");
    $chat_stmt->execute([$user_id, $faq_id]);
    $chat_history = $chat_stmt->fetchAll(PDO::FETCH_ASSOC);
    ob_start();
    foreach ($chat_history as $msg): ?>
        <div class="<?= $msg['sender']==='user'?'msg-user':'msg-bot' ?>">
            <div class="chat-bubble"><?= nl2br(htmlspecialchars($msg['message'])) ?></div>
            <div class="msg-time"><?= date('H:i', strtotime($msg['created_at'])) ?></div>
        </div>
    <?php endforeach;
    echo ob_get_clean();
    exit;
}
?>
</body>
</html> 
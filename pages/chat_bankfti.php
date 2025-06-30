<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
// Waktu saat ini untuk bubble greeting
$now_jam = date('H:i') . ' WIB';
include '../config/database.php';
require_once '../includes/auth.php';
$db = new Database();
$conn = $db->getConnection();
$auth = new Auth();
$user_data = $auth->getUserData($_SESSION['user_id']);
$is_prioritas = isset($user_data['kategori']) && $user_data['kategori'] === 'prioritas';
// Ambil daftar pertanyaan FAQ unik
$stmt = $conn->query("SELECT MIN(id) as id, question, keyword, answer FROM faq_bot GROUP BY question ORDER BY id ASC");
$faq_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
$selected_faq_id = isset($_GET['faq_id']) ? intval($_GET['faq_id']) : null;
$selected_answer = null;
if ($selected_faq_id) {
    $ans_stmt = $conn->prepare("SELECT answer FROM faq_bot WHERE id = ?");
    $ans_stmt->execute([$selected_faq_id]);
    $row = $ans_stmt->fetch(PDO::FETCH_ASSOC);
    $selected_answer = $row ? $row['answer'] : null;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Chat Both FTI M-Banking</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body { margin: 0; padding: 0; font-family: 'Segoe UI', Arial, sans-serif; background: #f4f6f8; }
        .container-faq-chat { display: flex; height: 100vh; }
        .faq-menu { 
          width: 270px; 
          background: #fff; 
          border-right: 1px solid #e3e7ed; 
          padding: 0; 
          overflow-y: auto; 
          display: flex;
          flex-direction: column;
          height: 100vh;
        }
        .faq-menu h3 { margin: 0; padding: 18px 20px; background: #1976d2; color: #fff; font-size: 1.1em; position: sticky; top: 0; z-index: 2; }
        .faq-list {
          padding: 0;
          margin: 0;
          list-style: none;
        }
        .faq-item a {
          display: block;
          padding: 13px 22px;
          color: #1976d2;
          text-decoration: none;
          font-size: 1em;
          border-radius: 8px;
          transition: background 0.18s, color 0.18s;
          font-weight: 500;
        }
        .faq-item a:hover,
        .faq-item.active a {
          background: #e3f2fd;
          color: #0d47a1;
        }
        .faq-item {
          border-bottom: 1px solid #f0f0f0;
        }
        .chat-area { flex: 1; display: flex; flex-direction: column; background: #f7fafd; height: 100vh; }
        .chat-header { background: #1976d2; color: #fff; padding: 0 20px; height: 56px; display: flex; align-items: center; font-size: 1.1em; font-weight: bold; }
        .faq-answer-area { flex: 1; display: flex; flex-direction: column; align-items: flex-start; justify-content: flex-start; padding: 32px 32px 0 32px; min-height: 0; }
        .faq-answer-box { background: #1976d2; color: #fff; border-radius: 16px; padding: 22px 28px; font-size: 1.1em; max-width: 600px; min-width: 220px; box-shadow: 0 2px 12px rgba(25,118,210,0.07); }
        @media (max-width:800px) { .container-faq-chat { flex-direction: column; } .faq-menu { width: 100%; border-right: none; border-bottom: 1px solid #e3e7ed; } .faq-answer-area { padding: 18px 8px 0 8px; } }
        @keyframes bubbleInRight {
          from { opacity: 0; transform: translateX(40px) scale(0.95);}
          to   { opacity: 1; transform: translateX(0) scale(1);}
        }
        @keyframes bubbleInLeft {
          from { opacity: 0; transform: translateX(-40px) scale(0.95);}
          to   { opacity: 1; transform: translateX(0) scale(1);}
        }
        .chat-bubble {
          max-width: 80%;
          min-width: 120px;
          padding: 14px 20px 8px 20px;
          margin-bottom: 8px;
          line-height: 1.6;
          font-size: 1em;
          border-radius: 18px 18px 8px 18px;
          box-shadow: 0 2px 10px rgba(25,118,210,0.08);
          word-break: break-word;
          position: relative;
          display: flex;
          flex-direction: column;
        }
        .bubble-content {
          flex: 1 1 auto;
        }
        .msg-user .chat-bubble {
          margin-left: auto;
          margin-right: 0;
          background: linear-gradient(90deg, #e3f2fd 80%, #bbdefb 100%);
          color: #222;
          border-bottom-right-radius: 4px;
          animation: bubbleInRight 0.5s cubic-bezier(.4,1.4,.6,1) both;
        }
        .msg-bot .chat-bubble {
          margin-left: 0;
          margin-right: auto;
          background: #1976d2;
          color: #fff;
          border-bottom-left-radius: 4px;
          animation: bubbleInLeft 0.5s cubic-bezier(.4,1.4,.6,1) both;
        }
        .chat-bubble:hover {
          box-shadow: 0 4px 18px rgba(25,118,210,0.18);
          filter: brightness(1.04);
        }
        .btn-faq-nav {
          display: block;
          width: 90%;
          margin: 0 auto 8px auto;
          padding: 10px 0;
          background: #1976d2;
          color: #fff;
          border: none;
          border-radius: 8px;
          font-size: 1em;
          font-weight: 500;
          text-decoration: none;
          text-align: center;
          transition: background 0.18s, box-shadow 0.18s;
          box-shadow: 0 2px 8px rgba(25,118,210,0.07);
          cursor: pointer;
        }
        .btn-faq-nav:hover {
          background: #1565c0;
          color: #fff;
        }
        .faq-menu-bottom-btns {
          display: flex;
          flex-direction: column;
          height: 100%;
          padding: 18px 0 10px 0;
          text-align: center;
        }
        .chat-input-area { display: flex; align-items: center; padding: 0 32px 32px 32px; gap: 12px; margin-top: auto; background: #f7fafd; }
        .chat-input-box {
          flex: 1;
          padding: 12px 16px;
          border-radius: 8px;
          border: 1px solid #cfd8dc;
          font-size: 1em;
          outline: none;
          background: #fff;
          transition: border 0.18s;
        }
        .chat-input-box:focus {
          border: 1.5px solid #1976d2;
        }
        .chat-send-btn {
          background: #1976d2;
          color: #fff;
          border: none;
          border-radius: 8px;
          padding: 10px 22px;
          font-size: 1em;
          font-weight: 500;
          cursor: pointer;
          transition: background 0.18s;
        }
        .chat-send-btn:hover {
          background: #1565c0;
        }
        /* Eksklusif Prioritas */
        .prioritas-layout .chat-header {
          background: linear-gradient(90deg, #1565c0 60%, #42a5f5 100%);
          color: #fff;
          box-shadow: 0 4px 18px rgba(33,150,243,0.13);
          border-bottom: 2.5px solid #ffd600;
          position: relative;
        }
        .prioritas-badge {
          display: inline-block;
          background: linear-gradient(90deg, #ffd600 60%, #fffde7 100%);
          color: #1565c0;
          font-weight: bold;
          font-size: 0.98em;
          border-radius: 16px;
          padding: 5px 16px 5px 12px;
          margin-left: 18px;
          box-shadow: 0 2px 8px rgba(255,214,0,0.10);
          vertical-align: middle;
        }
        .prioritas-badge .fa-crown {
          color: #ffd600;
          margin-right: 7px;
        }
        .prioritas-layout .chat-bubble {
          background: rgba(33,150,243,0.13);
          border: 1.5px solid #42a5f5;
          box-shadow: 0 4px 24px rgba(33,150,243,0.13);
          backdrop-filter: blur(2.5px);
        }
        .prioritas-layout .msg-user .chat-bubble {
          background: linear-gradient(90deg, #e3f2fd 80%, #90caf9 100%);
          border: 1.5px solid #42a5f5;
          color: #0d47a1;
        }
        .prioritas-layout .msg-bot .chat-bubble {
          background: linear-gradient(90deg, #1976d2 80%, #42a5f5 100%);
          border: 1.5px solid #ffd600;
          color: #fff;
        }
        .prioritas-layout .faq-menu h3 {
          background: linear-gradient(90deg, #1565c0 60%, #42a5f5 100%);
          color: #ffd600;
        }
        .prioritas-layout .btn-faq-nav {
          background: linear-gradient(90deg, #42a5f5 80%, #ffd600 100%);
          color: #0d47a1;
          font-weight: 700;
        }
        .prioritas-layout .btn-faq-nav:hover {
          background: linear-gradient(90deg, #ffd600 80%, #42a5f5 100%);
          color: #1976d2;
        }
        .prioritas-layout .faq-item a:hover,
        .prioritas-layout .faq-item.active a {
          background: #fffde7;
          color: #1565c0;
        }
        .prioritas-layout .faq-item a {
          color: #1565c0;
        }
        .prioritas-layout .chat-input-area {
          background: #e3f2fd;
        }
        .bubble-timestamp {
          display: block;
          width: 100%;
          text-align: right;
          margin: 2px 0 0 0;
          font-size: 0.93em;
          color: #b0b8c1;
          font-style: italic;
          letter-spacing: 0.5px;
        }
        .msg-user .bubble-timestamp {
          text-align: left;
        }
        .msg-bot .bubble-timestamp {
          text-align: right;
        }
    </style>
</head>
<body class="<?= $is_prioritas ? 'prioritas-layout' : '' ?>">
<div class="container-faq-chat">
    <div class="faq-menu">
        <h3>Pertanyaan Populer</h3>
        <?php if (empty($faq_list)): ?>
            <div style="padding: 18px; color: #888;">Belum ada FAQ.</div>
        <?php else: ?>
        <ul class="faq-list">
            <?php foreach ($faq_list as $faq): ?>
                <li class="faq-item<?php if ($faq['id'] == $selected_faq_id) echo ' active'; ?>">
                    <a href="?faq_id=<?= $faq['id'] ?>"> <?= htmlspecialchars($faq['question']) ?> </a>
                </li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
        <div class="faq-menu-bottom-btns">
            <a href="?chat_teller=1" class="btn-faq-nav">💬 Chat dengan Teller</a>
            <div style="margin-top:auto;"><a href="dashboard_pengaturan.php" class="btn-faq-nav" style="margin-bottom:10px;">⟵ Kembali ke Pengaturan</a></div>
        </div>
    </div>
    <div class="chat-area">
        <div class="chat-header">
            <?php if ($is_prioritas): ?>
                <span style="font-weight:700;font-size:1.13em;letter-spacing:0.5px;">Menu Chat Both FTI M-Banking</span>
                <span class="prioritas-badge"><i class="fa fa-crown"></i> PRIORITAS</span>
            <?php else: ?>
                Menu Chat Both FTI M-Banking
            <?php endif; ?>
        </div>
        <?php if (isset($_GET['chat_teller']) && $_GET['chat_teller'] == '1'): ?>
            <div class="faq-answer-area" style="flex:1;">
                <div class="msg-bot" style="align-self:flex-start;text-align:left;">
                    <div class="chat-bubble">
                        <div class="bubble-content">
                            <?php if ($is_prioritas): ?>
                                <b>👋 Selamat datang, Nasabah Prioritas!</b><br>Silakan tulis pesan Anda kepada teller di bawah ini.<br>
                            <?php else: ?>
                                <b>👋 Selamat datang di layanan chat teller.</b><br>Silakan tulis pesan Anda di bawah ini.<br>
                            <?php endif; ?>
                        </div>
                        <span class="bubble-timestamp" id="bubble-time-teller"></span>
                    </div>
                </div>
            </div>
            <form class="chat-input-area" onsubmit="event.preventDefault();alert('Pesan terkirim (simulasi)!');this.reset();">
                <input type="text" class="chat-input-box" placeholder="Tulis pesan untuk teller..." required />
                <button type="submit" class="chat-send-btn">Kirim</button>
            </form>
        <?php else: ?>
            <div class="faq-answer-area">
                <?php if ($selected_faq_id): ?>
                    <?php
                    $selected_question = null;
                    foreach ($faq_list as $faq) {
                        if ($faq['id'] == $selected_faq_id) {
                            $selected_question = $faq['question'];
                            break;
                        }
                    }
                    ?>
                    <?php if ($selected_question): ?>
                        <div style="display:flex;flex-direction:column;gap:18px;width:100%;">
                            <div class="msg-user" style="align-self:flex-end;text-align:right;">
                                <div class="chat-bubble" style="background:#e3f2fd;color:#222;border-bottom-right-radius:4px;max-width:70%;display:inline-block;">
                                    <div class="bubble-content"> <?= nl2br(htmlspecialchars($selected_question)) ?> </div>
                                    <span class="bubble-timestamp"></span>
                                </div>
                            </div>
                            <?php if ($selected_answer): ?>
                                <div class="msg-bot" style="align-self:flex-start;text-align:left;">
                                    <div class="chat-bubble" style="background:#1976d2;color:#fff;border-bottom-left-radius:4px;max-width:70%;display:inline-block;">
                                        <div class="bubble-content"> <?= nl2br(htmlspecialchars($selected_answer)) ?> </div>
                                        <span class="bubble-timestamp"></span>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="msg-bot" style="align-self:flex-start;text-align:left;">
                                    <div class="chat-bubble" style="background:#e53935;color:#fff;border-bottom-left-radius:4px;max-width:70%;display:inline-block;">Maaf, belum ada jawaban untuk pertanyaan ini.</div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="msg-bot" style="align-self:flex-start;text-align:left;">
                        <div class="chat-bubble">
                            <div class="bubble-content">
                                <?php if ($is_prioritas): ?>
                                    👋 <b>Selamat datang, Nasabah Prioritas!</b><br>Silakan pilih pertanyaan di kiri atau klik <b>Chat dengan Teller</b> untuk bantuan langsung.<br>
                                <?php else: ?>
                                    👋 <b>Halo, ada yang bisa kami bantu?</b><br>Silakan pilih pertanyaan di kiri atau klik <b>Chat dengan Teller</b> untuk bantuan langsung.<br>
                                <?php endif; ?>
                            </div>
                            <span class="bubble-timestamp" id="bubble-time-greeting"></span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<script>
function getNowWIB() {
  const now = new Date();
  let h = now.getHours().toString().padStart(2,'0');
  let m = now.getMinutes().toString().padStart(2,'0');
  return `[${h}:${m} WIB]`;
}
// Untuk greeting dan chat teller (realtime)
function updateBubbleTime(id) {
  document.getElementById(id).textContent = getNowWIB();
}
if (document.getElementById('bubble-time-greeting')) {
  updateBubbleTime('bubble-time-greeting');
  setInterval(()=>updateBubbleTime('bubble-time-greeting'), 1000);
}
if (document.getElementById('bubble-time-teller')) {
  updateBubbleTime('bubble-time-teller');
  setInterval(()=>updateBubbleTime('bubble-time-teller'), 1000);
}
// Untuk bubble FAQ (static, set sekali saat load)
document.querySelectorAll('.faq-answer-area .bubble-timestamp').forEach(function(el){
  if (!el.textContent) el.textContent = getNowWIB();
});
</script>
</body>
</html> 
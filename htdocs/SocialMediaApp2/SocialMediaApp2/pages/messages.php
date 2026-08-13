<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../includes/db_connection.php';

$current_user_id = $_SESSION['user_id'];
$conversations = [];
$messages = [];
$selected_user_id = null;
$selected_user_name = '';
$message_status = '';

try {
    $sql_conversations = "SELECT DISTINCT u.user_id, u.full_name, u.profile_picture
                          FROM users u
                          JOIN messages m ON (m.sender_id = u.user_id AND m.receiver_id = ?)
                                          OR (m.receiver_id = u.user_id AND m.sender_id = ?)
                          WHERE u.user_id != ?
                          ORDER BY u.full_name ASC";
    $stmt_conv = $pdo->prepare($sql_conversations);
    $stmt_conv->execute([$current_user_id, $current_user_id, $current_user_id]);
    $conversations = $stmt_conv->fetchAll();

} catch (PDOException $e) {
    $message_status = "Error fetching conversations. Please try again later.";
    error_log("Error fetching conversations: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send_message'])) {
    $receiver_id = intval($_POST['receiver_id']);
    $message_content = trim($_POST['message_content']);

    if (!empty($message_content) && $receiver_id > 0) {
        try {
            $sql_send = "INSERT INTO messages (sender_id, receiver_id, content) VALUES (?, ?, ?)";
            $stmt_send = $pdo->prepare($sql_send);
            $stmt_send->execute([$current_user_id, $receiver_id, $message_content]);
            $message_status = "Message sent successfully!";
            $selected_user_id = $receiver_id;
        } catch (PDOException $e) {
            $message_status = "Error sending message. Please try again later.";
            error_log("Error sending message: " . $e->getMessage());
        }
    } else {
        $message_status = "Message cannot be empty.";
    }
}

if (isset($_GET['user_id']) && is_numeric($_GET['user_id'])) {
    $selected_user_id = intval($_GET['user_id']);
    try {
        $sql_selected_user = "SELECT user_id, full_name, profile_picture FROM users WHERE user_id = ?";
        $stmt_selected_user = $pdo->prepare($sql_selected_user);
        $stmt_selected_user->execute([$selected_user_id]);
        $selected_user_data = $stmt_selected_user->fetch();

        if ($selected_user_data) {
            $selected_user_name = $selected_user_data['full_name'];
            $sql_fetch_messages = "SELECT m.*, s.full_name AS sender_name, s.profile_picture AS sender_pic
                                   FROM messages m
                                   JOIN users s ON m.sender_id = s.user_id
                                   WHERE (m.sender_id = ? AND m.receiver_id = ?)
                                      OR (m.sender_id = ? AND m.receiver_id = ?)
                                   ORDER BY m.sent_at ASC";
            $stmt_fetch = $pdo->prepare($sql_fetch_messages);
            $stmt_fetch->execute([$current_user_id, $selected_user_id, $selected_user_id, $current_user_id]);
            $messages = $stmt_fetch->fetchAll();
        } else {
            $selected_user_id = null;
            $message_status = "Selected user not found.";
        }
    } catch (PDOException $e) {
        $message_status = "Error fetching messages. Please try again later.";
        error_log("Error fetching messages: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - Dark Social</title>
    <link rel="stylesheet" href="../css/style.css"> 
    <style>
        
        .messages-container { display: flex; gap: 25px; margin-top: 30px; background-color: #2c2c2c; padding: 25px; border-radius: 12px; box-shadow: 0 5px 20px rgba(0, 0, 0, 0.5); border: 1px solid #444; height: 650px; overflow: hidden; }
        .conversation-list { flex: 1; background-color: #3a3a3a; padding: 20px; border-radius: 10px; height: 100%; overflow-y: auto; box-shadow: inset 0 0 10px rgba(0,0,0,0.5); border: 1px solid #4a4a4a; }
        .conversation-list h3 { color: #ffffff; text-align: center; margin-bottom: 20px; }
        .conversation-item { display: flex; align-items: center; gap: 12px; padding: 12px 15px; margin-bottom: 8px; border-bottom: 1px solid #4a4a4a; cursor: pointer; transition: background-color 0.2s ease, color 0.2s ease; border-radius: 5px; color: #e0e0e0; }
        .conversation-item:last-child { border-bottom: none; }
        .conversation-item:hover { background-color: #4a4a4a; color: #8a2be2; }
        .conversation-item.active { background-color: #8a2be2; color: white; font-weight: bold; }
        .conversation-item img { width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 2px solid #8a2be2; }
        .message-area { flex: 3; background-color: #3a3a3a; padding: 20px; border-radius: 10px; display: flex; flex-direction: column; height: 100%; box-shadow: inset 0 0 10px rgba(0,0,0,0.5); border: 1px solid #4a4a4a; }
        .message-area h3 { color: #ffffff; text-align: center; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #444; }
        .message-list { flex-grow: 1; overflow-y: auto; padding-right: 15px; margin-bottom: 15px; scroll-behavior: smooth; }
        .message-list::-webkit-scrollbar { width: 8px; }
        .message-list::-webkit-scrollbar-track { background: #444; border-radius: 5px; }
        .message-list::-webkit-scrollbar-thumb { background: #8a2be2; border-radius: 5px; }
        .message-list::-webkit-scrollbar-thumb:hover { background: #9370db; }
        .message { margin-bottom: 15px; padding: 12px 18px; border-radius: 10px; max-width: 75%; word-wrap: break-word; box-shadow: 0 2px 5px rgba(0,0,0,0.3); }
        .message.sent { background-color: #4a148c; color: white; margin-left: auto; text-align: right; border-bottom-right-radius: 2px; }
        .message.received { background-color: #301934; color: #e0e0e0; margin-right: auto; text-align: left; border-bottom-left-radius: 2px; }
        .message-sender-info { display: flex; align-items: center; gap: 8px; margin-bottom: 6px; font-size: 0.88em; opacity: 0.8; }
        .message-sender-info img { width: 25px; height: 25px; border-radius: 50%; object-fit: cover; border: 1px solid rgba(255,255,255,0.3); }
        .message-list .message.sent .message-sender-info { display: none; }
        .message small { display: block; font-size: 0.8em; opacity: 0.7; margin-top: 5px; }
        .message.sent small { text-align: right; }
        .message-input-area { display: flex; gap: 10px; margin-top: auto; padding-top: 15px; border-top: 2px solid #444; }
        .message-input-area input[type="text"] { flex-grow: 1; padding: 12px 15px; border: 1px solid #555; border-radius: 6px; background-color: #3a3a3a; color: #e0e0e0; font-family: 'Crimson Text', serif; }
        .message-input-area input[type="text"]:focus { border-color: #8a2be2; box-shadow: 0 0 0 2px rgba(138, 43, 226, 0.3); }
        .message-input-area button { padding: 12px 20px; background-color: #8a2be2; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 1.1em; font-family: 'Playfair Display', serif; transition: background-color 0.3s ease, transform 0.2s ease; box-shadow: 0 3px 10px rgba(138, 43, 226, 0.4); }
        .message-input-area button:hover { background-color: #9370db; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(138, 43, 226, 0.5); }
    </style>
</head>
<body>
    <header class="dashboard-header">
        <div class="container">
            <h1>Messages</h1>
            <nav>
                <a href="dashboard.php">Dashboard</a> |
                <a href="profile.php?user_id=<?php echo htmlspecialchars($_SESSION['user_id']); ?>">My Profile</a> |
                <a href="create_post.php">Create Post</a> |
                <a href="search_users.php">Search Users</a> |
                <a href="logout.php">Logout</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="messages-container">
            <div class="conversation-list">
                <h3>Conversations</h3>
                <?php if (empty($conversations)): ?>
                    <p>No conversations yet.</p>
                <?php else: ?>
                    <?php foreach ($conversations as $conv_user): ?>
                        <div class="conversation-item <?php echo ($selected_user_id === $conv_user['user_id']) ? 'active' : ''; ?>"
                             onclick="window.location.href='messages.php?user_id=<?php echo $conv_user['user_id']; ?>'">
                            <img src="<?php echo htmlspecialchars($conv_user['profile_picture'] ? '../' . $conv_user['profile_picture'] : '../images/default_profile.png'); ?>" alt="Profile Pic" class="profile-pic-small">
                            <span><?php echo htmlspecialchars($conv_user['full_name']); ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="message-area">
                <?php if ($selected_user_id !== null): ?>
                    <h3>Chatting with: <?php echo htmlspecialchars($selected_user_name); ?></h3>
                    <?php if (!empty($message_status)): ?>
                        <p class="message <?php echo (strpos($message_status, 'success') !== false) ? 'sent' : 'error'; ?>"><?php echo htmlspecialchars($message_status); ?></p>
                    <?php endif; ?>

                    <div class="message-list" id="messageList">
                        <?php if (empty($messages)): ?>
                            <p>No messages in this conversation yet.</p>
                        <?php else: ?>
                            <?php foreach ($messages as $message): ?>
                                <div class="message <?php echo ($message['sender_id'] == $current_user_id) ? 'sent' : 'received'; ?>">
                                    <?php if ($message['sender_id'] != $current_user_id): // Show sender info only for received messages ?>
                                        <div class="message-sender-info">
                                            <img src="<?php echo htmlspecialchars($message['sender_pic'] ? '../' . $message['sender_pic'] : '../images/default_profile.png'); ?>" alt="Sender Pic" class="profile-pic-small">
                                            <span><?php echo htmlspecialchars($message['sender_name']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <p><?php echo nl2br(htmlspecialchars($message['content'])); ?></p>
                                    <small><?php echo date('H:i', strtotime($message['sent_at'])); ?></small>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <form action="messages.php" method="post" class="message-input-area">
                        <input type="hidden" name="receiver_id" value="<?php echo $selected_user_id; ?>">
                        <input type="text" name="message_content" placeholder="Type your message..." required>
                        <button type="submit" name="send_message">Send</button>
                    </form>

                <?php else: ?>
                    <p>Select a conversation from the left to start chatting.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Dark Social. All rights reserved.</p>
        </div>
    </footer>
    <script src="../js/chat_scroll.js"></script>
</body>
</html>
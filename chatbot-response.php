<?php
header('Content-Type: application/json; charset=UTF-8');
include 'db.php';

function json_out($arr, $code = 200) {
    http_response_code($code);
    echo json_encode($arr);
    exit;
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        json_out(['reply' => 'Invalid request method'], 405);
    }

    if (!isset($pdo) || !$pdo) {
        json_out(['reply' => "Sorry, I'm having trouble connecting right now. Please try again later."], 500);
    }

    $message = strtolower(trim($_POST['message'] ?? ''));
    $username = sanitizeInput($_POST['username'] ?? 'Guest');

    if ($message === '') json_out(['reply' => 'Please enter a message.'], 200);
    if (strlen($message) > 500) json_out(['reply' => 'Message is too long. Please keep it under 500 characters.'], 200);

    $found = false;
    $reply = '';

    // ---------------------------
    // 1) Handle urgent/latest first
    // ---------------------------
    if (strpos($message, 'urgent') !== false) {
        $stmt = $pdo->prepare("SELECT title FROM news WHERE urgent = 1 ORDER BY created_at DESC LIMIT 3");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($rows) {
            $lines = ["URGENT Updates:"];
            foreach ($rows as $r) {
                $lines[] = "• " . $r['title'];
            }
            $reply = implode("\n", $lines);
        } else {
            $reply = "No urgent updates right now. You can check the Balitaan section for regular news.";
        }

        $found = true;
    }
    else if (strpos($message, 'latest') !== false) {
        $stmt = $pdo->prepare("SELECT title FROM news ORDER BY created_at DESC LIMIT 5");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($rows) {
            $lines = ["Latest News:"];
            foreach ($rows as $r) {
                $lines[] = "• " . $r['title'];
            }
            $reply = implode("\n", $lines);
        } else {
            $reply = "No news posted yet. Please check again later.";
        }

        $found = true;
    }

    // ---------------------------
    // 2) If not urgent/latest, match prompts
    // ---------------------------
    if (!$found) {
        $stmt = $pdo->prepare("SELECT keyword, reply FROM chatbot_prompts ORDER BY LENGTH(keyword) DESC");
        $stmt->execute();
        $prompts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($prompts as $p) {
            $keyword = strtolower(trim($p['keyword']));
            if ($keyword !== '' && strpos($message, $keyword) !== false) {
                $reply = $p['reply'];
                $found = true;
                break;
            }
        }
    }

    // ---------------------------
    // 3) Default reply
    // ---------------------------
    if (!$found) {
        $reply = "Hello $username! I didn't quite get that. You can ask about:\n"
               . "• Barangay clearance\n"
               . "• Emergency hotline\n"
               . "• Latest news\n"
               . "• Office hours\n"
               . "• Certificate of residency/indigency";
    }

    // ---------------------------
    // 4) Log chat (optional)
    // ---------------------------
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS chatbot_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(100) NOT NULL,
            message TEXT NOT NULL,
            response TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            flagged TINYINT(1) DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $logStmt = $pdo->prepare("INSERT INTO chatbot_logs (username, message, response) VALUES (?, ?, ?)");
        $logStmt->execute([$username, $message, $reply]);
    } catch (Exception $e) {
        // ignore logging errors
    }

    json_out(['reply' => $reply], 200);

} catch (Exception $e) {
    error_log("Chatbot error: " . $e->getMessage());
    json_out(['reply' => "An error occurred. Please try again."], 500);
}

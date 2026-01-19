<?php
// chatbot-logs-api.php - Admin CRUD for chatbot logs (Read / Update flag / Delete)
header('Content-Type: application/json; charset=UTF-8');
include 'db.php';

function json_out($arr, $code = 200) {
    http_response_code($code);
    echo json_encode($arr, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function read_body_data() {
    $raw = file_get_contents('php://input');
    $data = [];
    if ($raw) {
        $json = json_decode($raw, true);
        if (is_array($json)) return $json;
        parse_str($raw, $data);
    }
    if (!empty($_POST)) return $_POST;
    return $data;
}

try {
    if (!isAdmin()) {
        json_out(['success' => false, 'message' => 'Unauthorized access'], 403);
    }

    if (!$pdo) {
        json_out(['success' => false, 'message' => 'Database connection failed'], 500);
    }

    // Ensure table exists (safe for demo projects)
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS chatbot_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(120) NOT NULL,
            message TEXT NOT NULL,
            response TEXT NOT NULL,
            flagged TINYINT(1) NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
    );

    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'GET') {
        $flagged = isset($_GET['flagged']) ? (int)$_GET['flagged'] : 0;
        if ($flagged === 1) {
            $stmt = $pdo->prepare("SELECT id, username, message, response, flagged, created_at FROM chatbot_logs WHERE flagged = 1 ORDER BY created_at DESC LIMIT 200");
            $stmt->execute();
        } else {
            $stmt = $pdo->prepare("SELECT id, username, message, response, flagged, created_at FROM chatbot_logs ORDER BY created_at DESC LIMIT 200");
            $stmt->execute();
        }
        json_out(['success' => true, 'data' => $stmt->fetchAll()]);
    }

    $data = read_body_data();

    if (!validateCSRFToken($data['csrf_token'] ?? '')) {
        json_out(['success' => false, 'message' => 'Invalid CSRF token'], 403);
    }

    if ($method === 'PUT') {
        $id = isset($data['id']) ? (int)$data['id'] : 0;
        $flagged = isset($data['flagged']) ? (int)$data['flagged'] : 0;
        if ($id <= 0) json_out(['success' => false, 'message' => 'Invalid id'], 400);
        $flagged = ($flagged === 1) ? 1 : 0;

        $stmt = $pdo->prepare("UPDATE chatbot_logs SET flagged = ? WHERE id = ?");
        $stmt->execute([$flagged, $id]);
        json_out(['success' => true, 'message' => 'Log updated']);
    }

    if ($method === 'DELETE') {
        $id = isset($data['id']) ? (int)$data['id'] : 0;
        if ($id <= 0) json_out(['success' => false, 'message' => 'Invalid id'], 400);

        $stmt = $pdo->prepare("DELETE FROM chatbot_logs WHERE id = ?");
        $stmt->execute([$id]);
        json_out(['success' => true, 'message' => 'Log deleted']);
    }

    json_out(['success' => false, 'message' => 'Method not allowed'], 405);

} catch (PDOException $e) {
    error_log('Chatbot logs API DB error: ' . $e->getMessage());
    json_out(['success' => false, 'message' => 'Database error occurred'], 500);
} catch (Exception $e) {
    error_log('Chatbot logs API error: ' . $e->getMessage());
    json_out(['success' => false, 'message' => 'An error occurred'], 500);
}

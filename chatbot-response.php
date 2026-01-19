<?php
// chatbot-response.php - Improved with validation and logging
header('Content-Type: text/html; charset=UTF-8');
include 'db.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit('Invalid request method');
    }

    // Check if PDO connection exists
    if (!$pdo) {
        echo "Sorry, I'm having trouble connecting right now. Please try again later.";
        exit;
    }

    // Sanitize inputs
    $message = strtolower(trim($_POST['message'] ?? ''));
    $username = sanitizeInput($_POST['username'] ?? 'Guest');

    // Validate message
    if (empty($message)) {
        echo "Please enter a message.";
        exit;
    }

    // Limit message length
    if (strlen($message) > 500) {
        echo "Message is too long. Please keep it under 500 characters.";
        exit;
    }

    $found = false;
    $response = "";

    // Search for matching keyword in database
    try {
        $stmt = $pdo->prepare("SELECT keyword, reply FROM chatbot_prompts ORDER BY LENGTH(keyword) DESC");
        $stmt->execute();
        $prompts = $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Chatbot prompts query failed: " . $e->getMessage());
        echo "I'm having technical difficulties. Please contact the Barangay Hall at +63 917 123 4567.";
        exit;
    }

    foreach ($prompts as $p) {
        $keyword = strtolower($p['keyword']);
        if (strpos($message, $keyword) !== false) {
            $response = $p['reply'];
            $found = true;
            break;
        }
    }

    // If asking about urgent/latest news, fetch from database
    if (!$found && (strpos($message, 'latest') !== false || strpos($message, 'urgent') !== false)) {
        try {
            $urgentStmt = $pdo->prepare("SELECT title FROM news WHERE urgent = 1 ORDER BY created_at DESC LIMIT 3");
            $urgentStmt->execute();
            $urgentNews = $urgentStmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Urgent news query failed: " . $e->getMessage());
            $urgentNews = [];
        }
        
        if (count($urgentNews) > 0) {
            $response = "<strong>URGENT Updates:</strong><br>";
            foreach ($urgentNews as $news) {
                $response .= "• " . htmlspecialchars($news['title']) . " (URGENT)<br>";
            }
            $response .= '<a href="#news" class="text-red-600 underline">View all news</a>';
            $found = true;
        }
    }

    // Default response if no match found
    if (!$found) {
        $response = "Hello $username! I didn't quite get that. You can ask about:<br>
        • Barangay clearance<br>
        • Emergency hotline<br>
        • Latest news<br>
        • Office hours<br>
        • Certificate of residency/indigency";
    }

    // Log conversation for analytics (optional)
    try {
        $logStmt = $pdo->prepare(
            "INSERT INTO chatbot_logs (username, message, response, created_at) 
             VALUES (?, ?, ?, NOW())"
        );
        // Only log if table exists - this is optional
        $logStmt->execute([$username, $message, strip_tags($response)]);
    } catch (PDOException $e) {
        // Silently fail if logging table doesn't exist
        error_log("Chatbot logging failed: " . $e->getMessage());
    }

    echo $response;

} catch (PDOException $e) {
    error_log("Chatbot database error: " . $e->getMessage());
    echo "I'm having trouble connecting right now. Please try again later or contact the Barangay Hall directly at +63 917 123 4567.";
} catch (Exception $e) {
    error_log("Chatbot error: " . $e->getMessage());
    echo "An error occurred. Please try again.";
}
?>
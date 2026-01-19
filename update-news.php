<?php
// update-news.php - Improved with validation
header('Content-Type: application/json');
include 'db.php';

try {
    // Validate database connection
    if (!isset($pdo) || $pdo === null) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        exit;
    }

    // Check admin authentication
    if (!isAdmin()) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
        exit;
    }

    // Validate CSRF token
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
        exit;
    }

    // Validate required fields
    $required = ['id', 'title', 'excerpt', 'category'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => "Field '$field' is required"]);
            exit;
        }
    }

    // Validate ID is numeric
    if (!is_numeric($_POST['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid news ID']);
        exit;
    }

    // Sanitize inputs
    $id = (int)$_POST['id'];
    $title = sanitizeInput($_POST['title']);
    $excerpt = sanitizeInput($_POST['excerpt']);
    $category = sanitizeInput($_POST['category']);
    $urgent = isset($_POST['urgent']) ? 1 : 0;

    // Validate category
    $validCategories = ['Agriculture', 'Health', 'Infrastructure', 'Urgent', 'General'];
    if (!in_array($category, $validCategories)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid category']);
        exit;
    }

    // Check if news exists
    $checkStmt = $pdo->prepare("SELECT id FROM news WHERE id = ?");
    $checkStmt->execute([$id]);
    if (!$checkStmt->fetch()) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'News not found']);
        exit;
    }

    // Update news
    $stmt = $pdo->prepare(
        "UPDATE news
         SET title = ?, excerpt = ?, category = ?, urgent = ?, updated_at = NOW()
         WHERE id = ?"
    );
    
    $stmt->execute([$title, $excerpt, $category, $urgent, $id]);
    
    echo json_encode([
        'success' => true,
        'message' => 'News updated successfully'
    ]);

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred'
    ]);
} catch (Exception $e) {
    error_log("General error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred'
    ]);
}
?>
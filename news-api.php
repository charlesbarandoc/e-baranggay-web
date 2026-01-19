<?php

header('Content-Type: application/json');
include 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
  
    if ($pdo === null) {
        throw new Exception('Database connection failed');
    }

  
    if ($method === 'GET') {
        $stmt = $pdo->query("SELECT * FROM news ORDER BY created_at DESC");
        $news = $stmt->fetchAll();
        
        echo json_encode([
            'success' => true,
            'data' => $news
        ]);
        exit;
    }

   
    if ($method === 'POST') {
       
        if (!isAdmin()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
            exit;
        }

      
        if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
            exit;
        }

      
        $required = ['title', 'excerpt', 'category'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => "Field '$field' is required"]);
                exit;
            }
        }

     
        $title = sanitizeInput($_POST['title']);
        $excerpt = sanitizeInput($_POST['excerpt']);
        $category = sanitizeInput($_POST['category']);
        $urgent = isset($_POST['urgent']) ? 1 : 0;
        $date = date('M d, Y');

   
        $validCategories = ['Agriculture', 'Health', 'Infrastructure', 'Urgent', 'General'];
        if (!in_array($category, $validCategories)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid category']);
            exit;
        }

       
        $stmt = $pdo->prepare(
            "INSERT INTO news (title, excerpt, category, date, urgent, created_at)
             VALUES (?, ?, ?, ?, ?, NOW())"
        );
        
        $stmt->execute([$title, $excerpt, $category, $date, $urgent]);
        
        echo json_encode([
            'success' => true,
            'message' => 'News added successfully',
            'id' => $pdo->lastInsertId()
        ]);
        exit;
    }

   
    if ($method === 'DELETE') {
        if (!isAdmin()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
            exit;
        }

        parse_str(file_get_contents("php://input"), $data);
        
        if (empty($data['id']) || !is_numeric($data['id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid news ID']);
            exit;
        }

        if (!validateCSRFToken($data['csrf_token'] ?? '')) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
            exit;
        }

        $stmt = $pdo->prepare("DELETE FROM news WHERE id = ?");
        $stmt->execute([$data['id']]);
        
        echo json_encode([
            'success' => true,
            'message' => 'News deleted successfully'
        ]);
        exit;
    }

  
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);

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
<?php
// document-request.php - Handle document requests
header('Content-Type: application/json');
include 'db.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        exit;
    }

    // Validate CSRF token
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Invalid security token']);
        exit;
    }

    // Required fields
    $required = ['document_type', 'full_name', 'contact_number', 'purpose'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => "Field '$field' is required"]);
            exit;
        }
    }

    // Sanitize inputs
    $documentType = sanitizeInput($_POST['document_type']);
    $fullName = sanitizeInput($_POST['full_name']);
    $contactNumber = sanitizeInput($_POST['contact_number']);
    $address = sanitizeInput($_POST['address'] ?? '');
    $purpose = sanitizeInput($_POST['purpose']);
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);

    // Validate document type
    $validTypes = ['barangay_clearance', 'certificate_of_indigency', 'certificate_of_residency', 'barangay_id'];
    if (!in_array($documentType, $validTypes)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid document type']);
        exit;
    }

    // Validate contact number (Philippine format)
    if (!preg_match('/^(\+63|0)?9\d{9}$/', $contactNumber)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid Philippine contact number']);
        exit;
    }

    // Validate email if provided
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid email address']);
        exit;
    }

    // Generate reference number
    $referenceNumber = 'BRG-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

    // Insert request into database
    if (!$pdo) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit;
    }

    $stmt = $pdo->prepare(
        "INSERT INTO document_requests 
         (reference_number, document_type, full_name, contact_number, address, email, purpose, status, created_at)
         VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())"
    );
    
    $stmt->execute([
        $referenceNumber,
        $documentType,
        $fullName,
        $contactNumber,
        $address,
        $email,
        $purpose
    ]);

    // Send success response
    echo json_encode([
        'success' => true,
        'message' => 'Document request submitted successfully',
        'reference_number' => $referenceNumber,
        'estimated_days' => 3
    ]);

    // Optional: Send email notification
    // mail($email, "Document Request Confirmation", "Your reference number is: $referenceNumber");

} catch (PDOException $e) {
    error_log("Document request error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to submit request. Please try again.'
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
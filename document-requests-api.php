<?php
header('Content-Type: application/json; charset=UTF-8');
include 'db.php';

function out($arr, $code = 200){
  http_response_code($code);
  echo json_encode($arr);
  exit;
}

if (!isAdmin()) out(['success'=>false,'message'=>'Unauthorized'], 401);
if (!isset($pdo) || !$pdo) out(['success'=>false,'message'=>'Database not connected'], 500);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
  $status = trim($_GET['status'] ?? '');
  try {
    if ($status !== '' && strtolower($status) !== 'all') {
      $stmt = $pdo->prepare("SELECT * FROM document_requests WHERE status = ? ORDER BY created_at DESC");
      $stmt->execute([$status]);
    } else {
      $stmt = $pdo->query("SELECT * FROM document_requests ORDER BY created_at DESC");
    }
    out(['success'=>true,'data'=>$stmt->fetchAll(PDO::FETCH_ASSOC)]);
  } catch (PDOException $e) {
    error_log("document_requests GET error: ".$e->getMessage());
    out(['success'=>false,'message'=>'Failed to load requests'], 500);
  }
}

$body = json_decode(file_get_contents('php://input'), true) ?? [];

if (!validateCSRFToken($body['csrf_token'] ?? '')) {
  out(['success'=>false,'message'=>'Invalid CSRF token'], 403);
}

if ($method === 'PUT') {
  $id = (int)($body['id'] ?? 0);
  $status = trim($body['status'] ?? '');
  if ($id <= 0 || $status === '') out(['success'=>false,'message'=>'ID and status required'], 400);

  try {
    $stmt = $pdo->prepare("UPDATE document_requests SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);
    out(['success'=>true]);
  } catch (PDOException $e) {
    error_log("document_requests PUT error: ".$e->getMessage());
    out(['success'=>false,'message'=>'Failed to update'], 500);
  }
}

if ($method === 'DELETE') {
  $id = (int)($body['id'] ?? 0);
  if ($id <= 0) out(['success'=>false,'message'=>'ID required'], 400);

  try {
    $stmt = $pdo->prepare("DELETE FROM document_requests WHERE id = ?");
    $stmt->execute([$id]);
    out(['success'=>true]);
  } catch (PDOException $e) {
    error_log("document_requests DELETE error: ".$e->getMessage());
    out(['success'=>false,'message'=>'Failed to delete'], 500);
  }
}

out(['success'=>false,'message'=>'Method not allowed'], 405);

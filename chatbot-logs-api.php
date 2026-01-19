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
  try {
    $flagged = isset($_GET['flagged']) ? (int)$_GET['flagged'] : null;

    if ($flagged === 1) {
      $stmt = $pdo->prepare("SELECT * FROM chatbot_logs WHERE flagged = 1 ORDER BY created_at DESC");
      $stmt->execute();
    } else {
      $stmt = $pdo->query("SELECT * FROM chatbot_logs ORDER BY created_at DESC");
    }

    out(['success'=>true,'data'=>$stmt->fetchAll(PDO::FETCH_ASSOC)]);
  } catch (PDOException $e) {
    error_log("chatbot_logs GET error: ".$e->getMessage());
    out(['success'=>false,'message'=>'Failed to load logs'], 500);
  }
}

$body = json_decode(file_get_contents('php://input'), true) ?? [];

if (!validateCSRFToken($body['csrf_token'] ?? '')) {
  out(['success'=>false,'message'=>'Invalid CSRF token'], 403);
}

if ($method === 'PUT') {
  $id = (int)($body['id'] ?? 0);
  $flagged = (int)($body['flagged'] ?? 0);
  if ($id<=0) out(['success'=>false,'message'=>'ID required'], 400);

  $stmt = $pdo->prepare("UPDATE chatbot_logs SET flagged=? WHERE id=?");
  $stmt->execute([$flagged,$id]);
  out(['success'=>true]);
}

if ($method === 'DELETE') {
  $id = (int)($body['id'] ?? 0);
  if ($id<=0) out(['success'=>false,'message'=>'ID required'], 400);

  $stmt = $pdo->prepare("DELETE FROM chatbot_logs WHERE id=?");
  $stmt->execute([$id]);
  out(['success'=>true]);
}

out(['success'=>false,'message'=>'Method not allowed'], 405);

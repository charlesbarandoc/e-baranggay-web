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
    $stmt = $pdo->query("SELECT * FROM chatbot_prompts ORDER BY id DESC");
    out(['success'=>true,'data'=>$stmt->fetchAll(PDO::FETCH_ASSOC)]);
  } catch (PDOException $e) {
    error_log("chatbot_prompts GET error: ".$e->getMessage());
    out(['success'=>false,'message'=>'Failed to load prompts'], 500);
  }
}

$body = json_decode(file_get_contents('php://input'), true) ?? [];

if (!validateCSRFToken($body['csrf_token'] ?? '')) {
  out(['success'=>false,'message'=>'Invalid CSRF token'], 403);
}

if ($method === 'POST') {
  $keyword = trim($body['keyword'] ?? '');
  $reply   = trim($body['reply'] ?? '');
  if ($keyword==='' || $reply==='') out(['success'=>false,'message'=>'Keyword and reply required'], 400);

  try {
    $stmt = $pdo->prepare("INSERT INTO chatbot_prompts (keyword, reply) VALUES (?, ?)");
    $stmt->execute([$keyword, $reply]);
    out(['success'=>true]);
  } catch (PDOException $e) {
    if ($e->getCode()==='23000') out(['success'=>false,'message'=>'Keyword already exists'], 409);
    error_log("chatbot_prompts POST error: ".$e->getMessage());
    out(['success'=>false,'message'=>'Failed to create'], 500);
  }
}

if ($method === 'PUT') {
  $id = (int)($body['id'] ?? 0);
  $keyword = trim($body['keyword'] ?? '');
  $reply = trim($body['reply'] ?? '');
  if ($id<=0 || $keyword==='' || $reply==='') out(['success'=>false,'message'=>'ID, keyword, reply required'], 400);

  try {
    $stmt = $pdo->prepare("UPDATE chatbot_prompts SET keyword=?, reply=? WHERE id=?");
    $stmt->execute([$keyword,$reply,$id]);
    out(['success'=>true]);
  } catch (PDOException $e) {
    error_log("chatbot_prompts PUT error: ".$e->getMessage());
    out(['success'=>false,'message'=>'Failed to update'], 500);
  }
}

if ($method === 'DELETE') {
  $id = (int)($body['id'] ?? 0);
  if ($id<=0) out(['success'=>false,'message'=>'ID required'], 400);

  try {
    $stmt = $pdo->prepare("DELETE FROM chatbot_prompts WHERE id=?");
    $stmt->execute([$id]);
    out(['success'=>true]);
  } catch (PDOException $e) {
    error_log("chatbot_prompts DELETE error: ".$e->getMessage());
    out(['success'=>false,'message'=>'Failed to delete'], 500);
  }
}

out(['success'=>false,'message'=>'Method not allowed'], 405);

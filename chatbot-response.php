<?php
session_start();
include 'chatbot-prompts.php'; // include the new prompts file

$message = strtolower(trim($_POST['message'] ?? ''));
$username = htmlspecialchars($_POST['username'] ?? 'Guest');

$response = "Hello $username! I can help you with Barangay services, news, emergency hotlines, and community programs.";

// Search for matching prompt
foreach($chatbot_prompts as $category => $prompts){
    foreach($prompts as $keyword => $reply){
        if(str_contains($message, $keyword)){
            $response = $reply;
            break 2; // stop searching after first match
        }
    }
}

// fallback if no match
if(empty($response)){
    $response = "Hello $username! Sorry, I didn't understand that. You can ask about Barangay Clearance, Certificate of Indigency, news, or emergency hotline.";
}

echo $response;
?>

<?php

if(isset($_SESSION['user'])) return;

if(empty($_COOKIE['remember_token'])) return;

$token = $_COOKIE['remember_token'];
$hash = hash('sha256',$token);

$stmt = $pdo->prepare("
    SELECT u.* FROM user_sessions s
    JOIN users u ON u.id = s.user_id
    WHERE s.token_hash=? AND s.expires_at > NOW()
");

$stmt->execute([$hash]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if($user){
    $_SESSION['user'] = $user;
}else{
    setcookie("remember_token","",time()-3600,"/");
}

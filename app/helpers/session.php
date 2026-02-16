<?php

// sessão dura 30 dias
$lifetime = 60*60*24*30;

session_set_cookie_params([
    'lifetime'=>$lifetime,
    'path'=>'/',
    'httponly'=>true,
    'samesite'=>'Lax'
]);

if(session_status()===PHP_SESSION_NONE){
    session_start();
}

/* ===== AUTO LOGIN VIA COOKIE ===== */

if(!isset($_SESSION['user']) && isset($_COOKIE['remember_token'])){

    require_once __DIR__."/../config/database.php";

    $hash = hash('sha256',$_COOKIE['remember_token']);

    $stmt=$pdo->prepare("
        SELECT u.*
        FROM user_sessions s
        JOIN users u ON u.id=s.user_id
        WHERE s.token_hash=? AND s.expires_at>NOW()
    ");
    $stmt->execute([$hash]);

    if($user=$stmt->fetch(PDO::FETCH_ASSOC)){
        $_SESSION['user']=$user;
    }
}

function isLogged(){
    return isset($_SESSION['user']);
}

function user(){
    return $_SESSION['user']??null;
}

<?php

function createRememberToken($userId, $pdo){

    $token = bin2hex(random_bytes(32));
    $hash = hash('sha256', $token);

    $expires = date('Y-m-d H:i:s', time() + 60*60*24*30); // 30 dias

    $stmt = $pdo->prepare("
        INSERT INTO user_sessions (user_id, token_hash, expires_at)
        VALUES (?, ?, ?)
    ");
    $stmt->execute([$userId, $hash, $expires]);

    setcookie(
        "remember_token",
        $token,
        time()+60*60*24*30,
        "/",
        "",
        false,
        true
    );
}

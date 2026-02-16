<?php

function requireLogin(){

    if(isset($_SESSION['user'])) return;

    // requisição AJAX
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH'])){

        $_SESSION['pending_action'] = [
            'type' => $_POST['action_type'] ?? null,
            'data' => $_POST
        ];

        http_response_code(401);
        echo "LOGIN_REQUIRED";
        exit;
    }

    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header("Location: /ultraware_gaming/public/login.php");
    exit;
}

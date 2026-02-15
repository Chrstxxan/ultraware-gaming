<?php

if (session_status() === PHP_SESSION_NONE){
    session_start();
}

function isLogged(){
    return isset($_SESSION['user']);
}

function user(){
    return $_SESSION['user'] ?? null;
}

<?php

function flash($type, $message, $data = null){
    $_SESSION['flash'] = [
        'type'=>$type,
        'message'=>$message,
        'data'=>$data
    ];
}

function getFlash(){
    if(!isset($_SESSION['flash'])) return null;

    $msg = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $msg;
}

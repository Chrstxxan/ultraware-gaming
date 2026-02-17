<?php

function env($key, $default=null){

    static $vars=null;

    if($vars===null){
        $vars = [];

        $path = __DIR__.'/../../.env';
        if(file_exists($path)){
            foreach(file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line){
                if(str_starts_with(trim($line),'#')) continue;
                [$k,$v]=explode('=',$line,2);
                $vars[trim($k)] = trim($v);
            }
        }
    }

    return $vars[$key] ?? $default;
}

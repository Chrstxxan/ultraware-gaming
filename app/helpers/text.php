<?php

function resumo($texto, $limite = 90){

    $texto = strip_tags($texto);

    if(strlen($texto) <= $limite)
        return $texto;

    $cortado = substr($texto, 0, $limite);

    // corta até o último espaço pra não quebrar palavra
    return substr($cortado, 0, strrpos($cortado, ' ')) . '...';
}

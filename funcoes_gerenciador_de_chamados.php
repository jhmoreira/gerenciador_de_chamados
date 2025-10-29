<?php

$arquivo = __DIR__.'/chamados.json';

function verificarArquivo($caminhoDoArquivo){

    if(!file_exists($caminhoDoArquivo)){
        file_put_contents($caminhoDoArquivo, json_encode([], JSON_PRETTY_PRINT));
    }
}

function carregarChamados($caminhoDoArquivo){
verificarArquivo($caminhoDoArquivo);
$json = file_get_contents($caminhoDoArquivo);
$conteudoDoArquivo = json_decode($json,true);
return is_array($conteudoDoArquivo) ? $conteudoDoArquivo : [];
}
?>
<?php

$caminhoDoArquivo = __DIR__.'/chamados.json';

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

function salvarChamados($caminhoDoArquivo, $dados){
    $arquivoTemporario = tempnam(sys_get_temp_dir(),'chamado');
    if($arquivoTemporario === false) return false;
    $gravarDados = file_put_contents($arquivoTemporario, json_encode($dados, JSON_PRETTY_PRINT));
    if($gravarDados === false){
        unlink($arquivoTemporario);
        return false;
    }

    if(!rename($arquivoTemporario, $caminhoDoArquivo)){
        if(!copy($arquivoTemporario, $caminhoDoArquivo)){
            unlink($arquivoTemporario);
            return false;
        }
        return false;
    }

    return true;
}
?>
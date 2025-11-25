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

function adicionarChamado($caminhoDoArquivo, $titulo, $prioridade, $setor, $previsao, $finalizado, $criado_em){
    $tituloDoChamado = $titulo;
    $prioridadeDoChamado = $prioridade;
    $setorResponsavel = $setor;
    $previsaoParaResolver = $previsao? $previsao :'';

    $chamados = carregarChamados($caminhoDoArquivo);

    $novoChamado = [
        'id'=> uniqid(),
        'titulo' => $tituloDoChamado,
        'prioridade' => $prioridadeDoChamado,
        'setor' => $setorResponsavel,
        'previsao' => $previsaoParaResolver,
        'criado_em' => date('d/m/Y H:i:s')
    ];

    $chamados[] = $novoChamado;
    if(!salvarChamados($caminhoDoArquivo, $chamados)){
        throw new RuntimeException('Erro ao salvar');
    }
    return $novoChamado;
}

function buscarPorId($chamados, $id){
    foreach($chamados as $indice => $item){
        if(isset($item['id']) && $item['id'] === $id){
            return $indice;
        } 
    }
    return null;
}

function atualizarStatus($caminhoDoArquivo, $id){
    $chamados = carregarChamados($caminhoDoArquivo);
    $indice = buscarPorId($chamados, $id);
    if($indice ===null){
        return false;
    }
    $chamados[$indice]['finalizado'] = empty($chamados[$indice]['finalizado']) ? true :false;
    return salvarChamados($caminhoDoArquivo, $chamados);
}

function excluirChamado($caminhoDoArquivo, $id){
    $chamados = carregarChamados($caminhoDoArquivo);
    $indice = buscarPorId($chamados, $id);
    if($indice === null){
        return false;
    }
    array_splice($chamados, $indice,1);
    return salvarChamados($caminhoDoArquivo, array_values($chamados));
}
?>
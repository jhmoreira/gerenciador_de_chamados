<?php

$caminhoDoArquivo = __DIR__.'/chamados.json';

function verificarArquivo($caminhoDoArquivo){

    if(!file_exists($caminhoDoArquivo)){
        file_put_contents($caminhoDoArquivo, json_encode([], JSON_PRETTY_PRINT));
    }
}

function carregarChamados($caminhoDoArquivo){
verificarArquivo($caminhoDoArquivo);
$conteudo = file_get_contents($caminhoDoArquivo);

if($conteudo === false || trim($conteudo)==''){
    return [];
}

$dados = json_decode($conteudo, true);
if(!is_array($dados)){
    return [];
}
return $dados;
}

function salvarChamados($caminhoDoArquivo, $dados){
    $json = json_encode($dados, JSON_PRETTY_PRINT);

    if($json === false) return false;
    $arquivoTemporario = tempnam(sys_get_temp_dir(), 'chamado_');
    if($arquivoTemporario === false) return false;

    if(file_put_contents($arquivoTemporario, $json)===false){
        unlink($arquivoTemporario);
        return false;
    }

    if(!rename($arquivoTemporario, $caminhoDoArquivo)){
        if(!copy($arquivoTemporario, $caminhoDoArquivo)){
            unlink($arquivoTemporario);
            return false;
        }
    }

    unlink($arquivoTemporario);
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
        'titulo' => $tituloDoChamado ?? '',
        'prioridade' => $prioridadeDoChamado ?? '',
        'setor' => $setorResponsavel ?? '',
        'previsao' => $previsaoParaResolver ?? '',
        'finalizado' => (bool)$finalizado,
        'criado_em' => $criado_em ? $criado_em : date('d/m/Y H:i:s')
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
        return null;
    }
    if(!array_key_exists('finalizado', $chamados[$indice])){
        $chamados[$indice]['finalizado'] = false;
    }
    $chamados[$indice]['finalizado'] =!$chamados[$indice]['finalizado'];
    if( !salvarChamados($caminhoDoArquivo, $chamados)){
        return false;
    }
    return $chamados[$indice];
}

function excluirChamado($caminhoDoArquivo, $id){
    $chamados = carregarChamados($caminhoDoArquivo);
    $indice = buscarPorId($chamados, $id);
    if($indice === null){
        return null;
    }
    $removido = $chamados[$indice];

    array_splice($chamados, $indice,1);
    if(!salvarChamados($caminhoDoArquivo, array_values($chamados))){
        return false;
    }
    return $removido;
}
?>
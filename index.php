<?php
header("Content-Type: application/json; charset=utf-8");
require('funcoes_gerenciador_de_chamados.php');

$caminhoDoArquivo = __DIR__."/chamados.json";
$metodo = $_SERVER['REQUEST_METHOD'];

if($metodo == "GET"){
    if(isset($_GET['id'])){
        $id = $_GET['id'];
        $chamados = carregarChamados($caminhoDoArquivo);
        $indice = buscarPorId($chamados, $id);

        if($indice === null){
            echo json_encode(["erro"=>"Chamado não encontrado"]);
            exit;
        }
        echo json_encode($chamados[$indice]);
        exit;
    }
    $chamados = carregarChamados($caminhoDoArquivo);
    echo json_encode($chamados);
    exit;
}

if($metodo ==="POST"){
    $input = json_decode(file_get_contents('php://input'),true);

    if(!$input){
        echo json_encode([
            "status" => "Erro",
            "mensagem" => "JSON inválido ou vazio"
        ]);
        exit;
    }

    $camposObrigatorios = ['titulo', 'prioridade', 'setor','previsao'];

    foreach($camposObrigatorios as $campo){
        if(!isset($input[$campo]) ||trim($input[$campo])===""){
            echo json_encode([
            "status" => "Erro",
            "mensagem" => "Campo obrigatório ausente: $campo"
        ]);
        exit;
        }
    }
    $novoChamado = adicionarChamado($caminhoDoArquivo, $input['titulo'], $input['prioridade'], $input['setor'], $input['previsao'],false, null);
    echo json_encode([
            "status" => "Ok",
            "mensagem" => "Chamado criado com sucesso",
            "data" => $novoChamado
        ]);
        exit;
}
if($metodo === "PUT"){
    if(!isset($_GET['id'])){
        echo json_encode([
            "status" => "Erro",
            "mensagem" => "Parâmetro id é obrigatório"

        ]);
        exit;
    }

    $id = $_GET['id'];

    $input = json_decode(file_get_contents('php://input'), true);
    $valor = $input['finalizado']?? null;
    $resultado = atualizarStatus($caminhoDoArquivo, $id);

    if($resultado ===null){
        echo json_encode([
            "status" => "Erro",
            "mensagem" => "Chamado não encontrado"

        ]);
        exit;
    }
    echo json_encode([
            "status" => "Ok",
            "mensagem" => "Status atualizado",
            "data" => $resultado

        ]);
        exit;
}
?>
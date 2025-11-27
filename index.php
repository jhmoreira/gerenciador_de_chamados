<?php
header("Content_Type: application/json; charset=utf-8");
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
?>
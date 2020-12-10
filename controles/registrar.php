<?php
require_once __DIR__.'/../autoload.php';

if(!empty($_POST)) {
    $post = json_decode($_POST['lista']);
    $arquivo = (isset($_FILES['arquivo'])) ? $_FILES['arquivo'] : null;
    $acesso = new Entidade();
    $acesso->gravarEntidade($post,$arquivo);
    $resultado = $acesso->retorno;

    foreach($resultado as $chave => $valor) {
        $resultado[$chave] = $valor;
    }
    echo(json_encode($resultado));
}
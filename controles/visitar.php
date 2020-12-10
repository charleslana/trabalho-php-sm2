<?php
require_once __DIR__.'/../autoload.php';

if(!empty($_POST)) {
    $post = $_POST['post'];
    $acesso = new Pesquisa($post);
    $acesso->visitarPesquisa();
    $resultado = $acesso->retorno;

    foreach($resultado as $chave => $valor) {
        $resultado[$chave] = $valor;
    }
    echo(json_encode($resultado));
}
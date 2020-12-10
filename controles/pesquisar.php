<?php
require_once __DIR__.'/../autoload.php';

if(!empty($_POST)) {
    $post = $_POST['post'];
    $pag = $_POST['pag'];
    $acesso = new Pesquisa($post,$pag);
    $acesso->paginar();
    $resultado = $acesso->retorno;

    foreach($resultado as $chave => $valor) {
        $resultado[$chave] = $valor;
    }
    echo(json_encode($resultado));
}
<?php
require_once __DIR__.'/../autoload.php';

if(!empty($_POST)) {
    $post = $_POST['post'];
    $acesso = new Entidade();
    $acesso->email = $post['email'];
    $acesso->senha = $post['senha'];
    $acesso->confirmaSenha = $post['senha'];
    $acesso->entrarEntidade();
    $resultado = $acesso->retorno;

    foreach($resultado as $chave => $valor) {
        $resultado[$chave] = $valor;
    }
    echo(json_encode($resultado));
}
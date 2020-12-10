<?php
date_default_timezone_set('America/Sao_Paulo');
spl_autoload_register(function ($class) {
    require_once __DIR__.'/servicos/' .$class. '.php';
});
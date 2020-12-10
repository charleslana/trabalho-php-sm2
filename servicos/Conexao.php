<?php

class Conexao extends Configuracao {

    private $host;
    private $banco;
    private $usuario;
    private $senha;
    private $base;
    private $sConsulta;
    private $conectado = false;
    private $parametros;
    private $mensagem = 'Ops, ocorreu um erro no Banco de Dados: ';
    public $quantidadeLinhas = 0;
    public $quantidadeColunas = 0;
    public $quantidadeConsultas = 0;
    
    public function __construct(
            $host = self::HOST,
            $banco = self::BANCO,
            $usuario = self::USUARIO,
            $senha = self::SENHA
    ) {
        $this->host = $host;
        $this->banco = $banco;
        $this->usuario = $usuario;
        $this->senha = $senha;
        $this->conectar();
        $this->parametros = array();
    }

    private function conectar() {
        try {
            $this->base = new PDO('mysql:dbname=' . $this->banco . ';host=' . $this->host . ';charset=utf8', $this->usuario, $this->senha, array(
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
            ));
            $this->conectado = true;
        }
        catch (PDOException $e) {
            $this->registrarExcecao($e->getMessage());
            die($this->mensagem . $e->getMessage());
        }
    }

    public function fecharConexao() {
        $this->base = null;
    }

    private function iniciar($consulta, $parametros = "") {
        if (!$this->conectado) {
            $this->conectar();
        }
        try {
            $this->parametros = $parametros;
            $this->sConsulta = $this->base->prepare($this->construirParametros($consulta, $this->parametros));
            if (!empty($this->parametros)) {
                if (array_key_exists(0, $parametros)) {
                    $parametrosTipo = true;
                    array_unshift($this->parametros, "");
                    unset($this->parametros[0]);
                } else {
                    $parametrosTipo = false;
                }
                foreach ($this->parametros as $coluna => $valor) {
                    $this->sConsulta->bindParam($parametrosTipo ? intval($coluna) : ":" . $coluna, $this->parametros[$coluna]);
                }
            }
            $this->sConsulta->execute();
            $this->quantidadeConsultas++;
        }
        catch (PDOException $e) {
			$this->registrarExcecao($e->getMessage());
            die($this->mensagem . $e->getMessage());
        }
        $this->parametros = array();
    }

    private function construirParametros($consulta, $parametros = null) {
        if (!empty($parametros)) {
            $declaracaoBruta = explode(" ", $consulta);
            foreach ($declaracaoBruta as $valor) {
                if (strtolower($valor) == 'in') {
                    return str_replace("(?)", "(" . implode(",", array_fill(0, count($parametros), "?")) . ")", $consulta);
                }
            }
        }
        return $consulta;
    }

    public function verConsulta($consulta, $parametros = null, $modoDeBusca = PDO::FETCH_ASSOC) {
        $consulta = trim($consulta);
        $declaracaoBruta = explode(" ", $consulta);
        $this->iniciar($consulta, $parametros);
        $declaracao = strtolower($declaracaoBruta[0]);
        if ($declaracao === 'select' || $declaracao === 'show') {
            return $this->sConsulta->fetchAll($modoDeBusca);
        }
        else if ($declaracao === 'insert' || $declaracao === 'update' || $declaracao === 'delete') {
            return $this->sConsulta->rowCount();
        }
        else {
            return NULL;
        }
    }

    public function verColuna($consulta, $parametros = null) {
        $this->iniciar($consulta, $parametros);
        $resultadoColuna = $this->sConsulta->fetchAll(PDO::FETCH_COLUMN);
        $this->quantidadeLinhas = $this->sConsulta->rowCount();
        $this->quantidadeColunas = $this->sConsulta->columnCount();
        $this->sConsulta->closeCursor();
        return $resultadoColuna;
    }

    public function verLinha($consulta, $parametros = null, $modoDeBusca = PDO::FETCH_ASSOC) {
        $this->iniciar($consulta, $parametros);
        $resultadoDaLinha = $this->sConsulta->fetch($modoDeBusca);
        $this->quantidadeLinhas = $this->sConsulta->rowCount();
        $this->quantidadeColunas = $this->sConsulta->columnCount();
        $this->sConsulta->closeCursor();
        return $resultadoDaLinha;
    }

    public function verUnico($consulta, $parametros = null) {
        $this->iniciar($consulta, $parametros);
        return $this->sConsulta->fetchColumn();
    }

    public function verUltimoIdInserido() {
        return $this->base->lastInsertId();
    }

    private function registrarExcecao($mensagem, $sql = '') {
        $registro = new Registro;
        $mensagem = $this->mensagem . $mensagem;   
        if (!empty($sql)) {
            $mensagem .= "\r\nExecução do Sql: " . $sql;
        }
        $registro->escrever($mensagem);
    }
}
<?php
class Validacao extends Notificacao {

    public $campo;
    public $campoConfirma;
    public $validar = true;
    public $retornoValidar;

    public function validarCampos() {
        if(!$this->campo && $this->validar) {
            $this->validar = false;
            $this->retornoValidar = self::CAMPO_EM_BRANCO;
        }
    }

    public function validarEmail() {
        if(strlen($this->campo) < 3 && $this->validar || strlen($this->campo) > 45 && $this->validar) {
            $this->validar = false;
            $this->retornoValidar = self::CAMPO_EMAIL_MAX;
        }
        else if(!preg_match("/^[-!#$%&\'*+\\.\/0-9=?A-Z^_`{|}~]+@([-0-9A-Z]+\.)+([0-9A-Z]){2,4}$/i", $this->campo) && $this->validar) {
            $this->validar = false;
            $this->retornoValidar = self::CAMPO_EMAIL_VALIDO;
        }
    }

    public function validarSenha() {
        if(strlen($this->campo) < 6 && $this->validar || strlen($this->campo) > 45 && $this->validar) {
            $this->validar = false;
            $this->retornoValidar = self::CAMPO_SENHA_MAX;
        }
        else if($this->campo != $this->campoConfirma && $this->validar) {
            $this->validar = false;
            $this->retornoValidar = self::CAMPO_SENHA_DIFERENTE;
        }
        else {
            $this->campoConfirma = null;
        }
    }

    public function validarOcupacao() {
        if($this->campo < 1 && $this->validar || $this->campo > 2 && $this->validar) {
            $this->validar = false;
            $this->retornoValidar = self::CAMPO_OCUPACAO_VALIDO;
        }
    }

    public function validarNome() {
        if(strlen($this->campo) < 3 && $this->validar || strlen($this->campo) > 20 && $this->validar) {
            $this->validar = false;
            $this->retornoValidar = self::CAMPO_NOME_MAX;
        }
    }

    public function validarSobrenome() {
        if(strlen($this->campo) < 3 && $this->validar || strlen($this->campo) > 20 && $this->validar) {
            $this->validar = false;
            $this->retornoValidar = self::CAMPO_SOBRENOME_MAX;
        }
    }

    public function validarEndereco() {
        if(strlen($this->campo) < 3 && $this->validar || strlen($this->campo) > 45 && $this->validar) {
            $this->validar = false;
            $this->retornoValidar = self::CAMPO_ENDERECO_MAX;
        }
    }

    public function validarCidade() {
        if(strlen($this->campo) < 3 && $this->validar || strlen($this->campo) > 45 && $this->validar) {
            $this->validar = false;
            $this->retornoValidar = self::CAMPO_CIDADE_MAX;
        }
    }

    public function validarEstado() {
        if(strlen($this->campo) < 2 && $this->validar || strlen($this->campo) > 45 && $this->validar) {
            $this->validar = false;
            $this->retornoValidar = self::CAMPO_ESTADO_MAX;
        }
    }

    public function validarNumero() {
        if(strlen($this->campo) < 1 && $this->validar || strlen($this->campo) > 10 && $this->validar) {
            $this->validar = false;
            $this->retornoValidar = self::CAMPO_NUMERO_MAX;
        }
    }

    public function validarTelefone() {
        if(strlen($this->campo) < 3 && $this->validar || strlen($this->campo) > 20 && $this->validar) {
            $this->validar = false;
            $this->retornoValidar = self::CAMPO_TELEFONE_MAX;
        }
    }

    public function validarDescricao() {
        if(strlen($this->campo) < 10 && $this->validar || strlen($this->campo) > 1000 && $this->validar) {
            $this->validar = false;
            $this->retornoValidar = self::CAMPO_DESCRICAO_MAX;
        }
    }

    public function validarBanner() {
        if(!isset($this->campo['name']) && $this->validar) {
            $this->validar = false;
            $this->retornoValidar = self::CAMPO_EM_BRANCO;
        }
        else if($this->validar) {
            $acesso = new Banner;
            $acesso->campo = $this->campo;
            $acesso->enviarBanner();
            $this->validar = $acesso->validar;
            $this->retornoValidar = $acesso->retornoValidar;          
        }
    }
}
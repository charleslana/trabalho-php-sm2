<?php
class Banner extends Notificacao {

    public $campo;
    public $validar = true;
    public $retornoValidar;
    private $caminho = __DIR__.'/../uploads/';
    private $extensoesValidas = ['jpg','jpeg','png'];

    public function enviarBanner() {
        $tipoDeArquivo = pathinfo($this->caminho .$this->campo['name'],PATHINFO_EXTENSION);
        $tipoDeArquivo = strtolower($tipoDeArquivo);
        if(!in_array(strtolower($tipoDeArquivo), $this->extensoesValidas)) {
            $this->validar = false;
            $this->retornoValidar = self::CAMPO_BANNER_VALIDO;
        }
        else if($this->campo['size'] > 1000000) {
            $this->validar = false;
            $this->retornoValidar = self::CAMPO_BANNER_VALIDO;
        }
        else {
            if(!is_dir($this->caminho)) {
                mkdir($this->caminho);
                chmod($this->caminho,0777);
            }
            $nome = bin2hex(random_bytes(32)) .'.' .$tipoDeArquivo;
            if(move_uploaded_file($this->campo['tmp_name'],$this->caminho .$nome)) {
                chmod($this->caminho .$nome,0777);
                $this->retornoValidar = $nome;
            }
            else {
                $this->validar = false;
                $this->retornoValidar = self::CAMPO_BANNER_ERRO;
            }
        }
    }
}
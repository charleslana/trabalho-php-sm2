<?php
class Registro extends Ip {

	private $caminho = __DIR__.'/../logs/';

	public function escrever($mensagem) {
		$data = new DateTime();
		$registro = $this->caminho . $data->format('d-m-Y') . ".txt";
		if (is_dir($this->caminho)) {
			$ip = $this->verIp();
			if (!file_exists($registro)) {
				$abrir = fopen($registro, 'a+') or die('Erro fatal!');
				$conteudoRegistro = "Data: " . $data->format('d/m/Y H:i:s') . "\rTipo de Erro: " . $mensagem . "\r\nIP : " . $ip . "\r\n";
				fwrite($abrir, $conteudoRegistro);
				fclose($abrir);
				chmod($registro,0777);
			}
			else {
				$this->editar($registro, $data, $mensagem, $ip);
			}
		}
		else {
			if (mkdir($this->caminho, 0777) === true) {
				chmod($this->caminho,0777);
				$this->escrever($mensagem);
			}
		}
	}

	private function editar($registro, $data, $mensagem, $ip) {
		$conteudoRegistro = "Data: " . $data->format('d/m/Y H:i:s') . "\rTipo de Erro: " . $mensagem . "\r\nIP : " . $ip . "\r\n\r\n";
		$conteudoRegistro = $conteudoRegistro . file_get_contents($registro);
		file_put_contents($registro, $conteudoRegistro);
	}
}
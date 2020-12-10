<?php
class Pesquisa {

    private $campo;
    private $pagina;
    private $inicio;
    private $limite = 10;
    private $quantidadePaginas;
    private $quantidadeTotal;
    private $quantidadeLinks = 5;
    public $retorno = [];

    public function __construct($post,$pag = null) {
        $this->campo = strip_tags(trim($post));
        $this->inicio = abs((int) $pag);
        $this->pagina = abs((int) $pag);
    }

    public function paginar() {
        if($this->campo) {
            $base = new Conexao;
            $consulta = $base->verLinha(
                'SELECT tb_endereco.*, tb_usuario.* FROM tb_endereco INNER JOIN tb_usuario ON (tb_usuario.id =  tb_endereco.usuario_id)
                WHERE endereco LIKE :endereco OR cidade LIKE :cidade OR estado LIKE :estado',
                ['endereco' => '%'.$this->campo.'%', 'cidade' => '%'.$this->campo.'%',
                'estado' => '%'.$this->campo.'%']
            );
            $base->fecharConexao();
            $this->quantidadeTotal = $base->quantidadeLinhas;
            $this->quantidadePaginas = floor($this->quantidadeTotal / ($this->limite + 1));
            $this->pesquisar();
        }
    }

    private function pesquisar() {
        $this->inicio = (($this->inicio > 0 ? $this->inicio : 1) - 1) * $this->limite;
        $base = new Conexao;
        $consulta = $base->verConsulta(
            'SELECT tb_endereco.*, tb_usuario.* FROM tb_endereco INNER JOIN tb_usuario ON (tb_usuario.id =  tb_endereco.usuario_id)
            WHERE endereco LIKE :endereco OR cidade LIKE :cidade OR estado LIKE :estado ORDER BY id DESC LIMIT '.$this->inicio.','.$this->limite.'',
            ['endereco' => '%'.$this->campo.'%', 'cidade' => '%'.$this->campo.'%',
            'estado' => '%'.$this->campo.'%']
        );
        if($consulta) {
            foreach($consulta as $chave => $valor) {
                unset($consulta[$chave]['email']);
                unset($consulta[$chave]['senha']);
                unset($consulta[$chave]['token']);
                unset($consulta[$chave]['usuario_id']);
                unset($consulta[$chave]['endereco_id']);
                unset($consulta[$chave]['telefone_id']);
            }
            $combinar = [
                'paginacao' => [
                    'quantidadeTotal' => $this->quantidadeTotal,
                    'quantidadePaginas' => $this->quantidadePaginas,
                    'quantiaPorPagina' => $this->limite,
                    'quantidadeLinks' => $this->quantidadeLinks,
                    'pagina' => $this->pagina,
                ]
            ];
            $consulta = array_merge($consulta, $combinar);
            $this->retorno = $consulta;
        }
        $base->fecharConexao();
    }

    public function visitarPesquisa() {
        $base = new Conexao;
        $consulta = $base->verLinha(
            'SELECT tb_usuario.*, tb_endereco.*, tb_telefone.* FROM tb_usuario
            INNER JOIN tb_endereco ON (tb_endereco.usuario_id =  tb_usuario.id)
            INNER JOIN tb_telefone ON (tb_telefone.usuario_id =  tb_usuario.id)
            WHERE id = :id LIMIT 1',
            ['id' => abs((int) $this->campo)]
        );
        if($consulta) {
            unset($consulta['email']);
            unset($consulta['senha']);
            unset($consulta['token']);
            unset($consulta['usuario_id']);
            unset($consulta['endereco_id']);
            unset($consulta['telefone_id']);
            $this->retorno['sucesso'] = $consulta;
        }
    }
}
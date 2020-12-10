<?php
class Entidade extends Validacao {

    public $email;
    public $senha;
    public $confirmaSenha;
    private $atualSenha;
    private $ocupacao;
    private $nome;
    private $sobrenome;
    private $endereco;
    private $cidade;
    private $estado;
    private $numero;
    private $telefone;
    private $descricao;
    private $banner;
    private $registro;
    private $token = 'tokenEntidade';
    public $retorno = [];

    private function setarEntidade($lista,$arquivo) {
        foreach($lista as $data) {
            $this->email = trim($data->email);
            $this->senha = trim($data->senha);
            $this->confirmaSenha = trim($data->csenha);
            $this->atualSenha = (isset($data->asenha)) ? trim($data->asenha) : null;
            $this->ocupacao = trim((int) $data->ocupacao);
            $this->nome = strip_tags(trim($data->nome));
            $this->sobrenome = strip_tags(trim($data->sobrenome));
            $this->endereco = strip_tags(trim($data->endereco));
            $this->cidade = strip_tags(trim($data->cidade));
            $this->estado = strip_tags(trim($data->estado));
            $this->numero = trim((int) abs($data->numero));
            $this->telefone = trim((int) abs($data->telefone));
            $this->descricao = strip_tags(trim($data->descricao));
        }
        $this->banner = $arquivo;
    }

    public function gravarEntidade($lista,$arquivo) {
        $this->setarEntidade($lista,$arquivo);
        $this->campo = $this->email;
        $this->validarCampos();
        $this->validarEmail();
        $this->campo = $this->senha;
        $this->campoConfirma = $this->confirmaSenha;
        $this->validarCampos();
        $this->validarSenha();
        $this->campo = $this->ocupacao;
        $this->validarCampos();
        $this->validarOcupacao();
        $this->campo = $this->nome;
        $this->validarCampos();
        $this->validarNome();
        $this->campo = $this->sobrenome;
        $this->validarCampos();
        $this->validarSobrenome();
        $this->campo = $this->endereco;
        $this->validarCampos();
        $this->validarEndereco();
        $this->campo = $this->cidade;
        $this->validarCampos();
        $this->validarCidade();
        $this->campo = $this->estado;
        $this->validarCampos();
        $this->validarEstado();
        $this->campo = $this->numero;
        $this->validarCampos();
        $this->validarNumero();
        $this->campo = $this->telefone;
        $this->validarCampos();
        $this->validarTelefone();
        $this->campo = $this->descricao;
        $this->validarCampos();
        $this->validarDescricao();
        $this->campo = $this->banner;
        $this->validarBanner();
        if(!$this->validar) {
            $this->retorno['erro'] = $this->retornoValidar;
        }
        else {
            $base = new Conexao;
            $consulta = $base->verUnico(
                'SELECT id FROM tb_usuario WHERE email = :email LIMIT 1',
                ['email' => $this->email]
            );
            if($consulta) {
                $this->retorno['erro'] = self::CAMPO_EMAIL_EXISTENTE;
            }
            else {
                $data = new DateTime();
                $ocupacao = $this->ocupacao == 1 ? 'Pescador' : 'Mercado';
                $consulta = $base->verConsulta(
                    'INSERT INTO tb_usuario (email, senha, ocupacao, nome, sobrenome, descricao, banner, registro)
                    VALUES (:email, :senha, :ocupacao, :nome, :sobrenome, :descricao, :banner, :registro)',
                    ['email' => $this->email, 'senha' => md5($this->senha), 'ocupacao' => $ocupacao, 'nome' => $this->nome, 'sobrenome' => $this->sobrenome,
                    'descricao' => $this->descricao, 'banner' => $this->retornoValidar, 'registro' => $data->format('Y-m-d H:i:s')]
                );
                if($consulta) {
                    $id = $base->verUltimoIdInserido();
                    $consulta = $base->verConsulta(
                        'INSERT INTO tb_endereco (usuario_id, endereco, cidade, estado, numero)
                        VALUES (:usuario_id, :endereco, :cidade, :estado, :numero)',
                        ['usuario_id' => $id, 'endereco' => $this->endereco, 'cidade' => $this->cidade, 'estado' => $this->estado, 'numero' => $this->numero]
                    );
                    if($consulta) {
                        $consulta = $base->verConsulta(
                            'INSERT INTO tb_telefone (usuario_id, telefone)
                            VALUES (:usuario_id, :telefone)',
                            ['usuario_id' => $id, 'telefone' => $this->telefone]
                        );
                        if($consulta) {
                            session_start();
                            $token = md5(session_id());
                            if(!isset($_SESSION[$this->token])) {
                                $consulta = $base->verConsulta(
                                    'UPDATE tb_usuario SET token = :token WHERE id = :id',
                                    ['token' => $token, 'id' => $id]
                                );
                            }
                            if($consulta) {
                                $_SESSION[$this->token] = $token;
                                $this->retorno['sucesso'] = self::CAMPO_CADASTRO_SUCESSO;
                            }
                        }
                    }
                }
            }
            $base->fecharConexao();
        }
    }

    private function verAutenticacao() {
        session_start();
        $acesso = new HttpStatus;
        if(!isset($_SESSION[$this->token])) {
            $acesso->aplicarRequisicao(200);
            exit;
        }
        else {
            $base = new Conexao;
            $consulta = $base->verLinha(
                'SELECT tb_usuario.*, tb_endereco.*, tb_telefone.* FROM tb_usuario
                INNER JOIN tb_endereco ON (tb_endereco.usuario_id =  tb_usuario.id)
                INNER JOIN tb_telefone ON (tb_telefone.usuario_id =  tb_usuario.id)
                WHERE token = :token LIMIT 1',
                ['token' => $_SESSION[$this->token]]
            );
            $base->fecharConexao();
            if(!$consulta) {
                unset($_SESSION[$this->token]);
                $acesso->aplicarRequisicao(403);
                exit;
            }
            else {
                return $consulta;
            }
        }
    }

    public function sairEntidade() {
        session_start();
        if(isset($_SESSION[$this->token])) {
            $base = new Conexao;
            $consulta = $base->verConsulta(
                'UPDATE tb_usuario SET token = :token WHERE token = :tokenEntidade',
                ['token' => NULL, 'tokenEntidade' => $_SESSION[$this->token]]
            );
            if($consulta) {
                unset($_SESSION[$this->token]);
                $this->retorno['sucesso'] = true;
            }
            $base->fecharConexao();
        }
    }

    public function entrarEntidade() {
        session_start();
        if(!isset($_SESSION[$this->token])) {
            $this->campo = $this->email;
            $this->validarCampos();
            $this->validarEmail();
            $this->campo = $this->senha;
            $this->campoConfirma = $this->confirmaSenha;
            $this->validarCampos();
            $this->validarSenha();
            if(!$this->validar) {
                $this->retorno['erro'] = $this->retornoValidar;
            }
            else {
                $base = new Conexao;
                $consulta = $base->verUnico(
                    'SELECT id FROM tb_usuario WHERE email = :email AND senha = :senha LIMIT 1',
                    ['email' => $this->email, 'senha' => md5($this->senha)]
                );
                if(!$consulta) {
                    $this->retorno['erro'] = self::CAMPO_EMAIL_ERRO;
                }
                else {
                    $token = md5(session_id());
                    $consulta = $base->verConsulta(
                        'UPDATE tb_usuario SET token = :token WHERE id = :id',
                        ['token' => $token, 'id' => $consulta]
                    );
                    if($consulta) {
                        $_SESSION[$this->token] = $token;
                        $this->retorno['sucesso'] = true;
                    }
                }
                $base->fecharConexao();
            }           
        }
    }

    public function lerEntidade() {
        $consulta = $this->verAutenticacao();
        unset($consulta['senha']);
        unset($consulta['usuario_id']);
        unset($consulta['endereco_id']);
        unset($consulta['telefone_id']);
        $this->retorno['sucesso'] = $consulta;
    }

    public function atualizarEntidade($lista,$arquivo) {
        $consulta = $this->verAutenticacao();
        $this->setarEntidade($lista,$arquivo);
        if($this->atualSenha) {
            $this->campo = $this->senha;
            $this->campoConfirma = $this->confirmaSenha;
            $this->validarCampos();
            $this->validarSenha();
            $this->campo = md5($this->atualSenha);
            $this->campoConfirma = $consulta['senha'];
            $this->validarSenha();
        }
        $this->campo = $this->ocupacao;
        $this->validarCampos();
        $this->validarOcupacao();
        $this->campo = $this->nome;
        $this->validarCampos();
        $this->validarNome();
        $this->campo = $this->sobrenome;
        $this->validarCampos();
        $this->validarSobrenome();
        $this->campo = $this->endereco;
        $this->validarCampos();
        $this->validarEndereco();
        $this->campo = $this->cidade;
        $this->validarCampos();
        $this->validarCidade();
        $this->campo = $this->estado;
        $this->validarCampos();
        $this->validarEstado();
        $this->campo = $this->numero;
        $this->validarCampos();
        $this->validarNumero();
        $this->campo = $this->telefone;
        $this->validarCampos();
        $this->validarTelefone();
        $this->campo = $this->descricao;
        $this->validarCampos();
        $this->validarDescricao();
        if($this->banner) {
            $this->campo = $this->banner;
            $this->validarBanner();
        }
        if(!$this->validar) {
            $this->retorno['erro'] = $this->retornoValidar;
        }
        else {
            $senha = $this->atualSenha ? md5($this->senha) : $consulta['senha'];
            $banner = $this->banner ? $this->retornoValidar : $consulta['banner'];
            $ocupacao = $this->ocupacao == 1 ? 'Pescador' : 'Mercado';
            $base = new Conexao;
            $base->verConsulta(
                'UPDATE tb_usuario
                INNER JOIN tb_endereco ON (tb_endereco.usuario_id = tb_usuario.id)
                INNER JOIN tb_telefone ON (tb_telefone.usuario_id = tb_usuario.id)
                SET senha = :senha, ocupacao = :ocupacao, nome = :nome, sobrenome = :sobrenome, endereco = :endereco, cidade = :cidade,
                estado = :estado, numero = :numero, telefone = :telefone, descricao = :descricao, banner = :banner
                WHERE id = :id',
                ['senha' => $senha, 'ocupacao' => $ocupacao, 'nome' => $this->nome, 'sobrenome' => $this->sobrenome, 'endereco' => $this->endereco,
                'cidade' => $this->cidade, 'estado' => $this->estado, 'numero' => $this->numero, 'telefone' => $this->telefone, 'descricao' => $this->descricao,
                'banner' => $banner, 'id' => $consulta['id']]
            );
            $this->retorno['sucesso'] = self::CAMPO_ATUALIZACAO_SUCESSO;
            $this->retorno['banner'] = $banner;
            $base->fecharConexao();
        }
    }

    public function excluirEntidade() {
        $consulta = $this->verAutenticacao();
        $base = new Conexao;
        $consultar = $base->verConsulta('DELETE FROM tb_endereco WHERE usuario_id = :usuario_id', array('usuario_id'=> $consulta['id']));
        if($consultar) {
            $consultar = $base->verConsulta('DELETE FROM tb_telefone WHERE usuario_id = :usuario_id', array('usuario_id'=> $consulta['id']));
            if($consultar) {
                $consultar = $base->verConsulta('DELETE FROM tb_usuario WHERE id = :id', array('id'=> $consulta['id']));
                if($consultar) {
                    unset($_SESSION[$this->token]);
                    $this->retorno['sucesso'] = self::CAMPO_ENTIDADE_EXCLUSAO;
                }
            }
        }
        $base->fecharConexao();
    }
}
<?php
abstract class Notificacao {

    public const CAMPO_EM_BRANCO = 'Existem campos obrigatórios em branco.';
    public const CAMPO_EMAIL_MAX = 'O campo do Email deve conter entre 3 e 45 caracteres.';
    public const CAMPO_EMAIL_VALIDO = 'O email não é válido.';
    public const CAMPO_SENHA_MAX = 'O campo da Senha deve conter entre 6 e 45 caracteres.';
    public const CAMPO_SENHA_DIFERENTE = 'A senhas confirmadas não coincidem.';
    public const CAMPO_OCUPACAO_VALIDO = 'A ocupação não é válida.';
    public const CAMPO_NOME_MAX = 'O campo do Nome deve conter entre 3 e 20 caracteres.';
    public const CAMPO_SOBRENOME_MAX = 'O campo do Sobrenome deve conter entre 3 e 20 caracteres.';
    public const CAMPO_ENDERECO_MAX = 'O campo do Endereço deve conter entre 3 e 45 caracteres.';
    public const CAMPO_CIDADE_MAX = 'O campo da Cidade deve conter entre 3 e 45 caracteres.';
    public const CAMPO_ESTADO_MAX = 'O campo do Estado deve conter entre 2 e 45 caracteres.';
    public const CAMPO_NUMERO_MAX = 'O campo do Número deve conter entre 1 e 10 caracteres.';
    public const CAMPO_TELEFONE_MAX = 'O campo do Telefone deve conter entre 3 e 20 caracteres.';
    public const CAMPO_DESCRICAO_MAX = 'O campo da Descrição deve conter entre 10 e 1.000 caracteres.';
    public const CAMPO_BANNER_VALIDO = 'O banner não é válido.';
    public const CAMPO_BANNER_ERRO = 'Ocorreu um erro ao enviar o banner.';
    public const CAMPO_EMAIL_EXISTENTE = 'O email já existe no cadastro.';
    public const CAMPO_CADASTRO_SUCESSO = 'Cadastro efetuado com sucesso.';
    public const CAMPO_EMAIL_ERRO = 'Credenciais inválidas.';
    public const CAMPO_ATUALIZACAO_SUCESSO = 'Atualização feita com sucesso.';
    public const CAMPO_ENTIDADE_EXCLUSAO = 'A entidade foi excluída com sucesso.';
}
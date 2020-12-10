function pesquisarMercado(o,p = 1) {
    $('#search-result').hide();
    $('#search-result').html("");
    $('#paginacao').addClass('d-none');
    $.ajax({
        dataType: 'json',
        url: 'controles/pesquisar.php',
        type: 'POST',
        data: {
            post: $(o).val(),
            pag: p,
        },
        success: function(result) {
            $('#search-result').html("");
            $.each(result, function(key, value) {
                // append data to results
                var content = `
                    <!-- Grid column -->
                    <div class="col-lg-4 col-md-12 mb-5">
                        <!--Card-->
                        <div class="card">
                            <!--Card image-->
                            <div class="view">
                                <img src="uploads/${value['banner']}" class="card-img-top" alt="Banner" />
                                <a>
                                    <div class="mask rgba-white-slight waves-effect waves-light"></div>
                                </a>
                            </div>

                            <!--Card content-->
                            <div class="card-body">
                                <!--Title-->
                                <h4 class="card-title">${value['nome']} ${value['sobrenome']}</h4>
                                <!--Text-->
                                <p class="card-text">${value['endereco']}, ${value['cidade']}, ${value['estado']}, ${value['numero']}</p>
                                <button class="btn btn-primary visitar" type="button" onclick="visitar();page('perfil','/${value['id']}');">Visitar</button>
                            </div>
                        </div>
                        <!--/.Card-->
                    </div>
                    <!-- Grid column -->
                `;
                if(key != 'paginacao') {
                    $('#search-result').append(content);
                }
                else {
                    $('#proximo, #anterior, #primeiro, #ultimo').removeClass('disabled').removeAttr('onClick');
                    if(value['pagina'] <= 1) {
                        $('#anterior').addClass('disabled');
                        $('#primeiro').addClass('disabled');
                    }
                    else {
                        var subtracao = value['pagina'] - 1;
                        $('#anterior').attr('onClick', 'pesquisarMercado(\'#search-form\','+subtracao+')');
                        $('#primeiro').attr('onClick', 'pesquisarMercado(\'#search-form\',1)');
                    }
                    var multiplica = value['quantiaPorPagina'] * value['pagina'];
                    var divide = Math.ceil(value['quantidadeTotal'] / value['quantiaPorPagina']);
                    if(multiplica >= value['quantidadeTotal']) {
                        $('#proximo').addClass('disabled');
                        $('#ultimo').addClass('disabled');
                    }
                    else {
                        var soma = value['pagina'] + 1;
                        $('#proximo').attr('onClick', 'pesquisarMercado(\'#search-form\','+soma+')');
                        $('#ultimo').attr('onClick', 'pesquisarMercado(\'#search-form\','+divide+')');
                    }
                    if(value['pagina'] <= 1) {
                        value['pagina'] = 1;
                    }
                    var pg = '';
                    if(divide < value['pagina']) {
                        divide = value['pagina'];
                    }
                    var links_laterais =  Math.ceil(value['quantidadeLinks'] / 2);
                    var inicio = value['pagina'] - links_laterais;
                    if(inicio < 1) {
                        inicio = 1;
                    }
                    var limite = value['pagina'] + links_laterais;
                    if(limite > divide) {
                        limite = divide;
                    }
                    for(var i = inicio; i <= limite; i++) {
                        if(i == value['pagina']) {
                            pg +=`
                            <li class="page-item active">
                                <a class="page-link">${i}</a>
                            </li>
                            `;
                        }
                        else {
                            pg +=`
                            <li class="page-item" onclick="pesquisarMercado('#search-form',${i})">
                                <a class="page-link">${i}</a>
                            </li>
                            `;
                        }
                    }
                    $('#pagina').html(pg);
                }
            });
            // show results if not empty
            if ($('#search-result').html() != "") {
                $('#s_content').hide();
                $('#search-result').show();
                $('#pesquisa').addClass('mt-5');
                $('#paginacao').removeClass('d-none');
                $(window).scrollTop(0);
            }
            else {
                $('#s_content').show();
                $('#pesquisa').removeClass('mt-5');
                $('#paginacao').addClass('d-none');
            }
        },
        error: function(xhr, status, error) {
            if(xhr.status == 0) {
                toastr.error('Falha na conexão.');
            }
            else if(xhr.status == 403) {
                toastr.error('Erro 403');
            }
            else if(xhr.status == 404) {
                toastr.error('Erro 404');
            }
            else if(xhr.status == 500 || xhr.status == 503) {
                toastr.error('Erro 500');
            }
            else {
                toastr.error(xhr.responseText);
            }
        }
    }); 
}

function detectarPagina() {
    var pg = window.location.pathname.split("/");
    if(pg.length-1 > 2) {
        var s = 2;
        var i = '/' + pg[pg.length-1];
    }
    else {
        var s = 1;
        var i = '';
    }
    return pg[pg.length-1] == '' ? page('home','') : page(pg[pg.length-s],i);
}

$(document).ready(function() {
    page('nav','','#nav','componentes');
    page('footer','','#footer','componentes');
    detectarPagina();
    iniciarMdb();
    if (window.history && window.history.pushState) {
        $(window).on('popstate', function() {
            detectarPagina();
        });
    }
});

function iniciarMdb() {
    $('.mdb-select').materialSelect();
    $('.file-upload').file_upload({
        allowedFileExtensions: ["png", "jpg", "jpeg"],
        messages: {
            remove: "Remover",
            error: "Ops, algo deu errado."
        }
    });
    $('.file-upload').append('<div class="invalid-feedback">Por favor escolha uma imagem para o banner</div>');
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": false,
        "positionClass": "md-toast-top-right",
        "preventDuplicates": true,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }
}

function travar(d) {
    var texto = `
    <div class="ph-item mt-5 pt-5">
        <div class="ph-col-12">
            <div class="ph-picture"></div>
            <div class="ph-row">
                <div class="ph-col-6 big"></div>
                <div class="ph-col-4 empty big"></div>
                <div class="ph-col-2 big"></div>
                <div class="ph-col-4"></div>
                <div class="ph-col-8 empty"></div>
                <div class="ph-col-6"></div>
                <div class="ph-col-6 empty"></div>
                <div class="ph-col-12"></div>
            </div>
        </div>
    </div>
    `;
    $(d).html(texto);
}

function page(p,t = '',d = '#content',local = 'paginas') {
    carregando(false);
    travar(d);
    $.get(`${local}/${p}.html`)
    .done(function(data) {
        if(d == '#content') {
            history.pushState({}, null, p + ''+ t);
            $(window).scrollTop(0);
        }
        $(d).html(data);
    })
    .fail(function(xhr, status, error) {
        if(xhr.status == 0) {
            toastr.error('Falha na conexão.');
        }
        else if(xhr.status == 403) {
            toastr.error('Erro 403');
        }
        else if(xhr.status == 404) {
            toastr.error('Erro 404');
        }
        else if(xhr.status == 500 || xhr.status == 503) {
            toastr.error('Erro 500');
        }
        else {
            toastr.error(xhr.responseText);
        }
    })
}

function carregando(i) {
    if(i) {
        $('#content').hide();
        $('#carregando').removeClass('d-none');
    }
    else {
        $('#content').show();
        $('#carregando').addClass('d-none');
    }
}

function cadastro() {
    $('#btn').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Cadastrar').addClass('disabled');

    let formData = new FormData()
    var arquivo = $('#banner')[0].files[0];

    var lista = [{
        email: $('#email').val().trim(),
        senha: $('#senha').val().trim(),
        csenha: $('#csenha').val().trim(),
        ocupacao: $('#ocupacao').val().trim(),
        nome: $('#nome').val().trim(),
        sobrenome: $('#sobrenome').val().trim(),
        endereco: $('#endereco').val().trim(),
        cidade: $('#cidade').val().trim(),
        estado: $('#estado').val().trim(),
        numero: $('#numero').val().trim(),
        telefone: $('#telefone').val().trim(),
        descricao: $('#descricao').val().trim(),
    }];

    lista.forEach(function(element) {
        console.log(element);
    });

    formData.append('arquivo', arquivo);
    formData.append('lista', JSON.stringify(lista));

    $.ajax({
        dataType: 'json',
        url: 'controles/registrar.php',
        method: 'POST',
        contentType: false,
        processData: false,
        data: formData,
        success: function(response){
            if (response.erro) {
                toastr.error(response.erro);
                $('#btn').html('Cadastrar').removeClass('disabled');
            }
            else {
                if(response.sucesso) {
                    toastr.success(response.sucesso);
                    page('atualizacao');
                }
            }
        },
        error: function(xhr, status, error) {
            $('#btn').html('Cadastrar').removeClass('disabled');
            if(xhr.status == 0) {
                toastr.error('Falha na conexão.');
            }
            else if(xhr.status == 403) {
                toastr.error('Erro 403');
            }
            else if(xhr.status == 404) {
                toastr.error('Erro 404');
            }
            else if(xhr.status == 500 || xhr.status == 503) {
                toastr.error('Erro 500');
            }
            else {
                toastr.error(xhr.responseText);
            }
        }
    });
}

function visitar() {
    $('.visitar').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Visitar').addClass('disabled');
    $('#search-form').attr('disabled', 'disabled');
}

function ler(post = 1) {
    $.ajax({
        dataType: 'json',
        url: 'controles/ler.php',
        type: 'POST',
        data: {
            post
        },
        success: function(response) {
            if (response.erro) {
				toastr.error(response.erro);
            }
            else {
                if(response.sucesso) {
                    Object.entries(response.sucesso).forEach(([key, value]) => {
                        if(key == 'banner') {
                            $('[data-banner]').attr('src','uploads/' + value);
                        }
                        else if(key == 'ocupacao') {
                            var n = value == 'Pescador' ? 1 : 2;
                            $('#ocupacao option').removeAttr('selected').filter('[value='+n+']').attr('selected', true);
                        }
                        else {
                            $('#' + key).val(value).text(value);
                        }
                    });
                    $('.nome').text($('#nome').val());
                    $('.sobrenome').text($('#sobrenome').val());
                    console.log(response.sucesso);
                    $('.pesq').removeClass('d-none');
                    $('.cadastro').addClass('d-none');
                    $('.login').addClass('d-none');
                    $('.sair').removeClass('d-none');
                    carregando(false);
                    $('#modalLoginForm').modal('hide');
                    $('#modalLoginForm').on('hidden.bs.modal', function (e) {
                        $('.logar').html('Entrar').removeClass('disabled');
                        $('.close').html('<span aria-hidden="true">×</span>').removeClass('disabled');
                    });
                }
            }
        },
        error: function(xhr, status, error) {
            if(xhr.status == 0) {
                toastr.error('Falha na conexão.');
            }
            else if(xhr.status == 200 || xhr.status == 403) {
                page('home');
            }
            else if(xhr.status == 404) {
                toastr.error('Erro 404');
            }
            else if(xhr.status == 500 || xhr.status == 503) {
                toastr.error('Erro 500');
            }
            else {
                toastr.error(xhr.responseText);
            }
        }
    });
}

function lerHome(post = 1) {
    $.ajax({
        dataType: 'json',
        url: 'controles/ler.php',
        type: 'POST',
        data: {
            post
        },
        success: function(response) {
            if (response.erro) {
				toastr.error(response.erro);
            }
            else {
                if(response.sucesso) {
                    $('#nav-nome').text(response.sucesso['nome']);
                    $('.cadastro, .login').addClass('d-none');
                    $('.sair, .autenticado').removeClass('d-none');
                    $('.btn-orange').addClass('d-none');
                }
            }
        },
        error: function(xhr, status, error) {
            if(xhr.status == 0) {
                toastr.error('Falha na conexão.');
            }
            else if(xhr.status == 200 || xhr.status == 403) {
                $('.sair, .pesq, .autenticado').addClass('d-none');
                $('.cadastro, .login').removeClass('d-none');
                $('#frameModalBottom').modal('hide');
                $('.modal-backdrop').removeClass();
                $('#frameModalBottom').on('hidden.bs.modal', function (e) {
                    $('.excluir').html('Confirmar exclusão').removeClass('disabled');
                    $('.cancelar').removeClass('disabled');
                });
            }
            else if(xhr.status == 404) {
                toastr.error('Erro 404');
            }
            else if(xhr.status == 500 || xhr.status == 503) {
                toastr.error('Erro 500');
            }
            else {
                toastr.error(xhr.responseText);
            }
        }
    });
}

function lerCadastro(post = 1) {
    $.ajax({
        dataType: 'json',
        url: 'controles/ler.php',
        type: 'POST',
        data: {
            post
        },
        success: function(response) {
            if (response.erro) {
				toastr.error(response.erro);
            }
            else {
                if(response.sucesso) {
                    $('#nav-nome').text(response.sucesso['nome']);
                    $('.cadastro, .login').addClass('d-none');
                    $('.sair, .pesq, .autenticado').removeClass('d-none');
                    page('atualizacao');
                }
            }
        },
        error: function(xhr, status, error) {
            if(xhr.status == 0) {
                toastr.error('Falha na conexão.');
            }
            else if(xhr.status == 200 || xhr.status == 403) {
                $('.sair').addClass('d-none');
                $('.pesq, .login').removeClass('d-none');
            }
            else if(xhr.status == 404) {
                toastr.error('Erro 404');
            }
            else if(xhr.status == 500 || xhr.status == 503) {
                toastr.error('Erro 500');
            }
            else {
                toastr.error(xhr.responseText);
            }
        }
    });
}

function lerPerfil(post) {
    $.ajax({
        dataType: 'json',
        url: 'controles/visitar.php',
        type: 'POST',
        data: {
            post
        },
        success: function(response) {
            if (response.erro) {
				toastr.error(response.erro);
            }
            else {
                if(response.sucesso) {
                    Object.entries(response.sucesso).forEach(([key, value]) => {
                        if(key == 'banner') {
                            $('.card-img-top').attr('src','uploads/' + value);
                        }
                        else if(key == 'registro') {
                            var dateObj = new Date(value)
                            var dateString = dateObj.toLocaleString('pt-BR', {
                                year: 'numeric',
                                month: '2-digit',
                                day: '2-digit',
                                //hour: '2-digit',
                                //minute:'2-digit',
                                //second:'2-digit'
                            }).replace(/\//g, '-')
                            $('#registro').text(dateString);
                        }
                        else {
                            $('#' + key).text(value);
                        }
                    });
                    carregando(false);
                }
            }
        },
        error: function(xhr, status, error) {
            if(xhr.status == 0) {
                toastr.error('Falha na conexão.');
            }
            else if(xhr.status == 403) {
                toastr.error('Erro 403');
            }
            else if(xhr.status == 404) {
                toastr.error('Erro 404');
            }
            else if(xhr.status == 500 || xhr.status == 503) {
                toastr.error('Erro 500');
            }
            else {
                toastr.error(xhr.responseText);
            }
        }
    });
}

function sair(post = 1) {
    $('.sair').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Sair').addClass('disabled');
    $.ajax({
        dataType: 'json',
        url: 'controles/sair.php',
        type: 'POST',
        data: {
            post
        },
        success: function(response) {
            if (response.erro) {
                toastr.error(response.erro);
                $('.sair').html('<i class="fas fa-sign-out-alt"></i> Sair').removeClass('disabled');
            }
            else {
                if(response.sucesso) {
                    page('home');
                }
            }
        },
        error: function(xhr, status, error) {
            $('.sair').html('<i class="fas fa-sign-out-alt"></i> Sair').removeClass('disabled');
            if(xhr.status == 0) {
                toastr.error('Falha na conexão.');
            }
            else if(xhr.status == 403) {
                toastr.error('Erro 403');
            }
            else if(xhr.status == 404) {
                toastr.error('Erro 404');
            }
            else if(xhr.status == 500 || xhr.status == 503) {
                toastr.error('Erro 500');
            }
            else {
                toastr.error(xhr.responseText);
            }
        }
    });
}

function entrar() {
    $('.erro-login').hide();
    $('.logar').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Entrar').addClass('disabled');
    $('.close').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>').addClass('disabled');
    var post = {
        email: $('#defaultForm-email').val().trim(),
        senha: $('#defaultForm-pass').val().trim(),
    }
    $.ajax({
        dataType: 'json',
        url: 'controles/entrar.php',
        type: 'POST',
        data: {
            post
        },
        success: function(response) {
            if (response.erro) {
                $('.erro-login').text(response.erro).show();
                $('.logar').html('Entrar').removeClass('disabled');
                $('.close').html('<span aria-hidden="true">×</span>').removeClass('disabled');
            }
            else {
                if(response.sucesso) {
                    page('atualizacao');
                }
            }
        },
        error: function(xhr, status, error) {
            $('.logar').html('Entrar').removeClass('disabled');
            $('.close').html('<span aria-hidden="true">×</span>').removeClass('disabled');
            if(xhr.status == 0) {
                toastr.error('Falha na conexão.');
            }
            else if(xhr.status == 403) {
                toastr.error('Erro 403');
            }
            else if(xhr.status == 404) {
                toastr.error('Erro 404');
            }
            else if(xhr.status == 500 || xhr.status == 503) {
                toastr.error('Erro 500');
            }
            else {
                toastr.error(xhr.responseText);
            }
        }
    });
}

function atualizar() {
    $('#btn').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Atualizar').addClass('disabled');

    let formData = new FormData()
    var arquivo = $('#banner')[0].files[0];

    var lista = [{
        email: $('#email').val().trim(),
        senha: $('#senha').val().trim(),
        csenha: $('#csenha').val().trim(),
        asenha: $('#asenha').val().trim(),
        ocupacao: $('#ocupacao').val().trim(),
        nome: $('#nome').val().trim(),
        sobrenome: $('#sobrenome').val().trim(),
        endereco: $('#endereco').val().trim(),
        cidade: $('#cidade').val().trim(),
        estado: $('#estado').val().trim(),
        numero: $('#numero').val().trim(),
        telefone: $('#telefone').val().trim(),
        descricao: $('#descricao').val().trim(),
    }];

    lista.forEach(function(element) {
        console.log(element);
    });

    formData.append('arquivo', arquivo);
    formData.append('lista', JSON.stringify(lista));

    $.ajax({
        dataType: 'json',
        url: 'controles/atualizar.php',
        method: 'POST',
        contentType: false,
        processData: false,
        data: formData,
        success: function(response){
            if (response.erro) {
                toastr.error(response.erro);
            }
            else {
                if(response.sucesso) {
                    toastr.success(response.sucesso);
                    $('.nome').text($('#nome').val().trim());
                    $('.sobrenome').text($('#sobrenome').val().trim());
                    if(arquivo) {
                        $('[data-banner]').attr('src','uploads/' + response.banner);
                    }
                }
            }
            $('#btn').html('Atualizar').removeClass('disabled');
        },
        error: function(xhr, status, error) {
            $('#btn').html('Atualizar').removeClass('disabled');
            if(xhr.status == 0) {
                toastr.error('Falha na conexão.');
            }
            else if(xhr.status == 403) {
                toastr.error('Erro 403');
            }
            else if(xhr.status == 404) {
                toastr.error('Erro 404');
            }
            else if(xhr.status == 500 || xhr.status == 503) {
                toastr.error('Erro 500');
            }
            else {
                toastr.error(xhr.responseText);
            }
        }
    });
}

function excluir(post = 1) {
    $('.excluir').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Confirmar exclusão').addClass('disabled');
    $('.cancelar').addClass('disabled');
    $.ajax({
        dataType: 'json',
        url: 'controles/excluir.php',
        type: 'POST',
        data: {
            post
        },
        success: function(response) {
            if (response.erro) {
                toastr.error(response.erro);
                $('.excluir').html('Confirmar exclusão').removeClass('disabled');
                $('.cancelar').removeClass('disabled');
            }
            else {
                if(response.sucesso) {
                    toastr.success(response.sucesso);
                    page('home');
                }
            }
        },
        error: function(xhr, status, error) {
            $('.excluir').html('Confirmar exclusão').removeClass('disabled');
            $('.cancelar').removeClass('disabled');
            if(xhr.status == 0) {
                toastr.error('Falha na conexão.');
            }
            else if(xhr.status == 403) {
                toastr.error('Erro 403');
            }
            else if(xhr.status == 404) {
                toastr.error('Erro 404');
            }
            else if(xhr.status == 500 || xhr.status == 503) {
                toastr.error('Erro 500');
            }
            else {
                toastr.error(xhr.responseText);
            }
        }
    });
}
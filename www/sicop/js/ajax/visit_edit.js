$(function(){

    var caminho     = $('#js_caminho').val();
    var caminho_img = $('#js_caminho_img').val();

    //pre carregando o gif
    var loading = new Image();
    loading.src = caminho_img + 'system/loading.gif';
    var img_cont = '<p class="img_ajax"><img src="' + caminho_img + 'system/loading.gif" /></p>';

    $("#form_error").hide();

    // validação e submit do form
    $("#visit_up").live("submit", function() {

        var retorno = true;
        var err_msg = '';
        var content = $('#load_icon');

        // colocando uma imagem de loading
        content.html( img_cont );

        // capturar os dados do formulário
        var data = $(this).serializeArray();

        // bloquear o formulário
        $.lock_form_disable ( $(this) );

//        if ( retorno ) {
//            var nome_visit = $('#nome_visit').val();
//            if ( nome_visit == '' ){
//                err_msg = 'Informe o nome do visitante.';
//                retorno = false;
//            }
//        }
//
//        if ( retorno ) {
//
//            var sexo_visit = $("input[name='sexo_visit']:checked").val();
//
//            if ( sexo_visit == '' || sexo_visit == undefined ){
//                err_msg = 'Informe o sexo do visitante.';
//                retorno = false;
//            }
//
//        }
//
//        if ( retorno ) {
//
//            //alert( "retorno da função " + $.ck_rgv_exist() );
//
//            if ( $.ck_rgv_exist() ){
//                err_msg = 'Este RG já está cadastrado! Verifique!';
//                retorno = false;
//            }
//
//        }
//
//        if ( !retorno ) { // se não tiver validado
//            $("#form_error").fadeIn( 1000 );   // mostrar o paragrafo de erro
//            $("#form_error").html( err_msg );  // troca a mensagem de erro
//            $.unlock_form ( $(this) );         // desbloquear o formulário
//            $("#nome_visit").focus();          // foco no campo nome
//            content.html( '' );
//            return false;
//        }

        $.ajax({
            type   : "POST",
            cache  : false,
            url    : caminho + "send/sendvisit.php",
            data   : data,
            success: function( data ) {

                if ( data == 1 ) { // se retornar 1 obteve sucesso
                    history.go(-1);
                } else { // se não, mostra a mensagem
                    $.unlock_form ( $(this) );
                    $.fancybox( "Ocorreu um erro!!!" );
                }// /if ( data == 1 ) {

            } // /success: function( data ) {

        }); // /$.ajax({

        return false;

    }); // /$("#form_rc").live("submit", function() {


    var container = $('div.cont_validator_error');

    var val = $('#visit_up').validate({
        errorContainer: container,
        errorLabelContainer: $( container ),
        wrapper: 'li',
        //meta: "validate",
        errorClass: "validator_error",
        rules: {
            nome_visit: {
                required: true,
                minlength: 5
            },
            sexo_visit: {
                required: true
            },
            rg_visit: {
                rg_exist: true
            }

        },
        messages:{
            nome_visit:{
                required: "Digite o nome do visitante",
                minLength: "O nome do visitante deve conter, no mínimo, 2 caracteres"
            },
            sexo_visit:{
                required: "Escolha o sexo do visitante"
            }
        }

    });

});// /$(function(){

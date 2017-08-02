$("#etka_contato").submit(function(event){
    if (event.isDefaultPrevented()) {
        // handle the invalid form...
    } else {
        // everything looks good!
        event.preventDefault();
        submitForm();
    }
});

function submitForm(){
    // Initiate Variables With Form Content
    var nome = $("#contato-nome").val();
    var email = $("#contato-email").val();
    var empresa = $("#contato-empresa").val();
    var tel = $("#contato-tel").val();
    var assunto = $("#contato-assunto").val();
    var mensagem = $("#contato-mensagem").val();
 
    $.ajax({
        type: "POST",
        url: "./contato/contato.php",
        data: "contato-nome=" + nome + "&contato-email=" + email + "&contato-empresa=" + empresa + "&contato-tel=" + tel + "&contato-assunto=" + assunto + "&contato-mensagem=" + mensagem,
        success : function(text){
            if (text == "success"){
                formSuccess();
            } else {
                formError();
            }
        }
    });
}

function formSuccess(){
    $( "#etka_contato-enviado" ).removeClass( "etka_form-enviado" );
}

function formSuccess(){
    $( "#etka_contato-erro" ).removeClass( "etka_form-erro" );
}


$("#etka_contato").submit(function(event){
    var email = document.forms["etka_contato-mobile"]["m_contato-email"].value;
    var contato_email = document.getElementById("m_contato-email");
    var mensagem = document.forms["etka_contato-mobile"]["m_contato-mensagem"].value;
    var contato_msg = document.getElementById("m_contato-mensagem");
    var att = document.createAttribute("required");
    if (email == "") {
        contato_email.setAttributeNode(att);
        return false;
        // handle the invalid form...
        contatoMError();
    }
    if (mensagem == "") {
        contato_msg.setAttributeNode(att);
        return false;
        // handle the invalid form...
        contatoMError();
    } 
    if (event.isDefaultPrevented()) {
        // handle the invalid form...
        contatoMError();
    } else {
        // everything looks good!
        event.preventDefault();
        submitMContato();
    }
});

function submitMContato(){
    // Initiate Variables With Form Content
    var nome = $("#m_contato-nome").val();
    var email = $("#m_contato-email").val();
    var empresa = $("#m_contato-empresa").val();
    var telefone = $("#m_contato-tel").val();
    var assunto = $("#m_contato-assunto").val();
    var mensagem = $("#m_contato-mensagem").val();
 
    $.ajax({
        type: "POST",
        url: "./contato/mobile/contato.php",
        data: "m_contato-nome=" + nome + "&m_contato-email=" + email + "&m_contato-empresa=" + empresa + "&m_contato-tel=" + telefone + "&m_contato-assunto=" + assunto + "&m_contato-mensagem=" + mensagem,
        success : function(text){
            if (text == "success"){
                contatoMSuccess();
            } else {
                contatoMError();
            }
        }
    });
}

function contatoMSuccess(){
    $( "#etka_contato-mobile-enviado" ).removeClass( "etka_contato-mobile-enviado" );
    $( '#m_contato-nome, #m_contato-email, #m_contato-empresa, #m_contato-tel, #m_contato-assunto, #m_contato-mensagem' ).val('');
}

function contatoMError(){
    $( "#etka_contato-mobile-erro" ).removeClass( "etka_contato-mobile-erro" );
}


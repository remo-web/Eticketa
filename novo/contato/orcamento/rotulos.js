$("#etka_o-rotulo").submit(function(event){
    var email = document.forms["etka_o-rotulos"]["o_rotulos-email"].value;
    var o_rotulos_email = document.getElementById("o_rotulos-email");
    var mensagem = document.forms["etka_o-rotulos"]["o_rotulos-mensagem"].value;
    var o_rotulos_msg = document.getElementById("o_rotulos-mensagem");
    var att = document.createAttribute("required");
    if (email == "") {
        o_rotulos_email.setAttributeNode(att);
        return false;
        // handle the invalid form...
        o_rotulosError();
    }
    if (mensagem == "") {
        o_rotulos_msg.setAttributeNode(att);
        return false;
        // handle the invalid form...
        o_rotulosError();
    } 
    if (event.isDefaultPrevented()) {
        // handle the invalid form...
        o_rotulosError();
    } else {
        // everything looks good!
        event.preventDefault();
        submitO_rotulos();
    }
});

function submitO_rotulos(){
    // Initiate Variables With Form Content
    var nome = $("#o_rotulos-nome").val();
    var email = $("#o_rotulos-email").val();
    var empresa = $("#o_rotulos-empresa").val();
    var telefone = $("#o_rotulos-telefone").val();
    var largura = $("#o_rotulos-largura").val();
    var altura = $("#o_rotulos-altura").val();
    var formato = $("#o_rotulos-formato").val();
    var quantidade = $("#o_rotulos-quantidade").val();
    var frente = $("#o_rotulos-frente").val();
    var verso = $("#o_rotulos-verso").val();
    var finalidade = $("#o_rotulos-finalidade").val();
    var mensagem = $("#o_rotulos-mensagem").val();
 
    $.ajax({
        type: "POST",
        url: "./contato/orcamento/rotulos.php",
        data: "o_rotulos-nome=" + nome + "&o_rotulos-email=" + email + "&o_rotulos-empresa=" + empresa + "&o_rotulos-telefone=" + telefone + "&o_rotulos-largura=" + largura + "&o_rotulos-altura=" + altura + "&o_rotulos-formato=" + formato + "&o_rotulos-quantidade=" + quantidade + "&o_rotulos-frente=" + frente + "&o_rotulos-verso=" + verso + "&o_rotulos-finalidade=" + finalidade + "&o_rotulos-mensagem=" + mensagem,
        success : function(text){
            if (text == "success"){
                o_rotulosSuccess();
            } else {
                o_rotulosError();
            }
        }
    });
}

function o_rotulosSuccess(){
    $( "#etka_o-rotulos-enviado" ).removeClass( "etka_o-rotulos-enviado" );
    $( '#o_rotulos-nome, #o_rotulos-email, #o_rotulos-empresa, #o_rotulos-telefone, #o_rotulos-largura, #o_rotulos-altura, #o_rotulos-formato, #o_rotulos-quantidade, #o_rotulos-frente, #o_rotulos-verso, #o_rotulos-finalidade, #o_rotulos-mensagem' ).val('');
}

function o_rotulosError(){
    $( "#etka_o-rotulos-erro" ).removeClass( "etka_o-rotulos-erro" );
}


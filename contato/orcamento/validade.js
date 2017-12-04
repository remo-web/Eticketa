$("#etka_o-validade").submit(function(event){
    var email = document.forms["etka_o-validade"]["o_validade-email"].value;
    var o_validade_email = document.getElementById("o_validade-email");
    var quantidade = document.forms["etka_o-validade"]["o_validade-quantidade"].value;
    var o_validade_quantidade = document.getElementById("o_validade-quantidade");
    //var mensagem = document.forms["etka_o-validade"]["o_validade-mensagem"].value;
    //var o_validade_msg = document.getElementById("o_validade-mensagem");
    var att = document.createAttribute("required");
    if (email == "") {
        o_validade_email.setAttributeNode(att);
        return false;
        // handle the invalid form...
        o_validadeError();
    }
    if (quantidade == "") {
        o_validade_quantidade.setAttributeNode(att);
        return false;
        // handle the invalid form...
        o_validadeError();
    }
    //if (mensagem == "") {
      //  o_validade_msg.setAttributeNode(att);
      //  return false;
        // handle the invalid form...
      //  o_validadeError();
   // } 
    if (event.isDefaultPrevented()) {
        // handle the invalid form...
        o_validadeError();
    } else {
        // everything looks good!
        event.preventDefault();
        submitO_validade();
    }
});

function submitO_validade(){
    // Initiate Variables With Form Content
    var nome = $("#o_validade-nome").val();
    var email = $("#o_validade-email").val();
    var empresa = $("#o_validade-empresa").val();
    var telefone = $("#o_validade-telefone").val();
    var custom = $("input[name=o_validade-custom]:checked").val();
    var largura = $("#o_validade-largura").val();
    var altura = $("#o_validade-altura").val();
    var formato = $("input[name=o_validade-formato]:checked").val();
    var quantidade = $("#o_validade-quantidade").val();
    var frente = $("#o_validade-frente").val();
    var verso = $("#o_validade-verso").val();
    var finalidade = $("#o_validade-finalidade").val();
    var mensagem = $("#o_validade-mensagem").val();
 
    $.ajax({
        type: "POST",
        url: "./contato/orcamento/validade.php",
        data: "o_validade-nome=" + nome + "&o_validade-email=" + email + "&o_validade-empresa=" + empresa + "&o_validade-telefone=" + telefone + "&o_validade-custom=" + custom + "&o_validade-largura=" + largura + "&o_validade-altura=" + altura + "&o_validade-formato=" + formato + "&o_validade-quantidade=" + quantidade + "&o_validade-frente=" + frente + "&o_validade-verso=" + verso + "&o_validade-finalidade=" + finalidade + "&o_validade-mensagem=" + mensagem,
        success : function(text){
            if (text == "success"){
                o_validadeSuccess();
            } else {
                o_validadeError();
            }
        }
    });
}

function o_validadeSuccess(){
    $( "#etka_o-validade-enviado" ).removeClass( "etka_orcamento-enviado" );
    $( '#o_validade-nome, #o_validade-email, #o_validade-empresa, #o_validade-telefone, #o_validade-custom, #o_validade-largura, #o_validade-altura, #o_validade-formato, #o_validade-quantidade, #o_validade-frente, #o_validade-verso, #o_validade-finalidade, #o_validade-mensagem' ).val('');
}

function o_validadeError(){
    $( "#etka_o-validade-erro" ).removeClass( "etka_orcamento-erro" );
}


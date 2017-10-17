$("#etka_p-validade").submit(function(event){
    var email = document.forms["etka_p-validade"]["p_validade-email"].value;
    var p_validade_email = document.getElementById("p_validade-email");
    //var mensagem = document.forms["etka_p-validade"]["p_validade-mensagem"].value;
    //var p_validade_msg = document.getElementById("p_validade-mensagem");
    var att = document.createAttribute("required");
    if (email == "") {
        p_validade_email.setAttributeNode(att);
        return false;
        // handle the invalid form...
        p_validadeError();
    }
    //if (mensagem == "") {
      //  p_validade_msg.setAttributeNode(att);
      //  return false;
        // handle the invalid form...
      //  p_validadeError();
   // } 
    if (event.isDefaultPrevented()) {
        // handle the invalid form...
        p_validadeError();
    } else {
        // everything looks good!
        event.preventDefault();
        submitP_validade();
    }
});

function submitP_validade(){
    // Initiate Variables With Form Content
    var nome = $("#p_validade-nome").val();
    var email = $("#p_validade-email").val();
    var telefone = $("#p_validade-telefone").val();
    var quantidade = $("#etka_preco-validade_slider-label_input").val();
    var endereco = $("#p_validade-endereco").val();
 
    $.ajax({
        type: "POST",
        url: "./contato/precos/validade.php",
        data: "p_validade-nome=" + nome + "&p_validade-email=" + email + "&p_validade-telefone=" + telefone + "&etka_preco-validade_slider-label_input=" + quantidade + "&p_validade-endereco=" + endereco,
        success : function(text){
            if (text == "success"){
                p_validadeSuccess();
            } else {
                p_validadeError();
            }
        }
    });
}

function p_validadeSuccess(){
    $( "#etka_p-validade-enviado" ).removeClass( "etka_p-validade-enviado" );
    $( '#p_validade-nome, #p_validade-email, #p_validade-telefone, #etka_preco-validade_slider-label_input, #p_validade-endereco' ).val('');
}

function p_validadeError(){
    $( "#etka_p-validade-erro" ).removeClass( "etka_p-validade-erro" );
}


$("#etka_p-ingresso").submit(function(event){
    var email = document.forms["etka_p-ingresso"]["p_ingresso-email"].value;
    var p_ingresso_email = document.getElementById("p_ingresso-email");
    //var mensagem = document.forms["etka_p-ingresso"]["p_ingresso-mensagem"].value;
    //var p_ingresso_msg = document.getElementById("p_ingresso-mensagem");
    var att = document.createAttribute("required");
    if (email == "") {
        p_ingresso_email.setAttributeNode(att);
        return false;
        // handle the invalid form...
        p_ingressoError();
    }
    //if (mensagem == "") {
      //  p_ingresso_msg.setAttributeNode(att);
      //  return false;
        // handle the invalid form...
      //  p_ingressoError();
   // } 
    if (event.isDefaultPrevented()) {
        // handle the invalid form...
        p_ingressoError();
    } else {
        // everything looks good!
        event.preventDefault();
        submitP_ingresso();
    }
});

function submitP_ingresso(){
    // Initiate Variables With Form Content
    var nome = $("#p_ingresso-nome").val();
    var email = $("#p_ingresso-email").val();
    var telefone = $("#p_ingresso-telefone").val();
    var quantidade = $("#etka_preco-ingresso_slider-label_input").val();
    var endereco = $("#p_ingresso-endereco").val();
 
    $.ajax({
        type: "POST",
        url: "./contato/precos/ingresso.php",
        data: "p_ingresso-nome=" + nome + "&p_ingresso-email=" + email + "&p_ingresso-telefone=" + telefone + "&etka_preco-ingresso_slider-label_input=" + quantidade + "&p_ingresso-endereco=" + endereco,
        success : function(text){
            if (text == "success"){
                p_ingressoSuccess();
            } else {
                p_ingressoError();
            }
        }
    });
}

function p_ingressoSuccess(){
    $( "#etka_p-ingresso-enviado" ).removeClass( "etka_precos-enviado" );
    $( '#p_ingresso-nome, #p_ingresso-email, #p_ingresso-telefone, #etka_preco-ingresso_slider-label_input, #p_ingresso-endereco' ).val('');
}

function p_ingressoError(){
    $( "#etka_p-ingresso-erro" ).removeClass( "etka_precos-erro" );
}


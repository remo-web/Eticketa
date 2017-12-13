$("#etka_o-ingressos").submit(function(event){
    var email = document.forms["etka_o-ingressos"]["o_ingressos-email"].value;
    var o_ingressos_email = document.getElementById("o_ingressos-email");
    //var mensagem = document.forms["etka_o-ingressos"]["o_ingressos-mensagem"].value;
    //var o_ingressos_msg = document.getElementById("o_ingressos-mensagem");
    var att = document.createAttribute("required");
    if (email == "") {
        o_ingressos_email.setAttributeNode(att);
        return false;
        // handle the invalid form...
        o_ingressosError();
    }
    //if (mensagem == "") {
      //  o_ingressos_msg.setAttributeNode(att);
      //  return false;
        // handle the invalid form...
      //  o_ingressosError();
   // } 
    if (event.isDefaultPrevented()) {
        // handle the invalid form...
        o_ingressosError();
    } else {
        // everything looks good!
        event.preventDefault();
        submitO_ingressos();
    }
});

function submitO_ingressos(){
    // Initiate Variables With Form Content
    var nome = $("#o_ingressos-nome").val();
    var email = $("#o_ingressos-email").val();
    var empresa = $("#o_ingressos-empresa").val();
    var telefone = $("#o_ingressos-telefone").val();
//    var largura = $("#o_ingressos-largura").val();
//    var altura = $("#o_ingressos-altura").val();
    var formato = $("input[name=o_balanca-formato]:checked").val();
    var picote = $("#o_balanca-picote:checked").serialize();
    var quantidade = $("#o_ingressos-quantidade").val();
    var frente = $("#o_ingressos-frente").val();
    var verso = $("#o_ingressos-verso").val();
    var finalidade = $("#o_ingressos-finalidade").val();
    var mensagem = $("#o_ingressos-mensagem").val();
 
    $.ajax({
        type: "POST",
        url: "./contato/orcamento/ingressos.php",
        data: "o_ingressos-nome=" + nome + "&o_ingressos-email=" + email + "&o_ingressos-empresa=" + empresa + "&o_ingressos-telefone=" + telefone + "&o_balanca-formato=" + formato + "&o_balanca-picote=" + picote + "&o_ingressos-quantidade=" + quantidade + "&o_ingressos-frente=" + frente + "&o_ingressos-verso=" + verso + "&o_ingressos-finalidade=" + finalidade + "&o_ingressos-mensagem=" + mensagem,
        success : function(text){
            if (text == "success"){
                o_ingressosSuccess();
            } else {
                o_ingressosError();
            }
        }
    });
}

function o_ingressosSuccess(){
    $( "#etka_o-ingressos-enviado" ).removeClass( "etka_orcamento-enviado" );
    $( '#o_ingressos-nome, #o_ingressos-email, #o_ingressos-empresa, #o_ingressos-telefone, #o_balanca-formato, #o_balanca-picote, #o_ingressos-quantidade, #o_ingressos-frente, #o_ingressos-verso, #o_ingressos-finalidade, #o_ingressos-mensagem' ).val('');
}

function o_ingressosError(){
    $( "#etka_o-ingressos-erro" ).removeClass( "etka_orcamento-erro" );
}


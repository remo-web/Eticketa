$("#etka_p-lacres").submit(function(event){
    var email = document.forms["etka_p-lacres"]["p_lacres-email"].value;
    var o_lacres_email = document.getElementById("p_lacres-email");
    //var mensagem = document.forms["etka_p-lacres"]["p_lacres-mensagem"].value;
    //var o_lacres_msg = document.getElementById("p_lacres-mensagem");
    var att = document.createAttribute("required");
    if (email == "") {
        o_lacres_email.setAttributeNode(att);
        return false;
        // handle the invalid form...
        o_lacresError();
    }
    //if (mensagem == "") {
      //  o_lacres_msg.setAttributeNode(att);
      //  return false;
        // handle the invalid form...
      //  o_lacresError();
   // } 
    if (event.isDefaultPrevented()) {
        // handle the invalid form...
        o_lacresError();
    } else {
        // everything looks good!
        event.preventDefault();
        submitO_lacres();
    }
});

function submitO_lacres(){
    // Initiate Variables With Form Content
    var nome = $("#p_lacres-nome").val();
    var email = $("#p_lacres-email").val();
    var telefone = $("#p_lacres-telefone").val();
    var quantidade = $("#p_lacres-quantidade").val();
    var endereco = $("#p_lacres-endereco").val();
 
    $.ajax({
        type: "POST",
        url: "./contato/precos/lacres.php",
        data: "p_lacres-nome=" + nome + "&p_lacres-email=" + email + "&p_lacres-telefone=" + telefone + "&p_lacres-quantidade=" + quantidade + "&p_lacres-endereco=" + mensagem,
        success : function(text){
            if (text == "success"){
                o_lacresSuccess();
            } else {
                o_lacresError();
            }
        }
    });
}

function o_lacresSuccess(){
    $( "#etka_p-lacres-enviado" ).removeClass( "etka_p-lacres-enviado" );
    $( '#p_lacres-nome, #p_lacres-email, #p_lacres-telefone, #p_lacres-quantidade, #p_lacres-endereco' ).val('');
}

function o_lacresError(){
    $( "#etka_p-lacres-erro" ).removeClass( "etka_p-lacres-erro" );
}


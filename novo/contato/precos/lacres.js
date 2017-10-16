$("#etka_p-lacres").submit(function(event){
    var email = document.forms["etka_p-lacres"]["p_lacres-email"].value;
    var p_lacres_email = document.getElementById("p_lacres-email");
    //var mensagem = document.forms["etka_p-lacres"]["p_lacres-mensagem"].value;
    //var p_lacres_msg = document.getElementById("p_lacres-mensagem");
    var att = document.createAttribute("required");
    if (email == "") {
        p_lacres_email.setAttributeNode(att);
        return false;
        // handle the invalid form...
        p_lacresError();
    }
    //if (mensagem == "") {
      //  p_lacres_msg.setAttributeNode(att);
      //  return false;
        // handle the invalid form...
      //  p_lacresError();
   // } 
    if (event.isDefaultPrevented()) {
        // handle the invalid form...
        p_lacresError();
    } else {
        // everything looks good!
        event.preventDefault();
        submitP_lacres();
    }
});

function submitP_lacres(){
    // Initiate Variables With Form Content
    var nome = $("#p_lacres-nome").val();
    var email = $("#p_lacres-email").val();
    var telefone = $("#p_lacres-telefone").val();
    var quantidade = $("#etka_preco-delivery_slider-label_input").val();
    var endereco = $("#p_lacres-endereco").val();
 
    $.ajax({
        type: "POST",
        url: "./contato/precos/lacres.php",
        data: "p_lacres-nome=" + nome + "&p_lacres-email=" + email + "&p_lacres-telefone=" + telefone + "&etka_preco-delivery_slider-label_input=" + quantidade + "&p_lacres-endereco=" + endereco,
        success : function(text){
            if (text == "success"){
                p_lacresSuccess();
            } else {
                p_lacresError();
            }
        }
    });
}

function p_lacresSuccess(){
    $( "#etka_p-lacres-enviado" ).removeClass( "etka_p-lacres-enviado" );
    $( '#p_lacres-nome, #p_lacres-email, #p_lacres-telefone, #etka_preco-delivery_slider-label_input, #p_lacres-endereco' ).val('');
}

function p_lacresError(){
    $( "#etka_p-lacres-erro" ).removeClass( "etka_p-lacres-erro" );
}


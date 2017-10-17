$("#etka_p-garantia").submit(function(event){
    var email = document.forms["etka_p-garantia"]["p_garantia-email"].value;
    var p_garantia_email = document.getElementById("p_garantia-email");
    //var mensagem = document.forms["etka_p-garantia"]["p_garantia-mensagem"].value;
    //var p_garantia_msg = document.getElementById("p_garantia-mensagem");
    var att = document.createAttribute("required");
    if (email == "") {
        p_garantia_email.setAttributeNode(att);
        return false;
        // handle the invalid form...
        p_garantiaError();
    }
    //if (mensagem == "") {
      //  p_garantia_msg.setAttributeNode(att);
      //  return false;
        // handle the invalid form...
      //  p_garantiaError();
   // } 
    if (event.isDefaultPrevented()) {
        // handle the invalid form...
        p_garantiaError();
    } else {
        // everything looks good!
        event.preventDefault();
        submitP_garantia();
    }
});

function submitP_garantia(){
    // Initiate Variables With Form Content
    var nome = $("#p_garantia-nome").val();
    var email = $("#p_garantia-email").val();
    var telefone = $("#p_garantia-telefone").val();
    var quantidade = $("#etka_preco-garantia_slider-label_input").val();
    var endereco = $("#p_garantia-endereco").val();
 
    $.ajax({
        type: "POST",
        url: "./contato/precos/garantia.php",
        data: "p_garantia-nome=" + nome + "&p_garantia-email=" + email + "&p_garantia-telefone=" + telefone + "&etka_preco-garantia_slider-label_input=" + quantidade + "&p_garantia-endereco=" + endereco,
        success : function(text){
            if (text == "success"){
                p_garantiaSuccess();
            } else {
                p_garantiaError();
            }
        }
    });
}

function p_garantiaSuccess(){
    $( "#etka_p-garantia-enviado" ).removeClass( "etka_p-garantia-enviado" );
    $( '#p_garantia-nome, #p_garantia-email, #p_garantia-telefone, #etka_preco-garantia_slider-label_input, #p_garantia-endereco' ).val('');
}

function p_garantiaError(){
    $( "#etka_p-garantia-erro" ).removeClass( "etka_p-garantia-erro" );
}


$("#etka_o-lacres").submit(function(event){
    var email = document.forms["etka_o-lacres"]["o_lacres-email"].value;
    var o_lacres_email = document.getElementById("o_lacres-email");
    //var mensagem = document.forms["etka_o_lacres"]["o_lacres-mensagem"].value;
    //var o_lacres_msg = document.getElementById("o_lacres-mensagem");
    var att = document.createAttribute("required");
    if (email == "") {
        o_lacres_email.setAttributeNode(att);
        return false;
        // handle the invalid form...
        o_lacresError();
    }
   // if (mensagem == "") {
       //o_lacres_msg.setAttributeNode(att);
        //return false;
        // handle the invalid form...
       // o_lacresError();
    //} 
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
    var nome = $("#o_lacres-nome").val();
    var email = $("#o_lacres-email").val();
    var empresa = $("#o_lacres-empresa").val();
    var telefone = $("#o_lacres-telefone").val();
    var largura = $("#o_lacres-largura").val();
    var altura = $("#o_lacres-altura").val();
    var formato = $("input[name=o_lacres-formato]:checked").val();
    var quantidade = $("#o_lacres-quantidade").val();
    var frente = $("#o_lacres-frente").val();
    var verso = $("#o_lacres-verso").val();
    var finalidade = $("#o_lacres-finalidade").val();
    var mensagem = $("#o_lacres-mensagem").val();
 
    $.ajax({
        type: "POST",
        url: "./contato/orcamento/lacres.php",
        data: "o_lacres-nome=" + nome + "&o_lacres-email=" + email + "&o_lacres-empresa=" + empresa + "&o_lacres-telefone=" + telefone + "&o_lacres-largura=" + largura + "&o_lacres-altura=" + altura + "&o_lacres-formato=" + formato + "&o_lacres-quantidade=" + quantidade + "&o_lacres-frente=" + frente + "&o_lacres-verso=" + verso + "&o_lacres-finalidade=" + finalidade + "&o_lacres-mensagem=" + mensagem,
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
    $( "#etka_o-lacres-enviado" ).removeClass( "etka_o-lacres-enviado" );
    $( '#o_lacres-nome, #o_lacres-email, #o_lacres-empresa, #o_lacres-telefone, #o_lacres-largura, #o_lacres-altura, #o_lacres-formato, #o_lacres-quantidade, #o_lacres-frente, #o_lacres-verso, #o_lacres-finalidade, #o_lacres-mensagem' ).val('');
}

function o_lacresError(){
    $( "#etka_o-lacres-erro" ).removeClass( "etka_o-lacres-erro" );
}


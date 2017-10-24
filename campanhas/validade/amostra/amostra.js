$(document).ready(function() {
    $(".etka_card__close").click(function() {
        $(".etka_popup-card").addClass("etka_popup-enviado");
    });
});

$("#etka_validade-amostra").submit(function(event){
    var email = document.forms["etka_validade-amostra"]["validade_email"].value;
    var contato_email = document.getElementById("validade_email");
    var endereco = document.forms["etka_validade-amostra"]["validade_endereco"].value;
    var contato_end = document.getElementById("validade_endereco");
    var att = document.createAttribute("required");
    if (email == "") {
        contato_email.setAttributeNode(att);
        return false;
        // handle the invalid form...
        validadeAError();
    }
    if (endereco == "") {
        contato_end.setAttributeNode(att);
        return false;
        // handle the invalid form...
        validadeAError();
    } 
    if (event.isDefaultPrevented()) {
        // handle the invalid form...
        validadeAError();
    } else {
        // everything looks good!
        event.preventDefault();
        submitAValidade();
    }
});

function submitAValidade(){
    // Initiate Variables With Form Content
    var nome = $("#validade_nome").val();
    var email = $("#validade_email").val();
    var empresa = $("#validade_empresa").val();
    var telefone = $("#validade_tel").val();
    var endereco = $("#validade_endereco").val();
    var mensagem = $("#validade_mensagem").val();
 
    $.ajax({
        type: "POST",
        url: "./amostra.php",
        data: "validade_nome=" + nome + "&validade_email=" + email + "&validade_empresa=" + empresa + "&validade_tel=" + telefone + "&validade_endereco=" + endereco + "&validade_mensagem=" + mensagem,
        success : function(text){
            if (text == "success"){
                validadeASuccess();
            } else {
                validadeAError();
            }
        }
    });
}

function validadeASuccess(){
    $( "#etka_validade_enviado" ).removeClass( "etka_popup-enviado" );
    $( '#validade_nome, #validade_email, #validade_empresa, #validade_tel, #validade_endereco, #validade_mensagem' ).val('');
}

function validadeAError(){
    $( "#etka_validade_erro" ).removeClass( "etka_popup-erro" );
}


$(document).ready(function() {
    $(".etka_card__close").click(function() {
        $(".etka_popup-card").addClass("etka_popup-enviado");
    });
});

$("#etka_validade-amostra").submit(function(event){
    var email = document.forms["etka_validade-amostra"]["validade_a-email"].value;
    var contato_email = document.getElementById("validade_a-email");
    var endereco = document.forms["etka_validade-amostra"]["validade_a-endereco"].value;
    var contato_end = document.getElementById("validade_a-endereco");
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
    var nome = $("#validade_a-nome").val();
    var email = $("#validade_a-email").val();
    var empresa = $("#validade_a-empresa").val();
    var telefone = $("#validade_a-tel").val();
    var endereco = $("#validade_a-endereco").val();
    var mensagem = $("#validade_a-mensagem").val();
 
    $.ajax({
        type: "POST",
        url: "./amostra.php",
        data: "validade_a-nome=" + nome + "&validade_a-email=" + email + "&validade_a-empresa=" + empresa + "&validade_a-tel=" + telefone + "&validade_a-endereco=" + endereco + "&validade_a-mensagem=" + mensagem,
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
    $( "#etka_validade_a-enviado" ).removeClass( "etka_popup-enviado" );
    $( '#validade_a-nome, #validade_a-email, #validade_a-empresa, #validade_a-tel, #validade_a-endereco, #validade_a-mensagem' ).val('');
}

function validadeAError(){
    $( "#etka_validade_a-erro" ).removeClass( "etka_popup-erro" );
}


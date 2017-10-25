$(document).ready(function() {
    $(".etka_card__close").click(function() {
        $(".etka_popup-card").addClass("etka_popup-enviado");
    });
});

$('#pedido-simples').change(function(){
    if($(this).is(':checked'))
        $(this).next().text("Sim");
    else
        $(this).next().text("Não");
});

$("#etka_pedido").submit(function(event){
    var email = document.forms["etka_pedido"]["pedido-email"].value;
    var contato_email = document.getElementById("pedido-email");
    var endereco = document.forms["etka_pedido"]["pedido-endereco"].value;
    var contato_end = document.getElementById("pedido-endereco");
    var emailemp = document.forms["etka_pedido"]["pedido-emailemp"].value;
    var contato_emailemp = document.getElementById("pedido-emailemp");
    var endemp = document.forms["etka_pedido"]["pedido-endemp"].value;
    var contato_endemp = document.getElementById("pedido-endemp");
    var simples = document.forms["etka_pedido"]["pedido-simples"].value;
    var contato_simples = document.getElementById("pedido-simples"); 
    var att = document.createAttribute("required");
    if (email == "") {
        contato_email.setAttributeNode(att);
        return false;
        // handle the invalid form...
        pedidoError();
    }
    if (endereco == "") {
        contato_end.setAttributeNode(att);
        return false;
        // handle the invalid form...
        pedidoError();
    } 
    if (emailemp == "") {
        contato_emailemp.setAttributeNode(att);
        return false;
        // handle the invalid form...
        pedidoError();
    }
    if (endemp == "") {
        contato_endemp.setAttributeNode(att);
        return false;
        // handle the invalid form...
        pedidoError();
    } 
    if (event.isDefaultPrevented()) {
        // handle the invalid form...
        pedidoError();
    } else {
        // everything looks good!
        event.preventDefault();
        submitpedido();
    }
    if($(simples).is(':checked'))
        $(simples).next().text("Sim");
    else
        $(simples).next().text("Não");
});

function submitpedido(){
    // Initiate Variables With Form Content
    var assunto = $("#pedido-assunto").val();
    var nome = $("#pedido-nome").val();
    var email = $("#pedido-email").val();
    var empresa = $("#pedido-empresa").val();
    var telefone = $("#pedido-telefone").val();
    var endereco = $("#pedido-endereco").val();
    var quantidade = $("#pedido-quantidade").val();
    var mensagem = $("#pedido-mensagem").val();
    var cnpj = $("#pedido-cnpj").val();
    var razao = $("#pedido-razao").val();
    var endemp = $("#pedido-endemp").val();
    var insest = $("#pedido-insest").val();
    var emailemp = $("#pedido-emailemp").val();
    var telemp = $("#pedido-telemp").val();
    var simples = $("#pedido-simples").val();
 
    $.ajax({
        type: "POST",
        url: "../_assets/pedido.php",
        data: "pedido-assunto=" + assunto + "&pedido-nome=" + nome + "&pedido-email=" + email + "&pedido-empresa=" + empresa + "&pedido-telefone=" + telefone + "&pedido-endereco=" + endereco + "&pedido-quantidade=" + quantidade + "&pedido-mensagem=" + mensagem + "&pedido-cnpj=" + cnpj + "&pedido-razao=" + razao + "&pedido-endemp=" + endemp + "&pedido-insest=" + insest + "&pedido-emailemp=" + emailemp + "&pedido-telemp=" + telemp + "&pedido-simples=" + simples,
        success : function(text){
            if (text == "success"){
                pedidoSuccess();
            } else {
                pedidoError();
            }
        }
    });
}

function pedidoSuccess(){
    $( "#etka_pedido-enviado" ).removeClass( "etka_popup-enviado" );
    $( '#pedido-assunto, #pedido-nome, #pedido-email, #pedido-empresa, #pedido-telefone, #pedido-endereco, #pedido-quantidade, #pedido-mensagem, #pedido-cnpj, #pedido-razao, #pedido-endemp, #pedido-insest, #pedido-emailemp, #pedido-telemp, #pedido-simples' ).val('');
}

function pedidoError(){
    $( "#etka_pedido-erro" ).removeClass( "etka_popup-erro" );
}


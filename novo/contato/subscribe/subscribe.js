$(document).ready(function(submitSubscribe) {
$("#etka_subscribe").submit(function(event){
    var email = document.forms["etka_subscribe"]["subscribe-email"].value;
    var subscribe_email = document.getElementById("subscribe-email");
    var att = document.createAttribute("required");
    if (email == "") {
        subscribe_email.setAttributeNode(att);
        return false;
        // handle the invalid form...
        formError();
    }
    if (event.isDefaultPrevented()) {
        // handle the invalid form...
        formError();
    } else {
        // everything looks good!
        event.preventDefault();
        submitForm();
    }
});
});

function submitForm(){
    // Initiate Variables With Form Content
    var nome = $("#subscribe-nome").val();
    var email = $("#subscribe-email").val();
 
    $.ajax({
        type: "POST",
        url: "./contato/subscribe/subscribe.php",
        data: "subscribe-nome=" + nome + "&subscribe-email=" + email,
        success : function(text){
            if (text == "success"){
                formSuccess();
            } else {
                formError();
            }
        }
    });
}

function formSuccess(){
    $( "#etka_subscribe-enviado" ).removeClass( "etka_subscribe-enviado" );
    $( '#subscribe-nome, #subscribe-email' ).val('');
}

function formError(){
    $( "#etka_subscribe-erro" ).removeClass( "etka_subscribe-erro" );
}


$("#etka_subscribe").submit(function(event){
    var email = document.forms["etka_subscribe"]["subscribe-email"].value;
    var subscribe_email = document.getElementById("subscribe-email");
    var att = document.createAttribute("required");
    if (email == "") {
        subscribe_email.setAttributeNode(att);
        return false;
        // handle the invalid form...
        subscribeError();
    }
    if (event.isDefaultPrevented()) {
        // handle the invalid form...
        subscribeError();
    } else {
        // everything looks good!
        event.preventDefault();
        submitSubscribe();
    }
});

function submitSubscribe(){
    // Initiate Variables With Form Content
    var nome = $("#subscribe-nome").val();
    var email = $("#subscribe-email").val();
 
    $.ajax({
        type: "POST",
        url: "./contato/subscribe/subscribe.php",
        data: "subscribe-nome=" + nome + "&subscribe-email=" + email,
        success : function(text){
            if (text == "success"){
                subscribeSuccess();
            } else {
                subscribeError();
            }
        }
    });
}

function subscribeSuccess(){
    $( "#etka_subscribe-enviado" ).removeClass( "etka_subscribe-enviado" );
    $( '#subscribe-nome, #subscribe-email' ).val('');
}

function subscribeError(){
    $( "#etka_subscribe-erro" ).removeClass( "etka_subscribe-erro" );
}


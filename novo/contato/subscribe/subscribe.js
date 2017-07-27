//VALIDACAO
function validateForm() {
    var name = document.forms["etka_subscribe"]["subscribe-name"].value;
    if (name == "") {
        alert("Por favor, preencha o seu nome.");
        return false;
    }
    var email = document.forms["etka_subscribe"]["subscribe-email"].value;
    if (email == "") {
        alert("Preencha o seu e-mail, assim lhe antenderemos mais rapidamente.");
        return false;
    }
}

//CONFIRMACAO
$("#etka_subscribe").submit(function() {
    $.post('./contato/subscribe.php', {
        name: $('#subscribe-name').val(),
        email: $('#subscribe-email').val(),
        myFormSubmitted: 'yes'
    },function(data) {
        $("#etka_subscribe-enviado").html(data).fadeIn('100');
        $('#subscribe-name, #subscribe-email').val(''); /* Clear the inputs */
    }, 'text');
    return false;
});
//VALIDACAO
function validateForm() {
    var name = document.forms["etka_contato"]["name"].value;
    if (name == "") {
        alert("Por favor, preencha o seu nome.");
        return false;
    }
    var email = document.forms["etka_contato"]["email"].value;
    if (email == "") {
        alert("Preencha o seu e-mail, assim lhe antenderemos mais rapidamente.");
        return false;
    }
    var message = document.forms["etka_contato"]["message"].value;
    if (message =ts= "") {
        alert("Escreva uma mensagem para nós, ela é fundamental.");
        return false;
    }
}

//CONFIRMACAO
$("#etka_contato").submit(function() {
    $.post('./contato/contato.php', {
        name: $('#name').val(),
        email: $('#email').val(),
        company: $('#company').val(),
        phone: $('#phone').val(),
        subject: $('#subject').val(),
        message: $('#message').val(),
        myFormSubmitted: 'yes'
    },function(data) {
        $("#etka_contato-enviado").html(data).fadeIn('100');
        $('#name, #email, #company, #phone, #subject, #message').val(''); /* Clear the inputs */
    }, 'text');
    return false;
});
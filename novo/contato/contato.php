<?php
// define variables and set to empty values
$nomeErr = $emailErr = $assuntoErr = $mensagemErr = "";
$nome = $email = $empresa = $tel = $assunto = $mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (empty($_POST["contato-nome"])) {
        $nomeErr = "Name is required";
    } else {
        $nome = test_input($_POST["contato-nome"]);
    }
    
    if (empty($_POST["contato-email"])) {
        $emailErr = "Email is required";
    } else {
        $email = test_input($_POST["contato-email"]);
        // check if e-mail address is well-formed
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format"; 
        }
    }
    
    if (empty($_POST["contato-empresa"])) {
        $empresa = "";
    } else {
        $empresa = test_input($_POST["contato-empresa"]);
    }
    
    if (empty($_POST["contato-tel"])) {
        $tel = "";
    } else {
        $tel = test_input($_POST["contato-tel"]);
    }
    
    if (empty($_POST["contato-assunto"])) {
        $assuntoErr = "Assunto is required";
    } else {
        $assunto = test_input($_POST["contato-assunto"]);
    }
    
    if (empty($_POST["contato-mensagem"])) {
        $mensagemErr = "Mensagem is required";
    } else {
        $mensagem = test_input($_POST["contato-mensagem"]);
    }
}

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}
?>
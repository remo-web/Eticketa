<?php

$error = "";

$nome = $_POST["contato-nome"];

//email
if (empty($_POST["contato-email"])) {
    $error .= "Email is required ";
} else {
    $email = $_POST["contato-email"];
}

$empresa = $_POST["contato-empresa"];
$tel = $_POST["contato-tel"];
$assunto = $_POST["contato-assunto"];

//mensagem
if (empty($_POST["contato-mensagem"])) {
    $error .= "Mensagem is required ";
} else {
    $mensagem = $_POST["contato-mensagem"];
}
 
$To = "raphael.pais@eticketa.com.br";
$Subject = "[Site | Contato] $assunto";
 
// prepare email body text
$Body .= "Nome: ";
$Body .= $nome;
$Body .= "\n";
 
$Body .= "E-mail: ";
$Body .= $email;
$Body .= "\n";
 
$Body .= "Cargo / Empresa: ";
$Body .= $empresa;
$Body .= "\n";
 
$Body .= "Telefone: ";
$Body .= $tel;
$Body .= "\n";
 
$Body .= "Assunto: ";
$Body .= $assunto;
$Body .= "\n";
 
$Body .= "Mensagem: ";
$Body .= $mensagem;
$Body .= "\n";

$Body .= "
<html>
    $nome ($empresa), entrou em contato atráves do site sobre $assunto e dizendo:
    <br/>$mensagem
    <br/>
    Para retornar este contato utilize as seguintes opções:
    <br/>$tel
    <br/>$email
</html>
"

$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-Transfer-Encoding: 8bit" . "\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8" . "\r\n";
$headers .= "From: $email" . "\r\n";
 
// send email
$success = mail($To, $Subject, $Body, $headers);
 
// redirect to success page
if ($success && $error == ""){
   echo "success";
}else{
    if($error == ""){
        echo "Something went wrong :(";
    } else {
        echo $error;
    }
}
 
?>
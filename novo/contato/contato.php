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

$headers .= 'Content-Transfer-Encoding: 8bit' . "\r\n";
$headers .= 'Content-Type: text/plain; charset=UTF-8' . "\r\n";
 
// send email
$success = mail($To, $Subject, $Body, $headers, "From:".$email);
 
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
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
$Subject = "New Message Received $assunto";
 
// prepare email body text
$Body .= "Name: ";
$Body .= $nome;
$Body .= "\n";
 
$Body .= "Email: ";
$Body .= $email;
$Body .= "\n";
 
$Body .= "empresa: ";
$Body .= $empresa;
$Body .= "\n";
 
$Body .= "tel: ";
$Body .= $tel;
$Body .= "\n";
 
$Body .= "assunto: ";
$Body .= $assunto;
$Body .= "\n";
 
$Body .= "mensagem: ";
$Body .= $mensagem;
$Body .= "\n";
 
// send email
$success = mail($To, $Subject, $Body, "From:".$email);
 
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
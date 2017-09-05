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
$uglySubject = "[Site | Contato] $assunto";
$Subject='=?UTF-8?B?'.base64_encode($uglySubject).'?=';
 
// prepare email body text
//$Body .= "Nome: ";
//$Body .= $nome;
//$Body .= "\n";
// 
//$Body .= "E-mail: ";
//$Body .= $email;
//$Body .= "\n";
// 
//$Body .= "Cargo / Empresa: ";
//$Body .= $empresa;
//$Body .= "\n";
// 
//$Body .= "Telefone: ";
//$Body .= $tel;
//$Body .= "\n";
// 
//$Body .= "Assunto: ";
//$Body .= $assunto;
//$Body .= "\n";
// 
//$Body .= "Mensagem: ";
//$Body .= $mensagem;
//$Body .= "\n";

$boundary = md5("eticketa.com.br"); 

$Body = "--$boundary\r\n";
$Body .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
$Body .= "Content-Transfer-Encoding: base64\r\n\r\n";
$Body .= "$nome, enviou um <b>contato</b> pelo site com o seguinte recado: $mensagem"

$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-Transfer-Encoding: 8bit" . "\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8" . "\r\n";
$headers .= "From: $email" . "\r\n";
 
// send email
$success = mail($To, $Subject, $Body, $headers);
 
// redirect to success page
if ($success && $error == ""){
   echo "success";
}else{
    if($error == ""){
        echo "Algo deu errado... Mas deu errado num nível, que é melhor você nos ligar no telefone (21) 3490-9292, porque pelo site vai ser difícil.";
    } else {
        echo $error;
    }
}
 
?>
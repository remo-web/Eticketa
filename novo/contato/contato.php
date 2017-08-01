<?php
$nome = $_POST["contato-nome"];
$email = $_POST["contato-email"];
$empresa = $_POST["contato-empresa"];
$tel = $_POST["contato-tel"];
$assunto = $_POST["contato-assunto"];
$mensagem = $_POST["contato-mensagem"];
 
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
if ($success){
   echo "success";
}else{
    echo "invalid";
}
 
?>
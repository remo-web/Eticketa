<?php
$nome = $_POST["nome"];
$email = $_POST["email"];
$empresa = $_POST["empresa"];
$tel = $_POST["tel"];
$assunto = $_POST["assunto"];
$mensagem = $_POST["mensagem"];
 
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
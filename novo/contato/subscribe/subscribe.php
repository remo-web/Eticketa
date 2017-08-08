<?php

$error = "";

$nome = $_POST["subscribe-nome"];

//email
if (empty($_POST["subscribe-email"])) {
    $error .= "Email is required ";
} else {
    $email = $_POST["subscribe-email"];
}
 
$To = "raphael.pais@eticketa.com.br";
$Subject = "[Site | Subscribe]";
 
// prepare email body text
$Body .= "Nome: ";
$Body .= $nome;
$Body .= "\n";
 
$Body .= "E-mail: ";
$Body .= $email;
$Body .= "\n";

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
        echo "Something went wrong :(";
    } else {
        echo $error;
    }
}
 
?>
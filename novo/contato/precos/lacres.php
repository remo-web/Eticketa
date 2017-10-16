<?php

$error = "";

$nome = $_POST["p_lacres-nome"];

//email
if (empty($_POST["p_lacres-email"])) {
    $error .= "Email is required ";
} else {
    $email = $_POST["p_lacres-email"];
}

$empresa = $_POST["p_lacres-empresa"];
$quantidade = $_POST["p_lacres-quantidade"];
$endereco = $_POST["p_lacres-endereco"];

 
$To = "raphael.pais@eticketa.com.br";
$uglySubject = "[Site | Preços] Lacres para Delivery";
$Subject='=?UTF-8?B?'.base64_encode($uglySubject).'?=';

$Body .= "
<html>
<body style='width: 690px; font-family: sans-serif'>
<b style='text-transform: uppercase'>$nome</b><br>
<b>Cargo/Empresa: </b>$empresa<br>
<b>E-mail: </b>$email<br>
<b>Quantidade: </b>$quantidade<br>
<b>Endereço: </b>$endereco
</body></html>";

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
        echo "Algo deu errado... Mas deu errado num nível, que é melhor você nos ligar no telefone (21) 3490-9292, porque pelo site vai ser difícil.";
    } else {
        echo $error;
    }
}
 
?>
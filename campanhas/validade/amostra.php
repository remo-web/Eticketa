<?php

$error = "";

$nome = $_POST["validade_a-nome"];

//email
if (empty($_POST["validade_a-email"])) {
    $error .= "Email is required ";
} else {
    $email = $_POST["validade_a-email"];
}

$empresa = $_POST["validade_a-empresa"];
$telefone = $_POST["validade_a-tel"];

//endereço
if (empty($_POST["validade_a-endereco"])) {
    $error .= "Endereço is required ";
} else {
    $endereco = $_POST["validade_a-endereco"];
}

$mensagem = $_POST["validade_a-mensagem"];
 
$To = "colemais@eticketa.com.br";
$uglySubject = "[Campanha | Validade] Pedido de amostra";
$Subject='=?UTF-8?B?'.base64_encode($uglySubject).'?=';

$Body .= "
<html>
    <body style='width: 690px'>
        <b style='text-decoration: uppercase'>$nome</b>
        <b>Cargo / Empresa:</b> $empresa
        <b>E-mail:</b> $email
        <b>Telefone:</b> $telefone
        <b>Endereço para entrega:</b> $endereco
        <b>Observações / Dúvidas:</b> $mensagem
    </body>
</html>
";

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
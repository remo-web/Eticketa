<?php

$error = "";

$assunto = $_POST["amostra-assunto"];
$nome = $_POST["amostra-nome"];

//email
if (empty($_POST["amostra-email"])) {
    $error .= "Email is required ";
} else {
    $email = $_POST["amostra-email"];
}

$empresa = $_POST["amostra-empresa"];
$telefone = $_POST["amostra-telefone"];

//endereço
if (empty($_POST["amostra-endereco"])) {
    $error .= "Endereço is required ";
} else {
    $endereco = $_POST["amostra-endereco"];
}

$mensagem = $_POST["amostra-mensagem"];
 
$To = "colemais@eticketa.com.br";
$uglySubject = "[Campanha | $assunto] Pedido de amostra";
$Subject='=?UTF-8?B?'.base64_encode($uglySubject).'?=';

$Body .= "
<html>
    <body style='width: 690px'>
        <b style='text-transform: uppercase;'>$nome</b><br/>
        <b>Cargo / Empresa:</b> $empresa<br/>
        <b>E-mail:</b> $email<br/>
        <b>Telefone:</b> $telefone<br/>
        <b>Endereço para entrega:</b> $endereco<br/>
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
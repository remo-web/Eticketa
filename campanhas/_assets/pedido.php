<?php

$error = "";

$assunto = $_POST["pedido-assunto"];
$nome = $_POST["pedido-nome"];

//email
if (empty($_POST["pedido-email"])) {
    $error .= "Email is required ";
} else {
    $email = $_POST["pedido-email"];
}

$empresa = $_POST["pedido-empresa"];
$telefone = $_POST["pedido-telefone"];

//endereço
if (empty($_POST["pedido-endereco"])) {
    $error .= "Endereço is required ";
} else {
    $endereco = $_POST["pedido-endereco"];
}

$quantidade = $_POST["pedido-quantidade"];
$mensagem = $_POST["pedido-mensagem"];

$cnpj = $_POST["pedido-cnpj"];
$razao = $_POST["pedido-razao"];

//endereço
if (empty($_POST["pedido-endemp"])) {
    $error .= "Endereço is required ";
} else {
    $endemp = $_POST["pedido-endemp"];
}

$insest = $_POST["pedido-insest"];

//email_empresa
if (empty($_POST["pedido-emailemp"])) {
    $error .= "Email is required ";
} else {
    $emailemp = $_POST["pedido-emailemp"];
}

$telemp = $_POST["pedido-telemp"];
$simples = $_POST["pedido-simples"];
 
$To = "colemais@eticketa.com.br";
$uglySubject = "[Campanha | $assunto] Pedido formal";
$Subject='=?UTF-8?B?'.base64_encode($uglySubject).'?=';

$Body .= "
<html>
    <body style='width: 690px'>
        <b style='text-transform: uppercase;'>DADOS DE CONTATO E ENTREGA</b><br/>
        <b>Nome:</b> $nome<br/>
        <b>Cargo / Empresa:</b> $empresa<br/>
        <b>E-mail:</b> $email<br/>
        <b>Telefone:</b> $telefone<br/>
        <b>Endereço para entrega:</b> $endereco<br/>
        <b>Quantidade:</b> $quantidade <br/>
        <b>Observações / Dúvidas:</b> $mensagem<br/><br/>
        <b style='text-transform: uppercase;'>DADOS PARA EMISSÃO DE BOLETO E NOTA FISCAL</b><br/>
        <b>CNPJ:</b> $cnpj<br/>
        <b>Razão Social:</b> $razao<br/>
        <b>Endereço:</b> $endemp<br/>
        <b>Inscrição Estatual:</b> $insest<br/>
        <b>Email para envio da NF-e:</b> $emailemp<br/>
        <b>Telefone:</b> $telemp<br/>
        <b>Optante do Simples?</b> $simples      
        
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
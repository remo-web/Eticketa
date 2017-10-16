<?php

$error = "";

$nome = $_POST["o_validade-nome"];

//email
if (empty($_POST["o_validade-email"])) {
    $error .= "Email is required ";
} else {
    $email = $_POST["o_validade-email"];
}

$empresa = $_POST["o_validade-empresa"];
$telefone = $_POST["o_validade-telefone"];
$largura = $_POST["o_validade-largura"];
$altura = $_POST["o_validade-altura"];
$formato = $_POST["o_validade-formato"];
$quantidade = $_POST["o_validade-quantidade"];
$frente = $_POST["o_validade-frente"];
$verso = $_POST["o_validade-verso"];
$finalidade = $_POST["o_validade-finalidade"];
$mensagem = $_POST["o_validade-mensagem"];

 
$To = "raphael.pais@eticketa.com.br";
$uglySubject = "[Site | Orçamento] Etiqueta de validade para $finalidade";
$Subject='=?UTF-8?B?'.base64_encode($uglySubject).'?=';

$Body .= "
<html>
<body style='width: 690px; font-family: sans-serif'>
<b style='text-transform: uppercase'>$nome</b><br>
<b>Cargo/Empresa: </b>$empresa<br>
<b>E-mail: </b>$email<br>
<b>Telefone: </b>$telefone<br>
<b>Dimensões: </b>$largura (L) <b>x</b> $altura (A)<br>
<b>Formato: </b>$formato<br>
<b>Quantidade: </b>$quantidade<br>
<b>Cores: </b>$frente <b>x</b> $verso<br>
<b>Finalidade: </b>$finalidade<br>
<b>Observações: </b>$mensagem
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
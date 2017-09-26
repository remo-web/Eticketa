<?php
$error = "";
$nome = $_POST["o_rotulos-nome"];
//email
if (empty($_POST["o_rotulos-email"])) {
    $error .= "Email is required ";
} else {
    $email = $_POST["o_rotulos-email"];
}
$empresa = $_POST["o_rotulos-empresa"];
$telefone = $_POST["o_rotulos-telefone"];
$largura = $_POST["o_rotulos-largura"];
$altura = $_POST["o_rotulos-altura"];
$formato = $_POST["o_rotulos-formato"];
$quantidade = $_POST["o_rotulos-quantidade"];
$frente = $_POST["o_rotulos-frente"];
$verso = $_POST["o_rotulos-verso"];
$finalidade = $_POST["o_rotulos-finalidade"];
$obs = $_POST["o_rotulos-mensagem"];

$boundary = "XYZ-".md5(date("dmYis"))."-ZYX";

$path = $_FILES['file_attach']['tmp_name']; 
$fileType = $_FILES['file_attach']['type']; 
$fileName = $_FILES['file_attach']['name'];

$fp = fopen( $path, "rb" ); // abre o arquivo enviado
$anexo = fread( $fp, filesize( $path ) ); // calcula o tamanho
$anexo = chunk_split(base64_encode( $anexo )); // codifica o anexo em base 64
fclose( $fp ); // fecha o arquivo

$To = "raphael.pais@eticketa.com.br";
$uglySubject = "[Site | Orçamento] Rótulos";
$Subject='=?UTF-8?B?'.base64_encode($uglySubject).'?=';
 
/* prepare email body text
$Body .= "Nome: ";
$Body .= $nome;
$Body .= "\n";
 
$Body .= "E-mail: ";
$Body .= $email;
$Body .= "\n";
 
$Body .= "Cargo / Empresa: ";
$Body .= $empresa;
$Body .= "\n";
 
$Body .= "Telefone: ";
$Body .= $telefone;
$Body .= "\n";
 
$Body .= "Largura: ";
$Body .= $largura;
$Body .= " cm";
$Body .= "\n";
 
$Body .= "Altura: ";
$Body .= $altura;
$Body .= " cm";
$Body .= "\n";
 
$Body .= "Formato: ";
$Body .= $formato;
$Body .= "\n";
 
$Body .= "Quantidade: ";
$Body .= $quantidade;
$Body .= "\n";
 
$Body .= "Frente: ";
$Body .= $frente;
$Body .= " cores";
$Body .= "\n";
 
$Body .= "Verso: ";
$Body .= $verso;
$Body .= " cores";
$Body .= "\n";
 
$Body .= "Finalidade: ";
$Body .= $finalidade;
$Body .= "\n";
 
$Body .= "Observações: ";
$Body .= $mensagem;
$Body .= "\n";*/

$headers = "MIME-Version: 1.0" . PHP_EOL;
$headers .= "Content-Transfer-Encoding: 8bit" . "\r\n";
$headers .= "Content-Type: multipart/mixed; charset=UTF-8" . "\r\n";
$headers .= "From: $email" . "\r\n";
$headers .= "boundary=" . $boundary . PHP_EOL;
$headers .= "$boundary" . PHP_EOL;

$mensagem  = "--$boundary" . PHP_EOL;
$mensagem .= "Content-Type: text/html; charset='utf-8'" . PHP_EOL;
$mensagem .= "Mensagem"; // Adicione aqui sua mensagem
$mensagem .= "--$boundary" . PHP_EOL;

$mensagem .= "Content-Type: ". $fileType ."; name=\"". $fileName . "\"" . PHP_EOL;
$mensagem .= "Content-Transfer-Encoding: base64" . PHP_EOL;
$mensagem .= "Content-Disposition: attachment; filename=\"". $fileName . "\"" . PHP_EOL;
$mensagem .= "$anexo" . PHP_EOL;
$mensagem .= "--$boundary" . PHP_EOL;

// send email
$success = mail($To, $Subject, $mensagem, $headers);
 
// redirect to success page
if ($success && $error == ""){
    echo "success";
} else {
    if($error == ""){
        echo "Algo deu errado... Mas deu errado num nível, que é melhor você nos ligar no telefone (21) 3490-9292, porque pelo site vai ser difícil.";
    } else {
        echo $error;
    }
} 
?>
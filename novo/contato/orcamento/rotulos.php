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
$mensagem = $_POST["o_rotulos-mensagem"];
$arquivo = $_FILES["o_rotulos-anexo"];

$boundary = "XYZ-".date("dmYis")."-ZYX";


$fp = fopen($arquivo["tmp_name"], "rb"); // abre o arquivo enviado

$anexo = fread($fp, filesize($arquivo["tmp_name"])); // calcula o tamanho

$anexo = base64_encode($anexo);// codifica o anexo em base 64

fclose($fp);// fecha o arquivo



$To = "raphael.pais@eticketa.com.br";
$uglySubject = "[Site | Orçamento] Rótulos";
$Subject='=?UTF-8?B?'.base64_encode($uglySubject).'?=';
 
// prepare email body text
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
$Body .= "\n";

$headers  = "MIME-Version: 1.0 \r\n";
$headers .= "Content-Type: multipart/mixed \r\n";
//$headers .= "boundary="$boundary" \r\n";
$headers .= "$boundary \r\n";
$headers .= "From: $email" . "\r\n";

// email

$Body  = "--$boundary \r\n";
$Body .= "Content-Type: text/plain; charset='utf-8' \r\n";
$Body .= "Nome: $nome \r\n";
$Body .= "--$boundary \r\n";

// anexo 
$Body .= "Content-Type: ".$arquivo["type"]."; name=\"".$arquivo['name']."\" \r\n"; 
$Body .= "Content-Transfer-Encoding: base64 \r\n"; 
$Body .= "Content-Disposition: attachment; filename=\"".$arquivo['name']."\" \r\n";
$Body .= "$anexo \n"; 
$Body .= "--$boundary \r\n"; 

 
// send email
$success = mail($To, $Subject, $Body, $headers);
 
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
<?php
/*$error = "";
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
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-Transfer-Encoding: 8bit" . "\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8" . "\r\n";
$headers .= "From: $email" . "\r\n";
 
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
}*/







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
$texto = $_POST["o_rotulos-mensagem"];
 
$To = "raphael.pais@eticketa.com.br";
$uglySubject = "[Site | Orçamento] Rótulos";
$Subject='=?UTF-8?B?'.base64_encode($uglySubject).'?=';


// prepare email body text
$mensagem .= "Nome: ";
$mensagem .= $nome;
$mensagem .= "\n";
 
$mensagem .= "E-mail: ";
$mensagem .= $email;
$mensagem .= "\n";
 
$mensagem .= "Cargo / Empresa: ";
$mensagem .= $empresa;
$mensagem .= "\n";
 
$mensagem .= "Telefone: ";
$mensagem .= $telefone;
$mensagem .= "\n";
 
$mensagem .= "Largura: ";
$mensagem .= $largura;
$mensagem .= " cm";
$mensagem .= "\n";
 
$mensagem .= "Altura: ";
$mensagem .= $altura;
$mensagem .= " cm";
$mensagem .= "\n";
 
$mensagem .= "Formato: ";
$mensagem .= $formato;
$mensagem .= "\n";
 
$mensagem .= "Quantidade: ";
$mensagem .= $quantidade;
$mensagem .= "\n";
 
$mensagem .= "Frente: ";
$mensagem .= $frente;
$mensagem .= " cores";
$mensagem .= "\n";
 
$mensagem .= "Verso: ";
$mensagem .= $verso;
$mensagem .= " cores";
$mensagem .= "\n";
 
$mensagem .= "Finalidade: ";
$mensagem .= $finalidade;
$mensagem .= "\n";
 
$mensagem .= "Observações: ";
$mensagem .= $texto;
$mensagem .= "\n";

$arquivo = isset($_FILES["file_attach[]"]) ? $_FILES["file_attach[]"] : FALSE;

if(file_exists($arquivo["tmp_name"]) and !empty($arquivo)){
   $fp = fopen($_FILES["file_attach[]"]["tmp_name"],"rb");        
$anexo = fread($fp,filesize($_FILES["file_attach[]"]["tmp_name"]));                  
$anexo = base64_encode($anexo);fclose($fp);       
$anexo = chunk_split($anexo);$boundary = "XYZ-" . date("dmYis") . "-ZYX";        
$mens = "--$boundary\n";        
$mens .= "Content-Transfer-Encoding: 8bits\n";        
$mens .= "Content-Type: text/html; charset=\"ISO-8859-1\"\n\n"; 
//plain        
$mens .= "$mensagem\n";        
$mens .= "--$boundary\n";        
$mens .= "Content-Type: ".$arquivo["type"]."\n";        
$mens .= "Content-Disposition: attachment; filename=\"".
$arquivo["name"]."\"\n";        
$mens .= "Content-Transfer-Encoding: base64\n\n";       
$mens .= "$anexo\n";       
$mens .= "--$boundary--\r\n";
$headers  = "MIME-Version: 1.0\n";
$headers .= "From: \"$nome\" <$email_>\r\n";
$headers .= "Content-type: multipart/mixed; boundary=\"$boundary\"\r\n";
$headers .= "$boundary\n";
    
//envio o email com o anexo 

mail($To,$Subject,$mens, $headers);?><script language="javascript">alert("Enviado com sucesso!");
location ="rotulos.php";</script><?}
//se nao tiver anexo
else{?><script language="javascript">alert("Você não adicionou o arquivo! Tente novamente!");location = "rotulos.php";</script><? 

}
?>
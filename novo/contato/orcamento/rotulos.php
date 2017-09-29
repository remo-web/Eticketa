<?php

if(isset($_POST['o_rotulos-enviar']) && $_POST['o_rotulos-enviar']=='etka_o-rotulos'):
    $arquivo = $_FILES['etka_o-anexo'];
    $nome = $_POST['nome'];
    
    $para = "raphael.pais@eticketa.com.br";
    
    $boundary = "XYZ-".date("dmYis")."-ZYX";
    $fp = fopen($arquivo["tmp_name"], "rb"); // abre o arquivo enviado
    $anexo = fread($fp, filesize($arquivo["tmp_name"])); // calcula o tamanho
    $anexo = base64_encode($anexo); // codifica o anexo em base 64
    fclose($fp); // fecha o arquivo
    
    // cabeçalho do email
    $headers  = "MIME-Version: 1.0\n";
    $headers .= "Content-Type: multipart/mixed; ";
    //$headers .= "";
    $headers .= "$boundary\n";
    
    // email
    $mensagem  = "--$boundary\n";
    $mensagem .= "Content-Type: text/html; charset='utf-8'\n";
    $mensagem .= "<strong>Nome: </strong> $nome \r\n";
    $mensagem .= "--$boundary \n";
    
    // anexo
    $mensagem .= "Content-Type: ".$arquivo["type"].";name=".$arquivo['name']." \n";
    $mensagem .= "Content-Transfer-Encoding: base64 \n";
    $mensagem .= "Content-Disposition: attachment; filename=".$arquivo['name']." \r\n";
    $mensagem .= "$anexo \n";
    $mensagem .= "--$boundary \n";
    
    // enviar o email
    mail($para, $assunto, $mensagem, $headers);
    echo"Email enviado com Sucesso!";
    else:
        echo 'Email não enviado!';
    endif;
?>
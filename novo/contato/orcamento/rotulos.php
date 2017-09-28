<?php

require_once './vendor/autoload.php'
    
if(isset($_POST["email"])){
    if(!filter_var($_POST['email']), FILTER_VALIDATED_EMAIL){
        echo "Digite um email válido"
    }else{
        
        $mail = new PHPMailer();
        $mail->setFrom($_POST['o_rotulos-email'],$_POST['o_rotulos-nome']);
        $mail->addAddress("raphael.pais@eticketa.com.br","Teste")
        $mail->isHTML(true);
        $mail->Subject = "[Site | Orçamento] Rótulos";
        $mail->Body = "<h4> Nome: ".$_POST["o_rotulos-nome"]."Email: ".$_POST["o_rotulos-email"]."Empresa: ".$_POST["o_rotulos-empresa"]."Telefone: ".$_POST["o_rotulos-telefone"]."Largura: ".$_POST["o_rotulos-largura"]."Altura: ".$_POST["o_rotulos-altura"]."Formato: ".$_POST["o_rotulos-formato"]."Quantidade: ".$_POST["o_rotulos-quantidade"]."Frente: ".$_POST["o_rotulos-frente"]."Verso: ".$_POST["o_rotulos-verso"]."Finalidade: ".$_POST["o_rotulos-finalidade"]."Observações: ".$_POST["o_rotulos-mensagem"]."<h4>";
        
        if($mail->send()){
            echo "Email enviado";
        }else{
            echo "error"
        }
    }
    
}

?>
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './PHPMailer/src/Exception.php';
require './PHPMailer/src/PHPMailer.php';
require './PHPMailer/src/SMTP.php';
require_once './vendor/autoload.php';
    
    
        $mail = new PHPMailer(true);
        $mail->setFrom("gabriela.abreu88@gmail.com", "Gostosa");
        $mail->addAddress("raphael.pais@eticketa.com.br","Teste");
        $mail->addAttachment('/img/gabi.jpg');
        $mail->isHTML(true);
        $mail->Subject = "[Site | Orçamento] Rótulos";
        $mail->Body = "<h4> Só testando <h4>";
        
        if($mail->send()){
            echo "Email enviado";
        }else{
            echo "error";
        }
    

?>
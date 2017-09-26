<?php
if($_POST && isset($_FILES['file_attach']))
{
    $recipient_email    = "gabriela.abreu88@gmail.com"; //recepient
    $from_email         = "raphael.pais@eticketa.com.br"; //from email using site domain.
    $subject            = "Trocar isso depois: Attachment email from your website!"; //email subject line
    
    $sender_name = filter_var($_POST["o_rotulos-nome"], FILTER_SANITIZE_STRING); //capture sender name
    $sender_email = filter_var($_POST["o_rotulos-email"], FILTER_SANITIZE_STRING); //capture sender email
    $telefone = filter_var ($_POST["o_rotulos-telefone"], FILTER_SANITIZE_STRING);
    $largura = filter_var ($_POST["o_rotulos-largura"], FILTER_SANITIZE_STRING);
    $altura = filter_var ($_POST["o_rotulos-altura"], FILTER_SANITIZE_STRING);
    $formato = filter_var ($_POST["o_rotulos-formato"], FILTER_SANITIZE_STRING);
    $quantidade = filter_var ($_POST["o_rotulos-quantidade"], FILTER_SANITIZE_STRING);
    $frente = filter_var ($_POST["o_rotulos-frente"], FILTER_SANITIZE_STRING);
    $verso = filter_var ($_POST["o_rotulos-verso"], FILTER_SANITIZE_STRING);
    $finalidade = filter_var ($_POST["o_rotulos-finalidade"], FILTER_SANITIZE_STRING);
    $sender_message = filter_var($_POST["o_rotulos-mensagem"], FILTER_SANITIZE_STRING); //capture message
    $attachments = $_FILES['file_attach'];
    
    //php validation
    if(strlen($sender_name)<2){
        die('Nome muito pequeno ou inexistente');
    }
    if (!filter_var($sender_email, FILTER_VALIDATE_EMAIL)) {
      die('Invalid email');
    }
    if(strlen($sender_message)<4){
        die('Mensagem muito curta, por favor escreva algo!');
    }
    
    $file_count = count($attachments['name']); //count total files attached
    $boundary = md5("sanwebe.com"); 
            
    if($file_count > 0){ //if attachment exists
        //header
        $headers = "MIME-Version: 1.0\r\n"; 
        $headers .= "From:".$from_email."\r\n"; 
        $headers .= "Reply-To: ".$sender_email."" . "\r\n";
        $headers .= "Content-Type: multipart/mixed; boundary = $boundary\r\n\r\n"; 
        
        //message text
        $body = "--$boundary\r\n";
        $body .= "Content-Type: text/plain; charset=ISO-8859-1\r\n";
        $body .= "Content-Transfer-Encoding: base64\r\n\r\n"; 
        $body .= chunk_split(base64_encode($sender_message)); 

        //attachments
        for ($x = 0; $x < $file_count; $x++){       
            if(!empty($attachments['name'][$x])){
                
                if($attachments['error'][$x]>0) //exit script and output error if we encounter any
                {
                    $mymsg = array( 
                    1=>"The uploaded file exceeds the upload_max_filesize directive in php.ini", 
                    2=>"The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form", 
                    3=>"The uploaded file was only partially uploaded", 
                    4=>"No file was uploaded", 
                    6=>"Missing a temporary folder" ); 
                    die($mymsg[$attachments['error'][$x]]); 
                }
                
                //get file info
                $file_name = $attachments['name'][$x];
                $file_size = $attachments['size'][$x];
                $file_type = $attachments['type'][$x];
                
                //read file 
                $handle = fopen($attachments['tmp_name'][$x], "r");
                $content = fread($handle, $file_size);
                fclose($handle);
                $encoded_content = chunk_split(base64_encode($content)); //split into smaller chunks (RFC 2045)
                
                $body .= "--$boundary\r\n";
                $body .="Content-Type: $file_type; name="$file_name"\r\n";
                $body .="Content-Disposition: attachment; filename="$file_name"\r\n";
                $body .="Content-Transfer-Encoding: base64\r\n";
                $body .="X-Attachment-Id: ".rand(1000,99999)."\r\n\r\n"; 
                $body .= $encoded_content; 
            }
        }

    }else{ //send plain email otherwise
       $headers = "From:".$from_email."\r\n".
        "Reply-To: ".$sender_email. "\n" .
        "X-Mailer: PHP/" . phpversion();
        $body = $sender_message;
    }
        
     $sentMail = @mail($recipient_email, $subject, $body, $headers);
    if($sentMail) //output success or failure messages
    {       
        die('Thank you for your email');
    }else{
        die('Could not send mail! Please check your PHP mail configuration.');  
    }
}
?>
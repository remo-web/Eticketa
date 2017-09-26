<?php
    if(isset($_POST['o_rotulos-enviar']))
    {
        //The form has been submitted, prep a nice thank you message
        $output = '<h1>Thanks for your file and message!</h1>';
        //Set the form flag to no display (cheap way!)
        $flags = 'style="display:none;"';

        //Deal with the email
        $to = 'raphael.pais@eticketa.com.br';
        $subject = 'a file for you';

        $message = strip_tags($_POST['o_rotulos-mensagem']);
        $attachment = chunk_split(base64_encode(file_get_contents($_FILES['file_attach']['tmp_name'])));
        $filename = $_FILES['file_attach']['name'];

        $boundary =md5(date('r', time())); 

        $headers = "From: webmaster@example.com\r\nReply-To: webmaster@example.com";
        $headers .= "\r\nMIME-Version: 1.0\r\nContent-Type: multipart/mixed; boundary=\"_1_$boundary\"";

        $message="This is a multi-part message in MIME format.

--_1_$boundary
Content-Type: multipart/alternative; boundary=\"_2_$boundary\"

--_2_$boundary
Content-Type: text/plain; charset=\"iso-8859-1\"
Content-Transfer-Encoding: 7bit

$message

--_2_$boundary--
--_1_$boundary
Content-Type: application/octet-stream; name=\"$filename\" 
Content-Transfer-Encoding: base64 
Content-Disposition: attachment 

$attachment
--_1_$boundary--";

        mail($to, $subject, $message, $headers);
    }
?>
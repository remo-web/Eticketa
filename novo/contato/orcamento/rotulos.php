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
$anexo = $_FILES[file_attach];

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
$Body .= "\n"; */

$file_count = count($anexo['name']); //count total files attached
    $boundary = md5("sanwebe.com"); 

if($file_count > 0){ //if attachment exists
        //header
$headers = "MIME-Version: 1.0\r\n";
$headers .= "From: $email" . "\r\n";
$headers .= "Content-Transfer-Encoding: 8bit" . "\r\n";
$headers .= "Content-Type: multipart/mixed; boundary = $boundary charset=UTF-8\r\n\r\n";

        //message text
$body = "--$boundary\r\n";
$body .= "Content-Type: text/plain; charset=ISO-8859-1\r\n";
$body .= "Content-Transfer-Encoding: base64\r\n\r\n"; 
$body .= chunk_split(base64_encode($mensagem)); 

for ($x = 0; $x < $file_count; $x++){       
            if(!empty($anexo['name'][$x])){
                
                if($anexo['error'][$x]>0) //exit script and output error if we encounter any
                {
                    $mymsg = array( 
                    1=>"The uploaded file exceeds the upload_max_filesize directive in php.ini", 
                    2=>"The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form", 
                    3=>"The uploaded file was only partially uploaded", 
                    4=>"No file was uploaded", 
                    6=>"Missing a temporary folder" ); 
                    die($mymsg[$anexo['error'][$x]]); 
                }
                
                //get file info
                $file_name = $anexo['name'][$x];
                $file_size = $anexo['size'][$x];
                $file_type = $anexo['type'][$x];
                
                //read file 
                $handle = fopen($anexo['tmp_name'][$x], "r");
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
       $headers = "From:".$To."\r\n".
        "Reply-To: ".$email. "\n" .
        "X-Mailer: PHP/" . phpversion();
        $body = $mensagem;
    }

 
// send email
$success = mail($To, $Subject, $body, $headers);
 
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
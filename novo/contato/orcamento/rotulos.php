<?php
ini_set('display_errors', 1);
$nome = filterInput($_POST["o_rotulos-nome"]);
//email
if (empty($_POST["o_rotulos-email"])) {
    $error .= "Email is required ";
} else {
    $email = filterInput($_POST["o_rotulos-email"]);
}
$empresa = filterInput($_POST["o_rotulos-empresa"]);
$telefone = filterInput($_POST["o_rotulos-telefone"]);
$largura = filterInput($_POST["o_rotulos-largura"]);
$altura = filterInput($_POST["o_rotulos-altura"]);
$formato = filterInput($_POST["o_rotulos-formato"]);
$quantidade = filterInput($_POST["o_rotulos-quantidade"]);
$frente = filterInput($_POST["o_rotulos-frente"]);
$verso = filterInput($_POST["o_rotulos-verso"]);
$finalidade = filterInput($_POST["o_rotulos-finalidade"]);
$obs = filterInput($_POST["o_rotulos-mensagem"]);

$To = "raphael.pais@eticketa.com.br";
$uglySubject = "[Site | Orçamento] Rótulos";
$Subject='=?UTF-8?B?'.base64_encode($uglySubject).'?=';
 
echo $email.$To.$obs.$Subject;
if(sendEmailWithAttachments($To,$email,$Subject,$obs))
{
    echo "Email sent successfully!";
}
else
{
    echo "Failed to send the email...";
}
 
function filterInput($data)
{
     $data = trim($data);
     $data = stripslashes($data);
     $data = htmlspecialchars($data);
     return $data;
}
function sendEmailWithAttachments($To,$email,$Subject,$obs){
 
     
    if(isset($_FILES)) {
echo "iamhere";
        $allowedExtensions = array("pdf","doc","docx","gif","jpeg","jpg","JPG","png","PNG","rtf","txt","xml");
 
        $files = array();
        foreach($_FILES as $name=>$file) {
            //die("file size: ".$file['size']);
            if($file['size']>=5242880)//5mb
            {
                $fileSize=$file['size'];
                return false;
            }
            $file_name = $file['name']; 
            $temp_name = $file['tmp_name'];
            $file_type = $file['type'];
            $path_parts = pathinfo($file_name);
            $ext = $path_parts['extension'];
            if(!in_array($ext,$allowedExtensions)) {
                return false;
                die("File $file_name has the extensions $ext which is not allowed");
            }
 
            //move the file to the server, cannot be skipped
            $server_file="/tmp/$path_parts[basename]";
            move_uploaded_file($temp_name,$server_file);
            array_push($files,$server_file);
            //array_push($files,$temp_name);
        }
 
        // email fields
        $headers = "From: $email";
 
 
        // boundary 
        $semi_rand = md5(time()); 
        $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 
 
        // headers for attachment 
        $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\""; 
 
        // multipart boundary 
        $message = "This is a multi-part message in MIME format.\n\n" . "--{$mime_boundary}\n" . "Content-Type: text/plain; charset=\"iso-8859-1\"\n" . "Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n"; 
        $message .= "--{$mime_boundary}\n";
 
        // preparing attachments
        for($x=0;$x<count($files);$x++){
            $file = fopen($files[$x],"rb");
            $data = fread($file,filesize($files[$x]));
            fclose($file);
            $data = chunk_split(base64_encode($data));
            $message .= "Content-Type: {\"application/octet-stream\"};\n" . " name=\"$files[$x]\"\n" . 
            "Content-Disposition: attachment;\n" . " filename=\"$files[$x]\"\n" . 
            "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
            $message .= "--{$mime_boundary}\n";
        }
 
        // send
        return @mail($To, $Subject, $message, $headers);
 
    }
}   
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

$anexado = $_FILES['attach_file']['name'];
	 $extensao = strtolower(end(explode('.', $anexado)));
	 $extensoes = array ('txt', 'jpg', 'docx'); // AKI VC PODE COLOCAR AS EXTENÇÕES QUE VC AEITARA NO UPLOAD
 $size = $_FILES['file_attach']['size'];
 $maxsize = 1024 * 1024 * 2; // AKI VC ESPECIFICA O TAMANHO DE ARQUIVOS ACEITOS, LEMBRANDO QUE A CONFIGURAÇÃO É LIVE


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
$Body .= $obs;
$Body .= "\n";

$arquivo = isset($_FILES["file_attach"]) ? $_FILES["file_attach"] : FALSE;
 
if(file_exists($arquivo["tmp_name"]) and !empty($arquivo)){
 
 $fp = fopen($_FILES["file_attach"]["tmp_name"],"rb");
	 $anexo = fread($fp,filesize($_FILES["file_attach"]["tmp_name"]));
	 $anexo = base64_encode($anexo);
 
fclose($fp);
	 
$anexo = chunk_split($anexo);
    
$mens = "--$boundaryn";
$mens .= "Content-Transfer-Encoding: 8bitsn";
$mens .= "Content-Type: text/html; charset="UTF-8"nn";
$mens .= "$configuracao_da_mensagem_originaln";
$mens .= "--$boundaryn";
$mens .= "Content-Type: ".$arquivo["type"]."n";
$mens .= "Content-Disposition: attachment; filename="".$arquivo["name"].""n";
$mens .= "Content-Transfer-Encoding: base64nn";
$mens .= "$anexo";
$mens .= "--$boundary--rn";
    
     
	$headers  = "MIME-Version: 1.0n";
	$headers .= "Content-type: multipart/mixed; boundary="$boundary"rn";
	$headers .= "$boundaryn";
	}else{
	 
	$headers  = "MIME-Version: 1.0n";
	$headers .= "Content-Type: text/html; charset="UTF-8"nn";

	


/*$headers = "MIME-Version: 1.0\n";
$headers .= "Content-Transfer-Encoding: 8bit" . "\r\n";
$headers .= "Content-Type: multipart/mixed; charset=UTF-8" . "\r\n";
$headers .= "From: $email" . "\r\n";
$headers .= "boundary=" . $boundary . PHP_EOL;
$headers .= "$boundary" . PHP_EOL;
*/

 
// send email
$success = mail($To, $Subject, $Body, $mens, $headers);
 
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
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

$uploaddir = './upload/';
$uploadfile = $uploaddir . basename($_FILES['o_rotulos-anexo']['name']);


/*$nomeArquivo = $_FILES["o_rotulos-anexo"]["name"]; // Pega o nome do arquivo
$nomeTemporario = $_FILES["o_rotulos-anexo"]["tmp_name"]; // Pega o nome temporario do arquivo
$tamanhoArquivo = $_FILES["o_rotulos-anexo"]["size"]; // Pega o tamanho
$caminho = 'subir/'; // define a pasta onde sera salvo o arquivo

$arquivoArray = explode(".", $nomeArquivo); // Separa o nome do arquivo da extensão, por exemplo: imagem1.jpg -> ficara imagem1
    $extensao = end($arquivoArray); // Pega a extensao do arquivo (final da variavel $arquivoArray), por exemplo: imagem1.jpg -> ficara .jpg
    $arquivo = $caminho.md5(time().rand(3212, 15452)).'.'.$extensao; // Junta o caminho e cria um nome complexo para o arquivo para evitar duplicidade, a variável conterá por exemplo -> uploads/987asd3a218w6qw21qeq651.jpg

    if (!is_dir($caminho)) { // Verifica se a pasta para salvar o arquivo existe (uploads)
        mkdir($caminho); // Caso não exista cria a pasta
        chmod($caminho, 777); // Caso não exista adiciona permissões de leitura e escrita na pasta
    }
/
    move_uploaded_file($nomeTemporario, $arquivo);*/




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
if ($success && $error == "" && (move_uploaded_file($_FILES['o_rotulos-anexo']['tmp_name'], $uploadfile))) {
    echo "success";
} else {
    if($error == ""){
        echo "Algo deu errado... Mas deu errado num nível, que é melhor você nos ligar no telefone (21) 3490-9292, porque pelo site vai ser difícil.";
    } else {
        echo $error;
    }
} 
?>
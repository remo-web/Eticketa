<?php
 
$EmailFrom = "contato@eticketa.com.br";
$EmailTo = "contato@eticketa.com.br";
$EmailSubject = "Contato do site";
$Subject = "Assunto: ".$_POST['mailsubject'];
$Name = "Nome: ".$_POST['name']; 
$Email = "E-mail: ".$_POST['email']; 
$Site = "Site: ".$_POST['website'];  
$Select = "Como encontrou a ETICKETA: ".$_POST['select'];
$Message = "Mensagem: ".$_POST['message']; 

// validation
$validationOK=true;
if (!$validationOK) {
  print "<meta http-equiv=\"refresh\" content=\"0;URL=error.htm\">";
  exit;
}

// prepare email body text
$Body = "";
$Body .= $Subject;
$Body .= "\n";
$Body .= $Name;
$Body .= "\n";
$Body .= $Email;
$Body .= "\n";
$Body .= $Site;
$Body .= "\n";
$Body .= $Select;
$Body .= "\n";
$Body .= $Message;
$Body .= "\n";

// send email 
$success = mail($EmailTo, $EmailSubject, $Body, "From: <$EmailFrom>");

// redirect to success page 
if ($success){
  print "<meta http-equiv=\"refresh\" content=\"0;URL=contato_sucesso.html\">";
}
else{
    alert("Ocorreu um erro ao enviar sua mensagem. Por favor, tente novamente e caso persistir entre em contato através do e-mail: contato@eticketa.com.br");
}
?>
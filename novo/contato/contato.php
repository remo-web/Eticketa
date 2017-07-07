<?php
// check if fields passed are empty
if(empty($_POST['name'])  		||
   empty($_POST['email']) 		||
   empty($_POST['company'])     ||
   empty($_POST['phone']) 		||
   empty($_POST['subject']) 	||
   empty($_POST['message'])	    ||
   !filter_var($_POST['email'],FILTER_VALIDATE_EMAIL))
   {
	echo "No arguments Provided!";
	return false;
   }

if ($_SERVER['REQUEST_METHOD'] == "POST"){
    // The form has been submitted
    echo "
                                <div class="mdl-cell mdl-cell--middle mdl-cell--12-col">
                                    <span class="mdl-chip mdl-chip--deletable">
                                        <span class="mdl-chip__text">Mensagem enviada!</span>
                                        <button type="button" class="mdl-chip__action">
                                            <i class="material-icons">cancel</i>
                                        </button>
                                    </span>
                                </div>";
  } else {
    echo "Have a good night!";
}
	
$name = $_POST['name'];
$email_address = $_POST['email'];
$company = $_POST['company'];
$phone = $_POST['phone'];
$subject = $_POST['subject'];
$message = $_POST['message'];
	
// create email body and send it	
$to = 'raphael.pais@eticketa.com.br'; // *REPLACE WITH THE EMAIL ADDRESS YOU WANT THE FORM TO SEND MAIL TO*
$email_subject = "[SITE | Contato] $subject";
$email_body = "You have received a new message from your website contact form.\n\n"."Here are the details:\n\nName: $name\n\nCargo / Empresa: $company\n\nE-mail: $email_address\n\nPhone: $phone\n\nMessage:\n$message";
$headers = "MIME-Version: 1.0";
$headers .= "Content-type:text/html;charset=UTF-8";
$headers = "From: raphael.pais@eticketa.com.br\n"; // *REPLACE WITH THE EMAIL ADDRESS YOU WANT THE MESSAGE TO BE FROM*
$headers .= "Reply-To: $email_address";	
mail($to,$email_subject,$email_body,$headers);
return true;			
?>
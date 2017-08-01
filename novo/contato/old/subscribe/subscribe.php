<?php
// check if fields passed are empty
if(empty($_POST['subscribe-name'])  		||
   empty($_POST['subscribe-email']) 		||
   !filter_var($_POST['subscribe-email'],FILTER_VALIDATE_EMAIL))
   {
	echo "";
	return false;
   }

if ($_SERVER['REQUEST_METHOD'] == "POST"){
    // The form has been submitted
    echo '
        <span class="mdl-chip mdl-chip--contact">
            <span class="mdl-chip__contact mdl-color--green mdl-color-text--white">
                <i class="material-icons" style="vertical-align: middle">done</i>
            </span>
            <span class="mdl-chip__text">Obrigado!</span>
        </span>
    ';
  } else {
    echo '
        <span class="mdl-chip mdl-chip--contact">
            <span class="mdl-chip__contact mdl-color--red mdl-color-text--white">
                <i class="material-icons" style="vertical-align: middle">close</i>
            </span>
            <span class="mdl-chip__text">Ops! Por favor, tente novamente.</span>
        </span>
    ';
}
	
$name = $_POST['subscribe-name'];
$email_address = $_POST['subscribe-email'];
	
// create email body and send it	
$to = 'raphael.pais@eticketa.com.br'; // *REPLACE WITH THE EMAIL ADDRESS YOU WANT THE FORM TO SEND MAIL TO*
$email_subject = "[SITE | Subscribe]";
$email_body = "You have received a new message from your website contact form.\n\n"."Here are the details:\n\nName: $name\n\nE-mail: $email_address\n\n";
$headers = "MIME-Version: 1.0";
$headers .= "Content-type:text/html;charset=UTF-8";
$headers = "From: raphael.pais@eticketa.com.br\n"; // *REPLACE WITH THE EMAIL ADDRESS YOU WANT THE MESSAGE TO BE FROM*
$headers .= "Reply-To: $email_address";	
mail($to,$email_subject,$email_body,$headers);
return true;			
?>
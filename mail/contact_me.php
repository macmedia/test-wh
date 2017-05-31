<?php
// Check for empty fields
if(empty($_POST['name'])      ||
   empty($_POST['email'])     ||
   empty($_POST['phone'])     ||
   empty($_POST['message'])   ||
   !filter_var($_POST['email'],FILTER_VALIDATE_EMAIL))
   {
     echo json_encode(["code"=>"500","msg" => "Missing valid data."]);;
     return 0;
   }

$name = strip_tags(htmlspecialchars($_POST['name']));
$phone = strip_tags(htmlspecialchars($_POST['phone']));
$message = strip_tags(htmlspecialchars($_POST['message']));
$firstName = strip_tags(htmlspecialchars($_POST['firstName']));
$email_address = strip_tags(htmlspecialchars($_POST['email']));
$procurement = strip_tags(htmlspecialchars($_POST['procurement']));
$project_type = strip_tags(htmlspecialchars($_POST['project_type']));

// Create the email and send the message
$to = 'melser@etchamac.com';
$email_subject = "Website Contact Form:  $name";
$email_body = "You have received a new message from your website contact form.\n\n"."Here are the details:\n\nName: $name\n\nEmail: $email_address\n\nPhone: $phone\n\nMessage:\n$message\n\nProcurement: $procurement\n\n$project_type";
$headers = "From: noreply@etchamac.com\n"; // This is the email address the generated message will be from. We recommend using something like noreply@yourdomain.com.
$headers .= "Reply-To: $email_address";

// // Debug
// echo json_encode(["code"=>"200","msg" => nl2br($email_body)]);
// return 1;

if ( mail($to,$email_subject,$email_body,$headers) ){
  echo json_encode(["code"=>"200","msg" => "Thank you ". $firstname .". Your message has been sent."]);
  return 1;
}else{
  echo json_encode(["code"=>"500","msg" => "Sorry " . $firstName . ", it seems that my mail server is not responding. Please try again later!"]);
  return 0;
}
?>

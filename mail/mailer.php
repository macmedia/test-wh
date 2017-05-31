<?php
require "mailer_config.php";
require_once __DIR__ . '/../vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
require_once __DIR__ . '/../vendor/soundasleep/html2text/html2text.php';
require_once __DIR__ . "/../csrf/class.csrf.php";
require_once __DIR__ . "/../vendor/buuum/s3/src/S3/S3.php";


// Setup phpmailer
$mail = new PHPMailer;
$quote_random = mt_rand(100000, 999999);
$quote_number =  "ETCH-Q-$quote_random";
$quote_sha = sha1(mt_rand(1, 9999) . uniqid()) . time();
$quote_filename = "$quote_random-$quote_sha";

// Check of a POST has a valid CSRF Token.
if ( session_status() == PHP_SESSION_ACTIVE ){
  if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    echo json_encode(["code"=>"500","msg" => "Missing valid token. Please contact support."]);;
    return 0;
  }
}

// Setup file upload
if (!empty($_FILES['files'])) {

  $tmp_name = $_FILES['files']['tmp_name'][0];
  $artwork_name = $_FILES['files']['name'][0];
  $artwork_mime = $_FILES['files']['type'][0];
  $artwork_size = round($_FILES['files']['size'][0] / 1000, 1);
  $artwork_size_type = "KB";
  if( $artwork_size > 1000 ) {
    $artwork_size = round($artwork_size / 1000, 1);
    $artwork_size_type = "MB";
  }
  if( $_FILES['files']['size'][0] > $S3_MAX_FILESIZE ){
    echo json_encode(["code"=>"500","msg" => "File Size Error: File exceeds size limit. [".$_FILES['files']['size'][0]."]" ]);;
    return 0;
  }

  // Setup S3
  $auth    = \Buuum\S3::setAuth($S3_USER, $S3_PASS);
  $bucket  = \Buuum\S3::setBucket($S3_BUCKET);
  $url     = \Buuum\S3::setUrls($S3_HOST_URLS);
  $results = \Buuum\S3::putObject($tmp_name, $quote_filename);

  $artwork_url = "<a href='". $results['url']['default']. "'>$artwork_name</a> [ $artwork_size $artwork_size_type $artwork_mime ]";
}


//Reset the CSRF token
$csrf = new \security\csrf;
$csrf::set_token();

if(empty($_POST['name'])      ||
   empty($_POST['email'])     ||
   empty($_POST['phone'])     ||
   empty($_POST['message'])   ||
   !filter_var($_POST['email'],FILTER_VALIDATE_EMAIL))
   {
     echo json_encode(["code"=>"500","msg" => "Missing required data!"]);;
     return 0;
   }

// Get the passed in POST data from ajax call contact_me.js
$name = strip_tags(htmlspecialchars($_POST['name']));
$email = strip_tags(htmlspecialchars($_POST['email']));
$phone = strip_tags(htmlspecialchars($_POST['phone']));
$message = strip_tags(htmlspecialchars($_POST['message']));
$procurement = strip_tags(htmlspecialchars($_POST['procurement']));
$project_size = strip_tags(htmlspecialchars($_POST['project_size']));
$product_type = strip_tags(htmlspecialchars($_POST['product_type']));

// Load the template file for HTML email
if (!$body = file_get_contents(__DIR__ .'/templates/NewQuote.html') ){
     echo json_encode(["code"=>"500","msg" => "Unable to load template file!"]);
     return 0;
}

// Set variables used in return messages
list($firstname, $lastname) = explode(' ', $name,2);
$artwork_status = ( isset($artwork_url) ) ? $artwork_url : "N/A";


// Reformat Phone Number +1800555555 | 8005555555 | +1(800)555-5555 | +1.800.555.5555 > (800) 555-5555
$phone = preg_replace('/^[\+]?[\d]?[\D]*(\d{3})\D*\s?(\d{3})\D*\s?(\d{4})[\S\s]*$/', '($1) $2-$3', $phone);

// Swap the placeholders in the template file with variables
$template_vars = [
    '%name%' => $name,
    '%email%' => $email,
    '%phone%' => $phone,
    '%message%' => $message,
    '%procurement%' => $procurement,
    '%quote_number%' => $quote_number,
    '%project_size%' => $project_size,
    '%product_type%' => $product_type,
    '%artwork_status%' => $artwork_status
];
$body = str_replace(array_keys($template_vars), $template_vars, $body);


// Debug connection
//$mail->SMTPDebug = 2;

$mail->isSMTP();                                                  // Set email format to HTML
$mail->isHTML(true);                                              // Set mailer to use SMTP
$mail->Host = $SES_HOST;                                          // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                                           // Use Auth
$mail->Username = $SES_USER;                                      // SMTP username
$mail->Password = $SES_PASS;                                      // SMTP password
$mail->Port = 587;                                                // TCP port to connect to

$mail->setFrom($SES_FROM, $SES_NAME);                             // From address
$mail->addAddress($email, $name);                                 // Add a recipient
$mail->MsgHTML($body);                                            // Attach template
//$mail->addAttachment('/tmp/AlTest1.err');                         // Add attachments

$mail->Subject = "Request for new quote [$quote_number]";          // Set the subject

// convert html to text
$mail->AltBody = convert_html_to_text($body);

if(!$mail->send()) {
  echo json_encode(["code"=>"500","msg" => "Mailer Error! ". $mail->ErrorInfo ]);;
  return 0;
} else {
  echo json_encode(["code"=>"200","msg" => "Thank you ". $firstname .". Your message has been sent."]);
  return 1;
}

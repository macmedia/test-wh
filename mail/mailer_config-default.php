<?php
/* =============================================================
 * Setup the default user names and Password
 * for the mailer script
 * ============================================================ */

// Amazon SES Mail user credintials
// Needs to be an authorized SMTP user not IAM User
$SES_USER = "";
$SES_PASS = "";
$SES_HOST = "";
$SES_FROM = "";
$SES_NAME = "";


// Amazon S3 Storage user credintials
$S3_USER = "";
$S3_PASS = "";
$S3_BUCKET = "";
$S3_HOST_URLS = [
  "http"  => "http://s3-us-west-2.amazonaws.com/$S3_BUCKET",
  "https"  => "https://s3-us-west-2.amazonaws.com/$S3_BUCKET"
];
$S3_MAX_FILESIZE = 5000000;

?>

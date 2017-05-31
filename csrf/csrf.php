<?php
require_once('class.csrf.php');

$CSRF = new \security\csrf;
echo $CSRF::set_token();

?>

<?php namespace security;

/**
 *
 */
class CSRF
{

  function __construct()
  {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

  }

  public static function get_token(){
    return ( isset( $_SESSION['csrf_token'] ))? $_SESSION['csrf_token']:"WHY";
  }

  public static function set_token(){
    $_SESSION['csrf_token'] = sha1(mt_rand() . rand());
    return $_SESSION['csrf_token'];
  }


}

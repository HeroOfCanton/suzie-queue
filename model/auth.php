<?php
require 'config.php';

/*
 *
 *
 */
function auth($username, $pass){
  $username = "$username@".LDAP_DOMAIN;
  $ldap_conn = ldap_connect(LDAP_SERVER); 
  if($ldap_conn){
    $ldap_bind = ldap_bind($ldap_conn, $username, $pass);
    if($ldap_bind){
      ldap_unbind($ldap_conn);
      return 1;
    }
  }
  
  ldap_unbind($ldap_conn);
  return 0; 
}

function get_info($username){
}


function is_ta($username, $class){
}



//
//Helper Functions for LDAP
//
function _ldap_connect(){
  $ldap_conn = ldap_connect(LDAP_SERVER);
  if($ldap_conn){
    $ldap_bind = ldap_bind($ldap_conn, BIND_USER."@".LDAP_DOMAIN, BIND_PASSWD);
    if($ldap_bind){
      return $ldap_bind;
    } 
  }
  return NULL;
}

function _ldap_disconnct($ldap_bind){
  ldap_unbind($ldap_bind);
}

?>

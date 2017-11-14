<?php
require 'config.php';

/*
 *Functions for Authentication and Authorization.
 *
 *NOTE: All user and group 'names' are references to 
 *      sAMAccountName, not cn or displayName.
 */


/*
 *
 *
 */
function auth($username, $pass){
  $auth = 0;
  $username = $username."@".LDAP_DOMAIN;
  $ldap_conn = ldap_connect(LDAP_SERVER); 
  if($ldap_conn){
    $ldap_bind = ldap_bind($ldap_conn, $username, $pass);
    if($ldap_bind){
      $auth = 1;
    }
  }
  ldap_unbind($ldap_conn);
  return $auth; 
}


function get_info($username){
  $filter = "(sAMAccountName=$username)";
  $ldap_conn = _ldap_connect();
  
  $results = ldap_search($ldap_conn, BASE_OU, $filter);
  $entries = ldap_get_entries($ldap_conn, $results);
  //Do some error checking here!  
  //Consider writing a schema mapping in the config file
  //instead of hard coding the mapping

  $first_name = $entries[0]["givenname"][0];
  $last_name  = $entries[0]["sn"][0];
  
  _ldap_disconnect($ldap_conn);

  return array(
    "first_name" => $first_name,
    "last_name"  => $last_name,
  );
}




//
//Helper Functions for LDAP
//
function _ldap_connect(){
  $ldap_conn = ldap_connect(LDAP_SERVER);
  if($ldap_conn){
    $ldap_bind = ldap_bind($ldap_conn, BIND_USER."@".LDAP_DOMAIN, BIND_PASSWD);
    if($ldap_bind){
      return $ldap_conn;
    } 
  }
  return NULL;
}

function _ldap_disconnect($ldap_conn){
  ldap_unbind($ldap_conn);
}

?>

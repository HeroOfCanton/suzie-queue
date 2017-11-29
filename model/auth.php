<?php
require_once 'config.php';

/*
 *Functions for Authentication and Authorization.
 *
 *NOTE: All user and group 'names' are references to 
 *      sAMAccountName, not cn or displayName.
 */


/*
 *Returns true if user binds to LDAP
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

/*
 *Returns an array of information on $username from LDAP 
 *
 */
function get_info($username){
  $result = srch_by_sam($username); 
  if($result == NULL){
    return NULL;
  }

  $first_name = $result["givenname"][0];
  $last_name  = $result["sn"][0];
  $full_name  = $result["displayname"][0]; #We may need to fix this!

  #Touches the user entry in the sql table
  touch_user($username, $first_name, $last_name, $full_name);
 
  return array(
    "first_name" => $first_name,
    "last_name"  => $last_name,
  );
}




//Helper Functions for LDAP: No reason to call these from outside the model.

/*
 *Connect to LDAP server under the bind account in the config file
 *Returns an ldap_connection on success, NULL on failure.
 */
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

/*
 *Disconnects the bind account from LDAP
 */
function _ldap_disconnect($ldap_conn){
  ldap_unbind($ldap_conn);
}

/*
 *Converts a fully qualified DN to a sAMAccountName
 */
function dn_to_sam($dn){
  $filter = "(distinguishedName=$dn)";
  $ldap_conn = _ldap_connect();

  if($ldap_conn == NULL){
    return NULL;
  }

  $results = ldap_search($ldap_conn, BASE_OU, $filter);
  $entries = ldap_get_entries($ldap_conn, $results);

  if(!$entries["count"]){
    return NULL;
  }

  _ldap_disconnect($ldap_conn);
  return $entries[0]["samaccountname"][0];
}

/*
 *Performs an LDAP query on the sam
 *Returns only the first result since sams are unique
 */
function srch_by_sam($sam){
  if(empty($sam)){
    return NULL;
  }

  $ldap_conn = _ldap_connect();
  if($ldap_conn == NULL){
    return NULL;
  }

  $filter = "(sAMAccountName=$sam)";
  $results = ldap_search($ldap_conn, BASE_OU, $filter);
  $entries = ldap_get_entries($ldap_conn, $results);

  _ldap_disconnect($ldap_conn);
  if(!$entries["count"]){
    return NULL;
  }

  return $entries[0];
}

/*
 *Adds a user to to the "users" table in the database
 */
function touch_user($username, $first, $last, $full){
  $sql_conn = mysqli_connect(SQL_SERVER, SQL_USER, SQL_PASSWD, DATABASE);
  if(!$sql_conn){
    return 1;
  }

  $query = "INSERT IGNORE INTO users (username, first_name, last_name, full_name) VALUES ('".$username."','".$first."','".$last."','".$full."')";
  if(!mysqli_query($sql_conn, $query)){
    mysqli_close($sql_conn);
    return 1;
  }
  mysqli_close($sql_conn);
  return 0;
}
?>

<?php
require_once 'dbconstants.php';
function db_connect() {
//begin function
   $host = server;
   $username = username;
   $password = password;
   $db_name = dbname;
   $result = @ mysql_connect($host, $username, $password);
   if (!$result)
      return false;
   if (!@ mysql_select_db($db_name))
      return false;
   return $result;
}
function db_close($rs) {
   mysql_close($rs);
}
//end function
class dbfunctions {
   var $server;
   var $username;
   var $password;
   var $dbname;
   var $rs;
   var $db;
//Deletion of Records Generalized
   function mysql_delete($table, $condition) {
      $noerror = true;
      $sqld = "DELETE FROM " . $table . " WHERE " . $condition;
      if (($noerror = $this->mysql_qry($sqld, 0)))
         return $noerror;
   }
//Insertion of Records Generalized
   function mysql_insert($table, $fields, $values) {
      $noerror = true;
      $sqld = "INSERT INTO " . $table . " (" . $fields . ") VALUES (" . $values . ")";
      echo $sqld;
      if (($noerror = $this->mysql_qry($sqld, 0)))
         return $noerror;
   }
//Updation of Records Generalized
   function mysql_update($table, $sets, $condition) {
      $noerror = true;
      $sqld = "UPDATE " . $table . " SET " . $sets . " WHERE " . $condition;
      if (($noerror = $this->mysql_qry($sqld, 0))) {
         return $noerror;
      }
      else {
         return $noerror;
      }
   }
//Selecting of Records Generalized
   function mysql_select($table) {
      $noerror = true;
      if (($noerror = $this->mysql_qry($table, 1))) {
      }
      return $noerror;
   }
//Querying Database Generalized
   function mysql_qry($sql, $fetch) {
      $conn = db_connect();
      $noerror = true;
      if (!empty ($sql)) {
         $result = @ mysql_query($sql, $conn) or die(mysql_error());
         db_close($conn);
         if ($fetch)
              return $result;
         else
            return $noerror;
      }
      if (!$result) {
         $noerror = false;
      }
      else
         $noerror = false;
      if ($fetch)
               return $result;
      else
         return $noerror;
   }
}

?>
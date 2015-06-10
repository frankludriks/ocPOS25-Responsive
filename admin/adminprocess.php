<?php 

// admin/adminprocess.php

include("../includes/session.php");

class AdminProcess
{
   /* Class constructor */
   function AdminProcess() {
      global $session;
      /* Make sure administrator is accessing page */
      if(!$session->isAdmin()) {
         header("Location: ../index.php");
         return;
      }
      /* Admin submitted update user level form */
      if(isset($_POST['subupdlevel'])) {
         $this->procUpdateLevel();
      }
      /* Admin submitted create user form */
      else if(isset($_POST['subcreateuser'])) {
         $this->procCreateUser();
      }
      /* Admin submitted delete user form */
      else if(isset($_POST['subdeluser'])) {
         $this->procDeleteUser();
      }
      /* Admin submitted delete inactive users form */
      else if(isset($_POST['subdelinact'])) {
         $this->procDeleteInactive();
      }
      /* Should not get here, redirect to home page */
      else {
         header("Location: ../index.php");
      }
   }

   /**
    * procUpdateLevel - If the submitted username is correct,
    * their user level is updated according to the admin's
    * request.
    */
   function procUpdateLevel() {
      global $session, $database, $form;
      /* Username error checking */
      $subuser = $this->checkUsername("upduser");
      
      /* Errors exist, have user correct them */
      if($form->num_errors > 0) {
         $_SESSION['value_array'] = $_POST;
         $_SESSION['error_array'] = $form->getErrorArray();
         header("Location: ".$session->referrer);
      }
      /* Update user level */
      else {
         $database->updateUserField($subuser, "userlevel", (int)$_POST['updlevel']);
         header("Location: ".$session->referrer);
      }
   }
   
    /**
    * procCreateUser - Add user to the database.
    */
   function procCreateUser() {
      global $session, $database, $form;

      /* Convert username to all lowercase (by option) */
      if(ALL_LOWERCASE) {
         $_POST['adduser'] = strtolower($_POST['adduser']);
      }
      /* Registration attempt */
      $retval = $session->create_user($_POST['adduser'], $_POST['adduserpass'], $_POST['adduseremail']);
      
      /* Registration Successful */
      if($retval == 0) {
         $_SESSION['reguname'] = $_POST['adduser'];
         $_SESSION['regsuccess'] = true;
         header("Location: ".$session->referrer);
      }
      /* Error found with form */
      else if($retval == 1) {
         $_SESSION['value_array'] = $_POST;
         $_SESSION['error_array'] = $form->getErrorArray();
         header("Location: ".$session->referrer);
      }
      /* Registration attempt failed */
      else if($retval == 2) {
         $_SESSION['reguname'] = $_POST['adduser'];
         $_SESSION['regsuccess'] = false;
         header("Location: ".$session->referrer);
      }
   }
   
   /**
    * procDeleteUser - If the submitted username is correct,
    * the user is deleted from the database.
    */
   function procDeleteUser() {
      global $session, $database, $form;
      /* Username error checking */
      $subuser = $this->checkUsername("deluser");
      
      $userinfo = $database->getUserInfo($subuser);
      $userlevel = $userinfo['userlevel'];
      if($userlevel == '9') {
        $form->setError("deluser", "* Cannot delete Admin users! <br>");
      }
      
      /* Errors exist, have user correct them */
      if($form->num_errors > 0) {
         $_SESSION['value_array'] = $_POST;
         $_SESSION['error_array'] = $form->getErrorArray();
         header("Location: ".$session->referrer);
      } else {
       /* Delete user from database */
         $q = "DELETE FROM ".POS_USERS." WHERE username = '$subuser'";
         $database->query($q);
         header("Location: ".$session->referrer);
      }
   }
   
   /**
    * procDeleteInactive - All inactive users are deleted from
    * the database, not including administrators. Inactivity
    * is defined by the number of days specified that have
    * gone by that the user has not logged in.
    */
   function procDeleteInactive() {
      global $session, $database;
      $inact_time = $session->time - $_POST['inactdays']*24*60*60;
      $q = "DELETE FROM ".POS_USERS." WHERE timestamp < $inact_time "
          ."AND userlevel != ".ADMIN_LEVEL;
      $database->query($q);
      header("Location: ".$session->referrer);
   }

   
   /**
    * checkUsername - Helper function for the above processing,
    * it makes sure the submitted username is valid, if not,
    * it adds the appropriate error to the form.
    */
   function checkUsername($uname, $ban=false) {
      global $database, $form;
      /* Username error checking */
      $subuser = $_POST[$uname];
      $field = $uname;  //Use field name for username
      if(!$subuser || strlen($subuser = trim($subuser)) == 0) {
         $form->setError($field, "* Username not entered<br>");
      } else {
         /* Make sure username is in database */
         $subuser = stripslashes($subuser);
         if(strlen($subuser) < MIN_USER_LEN || strlen($subuser) > 30 ||
            !preg_match('/^([0-9a-z])+$/i', $subuser) ||
            (!$ban && !$database->usernameTaken($subuser))) {
            $form->setError($field, "* Username does not exist<br>");
         }
      }
      return $subuser;
   }

};



/* Initialize process */
$adminprocess = new AdminProcess;

?>

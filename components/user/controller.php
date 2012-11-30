<?php

    /*
    *  Copyright (c) Codiad & Kent Safranski (codiad.com), distributed
    *  as-is and without warranty under the MIT License. See 
    *  [root]/license.txt for more. This information must remain intact.
    */

    require_once('../../config.php');
    require_once('class.user.php');
    require_once('../project/class.project.php');
    
    //////////////////////////////////////////////////////////////////
    // Verify Session or Key
    //////////////////////////////////////////////////////////////////
    
    if($_GET['action']!='authenticate'){ checkSession(); }
    
    $User = new User();

    //////////////////////////////////////////////////////////////////
    // Authenticate
    //////////////////////////////////////////////////////////////////
    
    if($_GET['action']=='authenticate'){
        $User->username = $_POST['username'];
        $User->password = $_POST['password'];
        $User->Authenticate();
    }
    
    //////////////////////////////////////////////////////////////////
    // Logout
    //////////////////////////////////////////////////////////////////
    
    if($_GET['action']=='logout'){
        session_unset(); session_destroy(); session_start();
    }
    

    //////////////////////////////////////////////////////////////////
    // Assign User
    //////////////////////////////////////////////////////////////////
    
    if($_GET['action']=='assig'){
        $User->username = $_GET['username'];
        $a = $_GET["a"];
        $User->Assig($a);
    }

    //////////////////////////////////////////////////////////////////
    // Create User
    //////////////////////////////////////////////////////////////////
    
    /*if($_GET['action']=='create'){
        $User->username = $_GET['username'];
        $User->password = $_GET['password'];
        $User->Create();
    }
    
    //////////////////////////////////////////////////////////////////
    // Delete User
    //////////////////////////////////////////////////////////////////
    
    /*if($_GET['action']=='delete'){
        $User->username = $_GET['username'];
        $User->Delete();
    }
    
    //////////////////////////////////////////////////////////////////
    // Change Password
    //////////////////////////////////////////////////////////////////
    
    if($_GET['action']=='password'){
        $User->username = $_GET['username'];
        $User->password = $_GET['password'];
        $User->Password();
    }*/
    
    //////////////////////////////////////////////////////////////////
    // Change Project
    //////////////////////////////////////////////////////////////////
    
    if($_GET['action']=='project'){
        $User->username = $_SESSION['user'];
        $User->project  = $_GET['project'];
        $User->Project();
    }
    
    //////////////////////////////////////////////////////////////////
    // Verify User Account
    //////////////////////////////////////////////////////////////////
    
    if($_GET['action']=='verify'){
        $User->username = $_SESSION['user'];
        $User->Verify();
    }

?>
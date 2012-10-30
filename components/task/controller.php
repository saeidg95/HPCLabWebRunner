<?php

    /*
    *  Copyright (c) Codiad & Kent Safranski (codiad.com), distributed
    *  as-is and without warranty under the MIT License. See 
    *  [root]/license.txt for more. This information must remain intact.
    */

    require_once('../../config.php');
    require_once('class.task.php');
    
    //////////////////////////////////////////////////////////////////
    // Verify Session or Key
    //////////////////////////////////////////////////////////////////
 
    $Task = new Task();

    //////////////////////////////////////////////////////////////////
    // Create Task
    //////////////////////////////////////////////////////////////////
    
    if($_GET['action']=='create'){
        $Task->name = $_GET['name'];
        $Task->description = $_GET['description'];
        $Task->job =  $_GET['job'];
        $Task->type  =  $_GET['type'];
        $Task->path  =  $_GET['path'];
        $Task->Create();
    }
    
    //////////////////////////////////////////////////////////////////
    // Delete User
    //////////////////////////////////////////////////////////////////
    
    if($_GET['action']=='delete'){
        $Task->id = $_GET['tid'];
        $Task->Delete();
    }
    
    //////////////////////////////////////////////////////////////////
    // Change Password
    //////////////////////////////////////////////////////////////////
    
    if($_GET['action']=='password'){
        $User->username = $_GET['username'];
        $User->password = $_GET['password'];
        $User->Password();
    }
    
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
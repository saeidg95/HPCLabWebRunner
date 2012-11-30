<?php

/*
*  Copyright (c) Codiad & Kent Safranski (codiad.com), distributed
*  as-is and without warranty under the MIT License. See 
*  [root]/license.txt for more. This information must remain intact.
*/

require_once '../../Ssh2_crontab_manager/Ssh2_crontab_manager.php';

class User {

    //////////////////////////////////////////////////////////////////
    // PROPERTIES
    //////////////////////////////////////////////////////////////////

    public $username    = '';
    public $password    = '';
    public $project     = '';
    public $users       = '';
    public $actives     = '';
    
    //////////////////////////////////////////////////////////////////
    // METHODS
    //////////////////////////////////////////////////////////////////
    
    // -----------------------------||----------------------------- //
    
    //////////////////////////////////////////////////////////////////
    // Construct
    //////////////////////////////////////////////////////////////////
    
    public function __construct(){
        $this->users = getJSON('users.php');
        $this->actives = getJSON('active.php');
    }
    
    //////////////////////////////////////////////////////////////////
    // Authenticate
    //////////////////////////////////////////////////////////////////
    
    public function Authenticate(){
            GLOBAL $HOSTAUTH;

            $pass = false;
            try{
                $ssh_con = new Ssh2_crontab_manager($HOSTAUTH,"22",$this->username,$this->password);
            }catch(Exception $e){
                echo formatJSEND("error","Incorrect Username or Password");
            }
           

            if( $ssh_con->init ){
                $pass = true;
                $user = array( 'projects' => array() );

                //$users = getJSON('users.php');
                $exist = false;

                if( count($this->users) ){
                    foreach($this->users as $u){
                        if($u['username']==$this->username){
                            if( isset( $u["projects"] ) && count( $u["projects"] ) ){
                                foreach( $u["projects"] as $project  ){
                                    $user['projects'][] = $project;
                                }
                            }
                            $exist = true;
                            break;
                        }
                    }
                }

                $_SESSION['user'] = $this->username;
                $_SESSION['user_passwd'] = $this->password;
                

                $path_project = $this->username."/".$this->username;

                if( !$exist ){

                    $user['projects'][] = $path_project;

                    $this->users[] = array(
                        "username"=>$this->username,
                        "password"=>"",
                        "project"=> $path_project,
                        "projects" => $user['projects']
                    );
                   
                    $p = new Project();
                    $p->path = $path_project;
                    $p->name = $this->username;
                    $d = $p->CreateExt($this->username);
                    
                    if( !$d["success"] ){
                        echo formatJSEND("error",$d["msg"]);
                        return;
                    }

                    saveJSON('users.php',$this->users);
                }

                $_SESSION['projects'] = $user["projects"];

                if(  count(  $user['projects'] ) == 1){ 
                    $_SESSION['project'] = $path_project; 
                }else{
                    $_SESSION['project'] =  $user["projects"][0];
                    //todo make selector project load!!
                }

                echo formatJSEND("success",array("username"=>$this->username));
            }else{
                echo formatJSEND("error","Incorrect Username or Password");
            }

    }
    

    public function Create(){
        $this->EncryptPassword();
        $pass = $this->checkDuplicate();
        if($pass){
            $this->users[] = array("username"=>$this->username,"password"=>$this->password,"project"=>"");
            saveJSON('users.php',$this->users);
            echo formatJSEND("success",array("username"=>$this->username));
        }else{
            echo formatJSEND("error","The Username is Already Taken");
        }
    }


    //////////////////////////////////////////////////////////////////
    // Create Account
    //////////////////////////////////////////////////////////////////
    /*
    public function Create(){
        $this->EncryptPassword();
        $pass = $this->checkDuplicate();
        if($pass){
            $this->users[] = array("username"=>$this->username,"password"=>$this->password,"project"=>"");
            saveJSON('users.php',$this->users);
            echo formatJSEND("success",array("username"=>$this->username));
        }else{
            echo formatJSEND("error","The Username is Already Taken");
        }
    }
    
    //////////////////////////////////////////////////////////////////
    // Delete Account
    //////////////////////////////////////////////////////////////////
    
    public function Delete(){
        // Remove User
        $revised_array = array();
        foreach($this->users as $user=>$data){
            if($data['username']!=$this->username){
                $revised_array[] = array("username"=>$data['username'],"password"=>$data['password'],"project"=>$data['project']);
            }
        }
        // Save array back to JSON
        saveJSON('users.php',$revised_array);
        
        // Remove any active files
        foreach($this->actives as $active=>$data){
            if($this->username==$data['username']){
                unset($this->actives[$active]);
            }
        }
        saveJSON('active.php',$this->actives);
        
        // Response
        echo formatJSEND("success",null);
    }
    
    //////////////////////////////////////////////////////////////////
    // Change Password
    //////////////////////////////////////////////////////////////////
    
    public function Password(){
        $this->EncryptPassword();
        $revised_array = array();
        foreach($this->users as $user=>$data){
            if($data['username']==$this->username){
                $revised_array[] = array("username"=>$data['username'],"password"=>$this->password);
            }else{
                $revised_array[] = array("username"=>$data['username'],"password"=>$data['password'],"project"=>$data['project']);
            }
        }
        // Save array back to JSON
        saveJSON('users.php',$revised_array);
        // Response
        echo formatJSEND("success",null);
    }
    */

    //////////////////////////////////////////////////////////////////
    // Set Current Project
    //////////////////////////////////////////////////////////////////
    
    public function Assig($a){

        $revised_array = array();
        $revised_array_t = array();
        if( isset($_SESSION["project"]) ){

            foreach($this->users as $user=>$data){
                $revised_array_t = $data;

                if($this->username==$data['username']){

                    if($a){
                        //Agrego el proyecto al usuario
                        $revised_array_t["projects"][] = $_SESSION["project"];
                    }else{
                        //Elimino el proyecto del usuario
                        $i = -1;
                        if( count( $revised_array_t["projects"] ) ){
                            foreach ( $revised_array_t["projects"] as $k => $p) {
                                if( $p == $_SESSION["project"] ){
                                    $i = $k;
                                    break;
                                }
                            }

                            if( $i>=0){
                               unset( $revised_array_t["projects"][$i]);
                            }

                        }
                    }

                    $revised_array[] = $revised_array_t;
                }else{
                    $revised_array[] = $data;
                }

            }

            // Save array back to JSON
            saveJSON('users.php',$revised_array);
            // Response
            echo formatJSEND("success","User '".$this->username."' ". ( ($a)?"assigned.":"Unassinged."  ) );

        }else{
            // Response
            echo formatJSEND("error","Empty project set");
        }
            
        
    }
    
    //////////////////////////////////////////////////////////////////
    // Check Duplicate
    //////////////////////////////////////////////////////////////////
    
    public function CheckDuplicate(){
        $pass = true;
        foreach($this->users as $user=>$data){
            if($data['username']==$this->username){
                $pass = false;
            }
        }
        return $pass;
    }
    
    //////////////////////////////////////////////////////////////////
    // Verify Account Exists
    //////////////////////////////////////////////////////////////////
    
    public function Verify(){
        $pass = 'false';
        foreach($this->users as $user=>$data){
            if($this->username==$data['username']){
                $pass = 'true';
            }
        }
        echo($pass);
    }
    
    //////////////////////////////////////////////////////////////////
    // Encrypt Password
    //////////////////////////////////////////////////////////////////
    
    private function EncryptPassword(){
        //$this->password = sha1(md5($this->password));
    }
    
}
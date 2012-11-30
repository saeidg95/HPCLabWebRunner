<?php

/*
*  Copyright (c) Codiad & Kent Safranski (codiad.com), distributed
*  as-is and without warranty under the MIT License. See 
*  [root]/license.txt for more. This information must remain intact.
*/

class Project {

    //////////////////////////////////////////////////////////////////
    // PROPERTIES
    //////////////////////////////////////////////////////////////////

    public $name        = '';
    public $path        = '';
    public $projects    = '';
    public $no_return   = false;
    
    //////////////////////////////////////////////////////////////////
    // METHODS
    //////////////////////////////////////////////////////////////////
    
    // -----------------------------||----------------------------- //
    
    //////////////////////////////////////////////////////////////////
    // Construct
    //////////////////////////////////////////////////////////////////
    
    public function __construct(){
        $this->projects = getJSON('projects.php');
    }
    
    //////////////////////////////////////////////////////////////////
    // Get First (Default, none selected)
    //////////////////////////////////////////////////////////////////
    
    public function GetFirst(){
                
        $this->name = $this->projects[0]['name'];
        $this->path = $this->projects[0]['path'];
        
        // Set Sessions
        $_SESSION['project'] = $this->path;
        
        if(!$this->no_return){
            echo formatJSEND("success",array("name"=>$this->name,"path"=>$this->path));
        }
    }
    
    //////////////////////////////////////////////////////////////////
    // Get Name From Path
    //////////////////////////////////////////////////////////////////
    
    public function GetName(){
        foreach($this->projects as $project=>$data){
            if($data['path']==$this->path){
                $this->name = $data['name'];
            }
        }
        return $this->name;
    }
    
    //////////////////////////////////////////////////////////////////
    // Open Project
    //////////////////////////////////////////////////////////////////
    
    public function Open(){
        $pass = false;
        foreach($this->projects as $project=>$data){
            if($data['path']==$this->path){
                $pass = true;
                $this->name = $data['name'];
                $_SESSION['project'] = $data['path'];
            }
        }
        if($pass){
            echo formatJSEND("success",array("name"=>$this->name,"path"=>$this->path));
        }else{
            echo formatJSEND("error","Error Opening Project");
        }
    }
    
    //////////////////////////////////////////////////////////////////
    // Create
    //////////////////////////////////////////////////////////////////
    
    public function Create(){

        $this->path = $_SESSION["user"]."/".$this->SanitizePath();

        $pass = $this->checkDuplicate();
        if($pass){

            if( $this->CreatePath($_SESSION["user"]) ){
                $this->projects[] = array("name"=>$this->name,"path"=>$this->path);
                saveJSON('projects.php',$this->projects);

                $users =  getJSON('users.php');
                $users_to_saved=array();

                foreach($users as $u){
                    if($u['username']==$_SESSION["user"] ){
                        $u["projects"][] = $this->path;
                        $_SESSION["projects"][] = $this->path;
                    }

                    $users_to_saved[] = $u;
                }
                saveJSON('users.php',$users_to_saved);

                echo formatJSEND("success",array("name"=>$this->name,"path"=>$this->path));
            }else{
                echo formatJSEND("error","Error trying of create filepath for the project");
            }

                
        }else{
            echo formatJSEND("error","A Project With the Same Name or Path Exists");
        }
    }
    
    public function CreateExt($user){

        $this->path = $user."/".$this->SanitizePath();

        $pass = $this->checkDuplicate();

        $data = array("success"=>true, "msg" => "");
        
        if($pass){

            if( $this->CreatePath($user) ){
                $this->projects[] = array("name"=>$this->name,"path"=>$this->path);
                saveJSON('projects.php',$this->projects);
                $data["success"] = true;
                $data["msg"] = "Project: '".$this->name."' has been created successful.";
            }else{
                $data["success"] = false;
                $data["msg"] = "Error trying of create filepath for the project";
            }

                
        }else{
            $data["success"] = false;
            $data["msg"] = "A Project With the Same Name or Path Exists";
        }

        return $data;
    }

    //////////////////////////////////////////////////////////////////
    // Delete Project
    //////////////////////////////////////////////////////////////////
    
    public function Delete(){
        $revised_array = array();
        foreach($this->projects as $project=>$data){
            if($data['path']!=str_replace("/","",$this->path)){
                $revised_array[] = array("name"=>$data['name'],"path"=>$data['path']);
            }
        }
        // Save array back to JSON
        saveJSON('projects.php',$revised_array);
        // Response
        echo formatJSEND("success",null);
    }
    
    ////////////////////////////////////
    // CREATE PATH FILE PROJECT BY USER
    ////////////////////////////////////
    
    public function CreatePath($user){
        $path_real = str_replace("%user", $user, WORKSPACE_USERS);
        if( ! file_exists( $path_real)  ){
            //echo $path_real;
            //if( is_writable($path_real) ){
                if( !mkdir($path_real) ){
                    echo "No se puede crear";
                    return false;
                }
        }

        $ppath=WORKSPACE . "/" . $this->path;
        if( !file_exists($ppath) ){ 
            if( ! mkdir($ppath) )
              return false;
        }
        $resources_path = $ppath."/resources";
        if( !file_exists($resources_path) ){ 
            if( ! mkdir($resources_path) )
              return false;
        }

        return true;

    }

    public static function getRealPath($project){
        return str_replace("%user", $_SESSION["user"], WORKSPACE_USERS)."/".$project;
    }

    //////////////////////////////////////////////////////////////////
    // Check Duplicate
    //////////////////////////////////////////////////////////////////
    
    public function CheckDuplicate(){
        $pass = true;
        foreach($this->projects as $project=>$data){
            if($data['path']==$this->path){
                $pass = false;
            }
        }
        return $pass;
    }
    
    //////////////////////////////////////////////////////////////////
    // Sanitize Path
    //////////////////////////////////////////////////////////////////
    
    public function SanitizePath(){
        $sanitized = str_replace(" ","_",$this->name);
        return preg_replace('/[^\w-]/', '', $sanitized);
    }
    
}
<?php

/*
*  Copyright (c) Codiad & Kent Safranski (codiad.com), distributed
*  as-is and without warranty under the MIT License. See 
*  [root]/license.txt for more. This information must remain intact.
*/

require_once '../../Ssh2_crontab_manager/Ssh2_crontab_manager.php';

class Task {

    //////////////////////////////////////////////////////////////////
    // PROPERTIES
    //////////////////////////////////////////////////////////////////

    public $id          = '';
    public $name        = '';
    public $description = '';
    public $project     = '';
    public $type        = '';
    public $job         = '';
    public $path         = '';
    
    public $tasks       = '';

    //////////////////////////////////////////////////////////////////
    // METHODS
    //////////////////////////////////////////////////////////////////
    
    // -----------------------------||----------------------------- //
    
    //////////////////////////////////////////////////////////////////
    // Construct
    //////////////////////////////////////////////////////////////////
    
    public function __construct(){
        $this->tasks = getJSON('tasks.php');
        $this->actives = getJSON('active.php');
    }
        
    //////////////////////////////////////////////////////////////////
    // Create Task
    //////////////////////////////////////////////////////////////////
    
    public function Create(){
	 
        GLOBAL $HOSTAUTH;
        $this->id = time();
        $path_bin = WORKSPACE."/".$_SESSION['project']."/.bin";

        if( ! is_dir( $path_bin ) ){
            if( ! mkdir($path_bin)  ){
                   echo formatJSEND("error","Error to try created run bin path.");    
                   return;
            }
        }

        if( !$this->checkExist() ){
            $this->tasks[] = array(
                "id"=>$this->id,
                "name"=>$this->name,
                "description"=>$this->description,
                "job"=>$this->job,
                "type"=>$this->type,
                "path"=>$this->path,
                "project"=>$_SESSION['project']
            );

            $cron_job_file = $path_bin."/".$this->id.".sh";

            $handle = @fopen($cron_job_file, 'w') or die('Cannot open file:  '.$cron_job_file);
            $data = "#!/bin/bash\n".$this->type."\t".WORKSPACE.$this->path;
            @fwrite($handle, $data);
            fclose($handle);
            
            /****TODO ADD JOB LINE ON CRON TAB FOR USER***/
            
                $users = getJSON('users.php');
                $current;
                foreach($users as $user){ if($user["username"] == $_SESSION["user"]){ $current = $user; break;} }

		  //echo $this->job.'    '.$cron_job_file .'  >/dev/null 2>&1';          

                $ssh_con = new Ssh2_crontab_manager($HOSTAUTH,"22",$current["username"],$_SESSION['user_passwd']);
                $ssh_con->append_cronjob(  $this->job.'    '.$cron_job_file .'  >/dev/null 2>&1' );  


            saveJSON('tasks.php',$this->tasks);
            echo formatJSEND("success",array("name"=>$this->name));
        }else{
            echo formatJSEND("error","The Task is Already Register. Error ID");
        }
            
    }


    //////////////////////////////////////////////////////////////////
    // Delete Task
    //////////////////////////////////////////////////////////////////
    
    public function Delete(){

      GLOBAL $HOSTAUTH;
        if( $this->checkExist() ){
            $news=array();
        
            foreach ($this->tasks as $key => $data) {
                if( $data["id"] != $this->id ){
                    $news[] = $data;
                }else{
                    $task = $data;
                }
            }

            $users = getJSON('users.php');
            $current;
            foreach($users as $user){ if($user["username"] == $_SESSION["user"]){ $current = $user; break;} }
                
            $path_bin = WORKSPACE."/".$_SESSION['project']."/.bin/".$this->id.".sh";
            $cron_job_file =/*$task["job"]."    ".*/$path_bin;

            $new_line = str_replace("*","\*", $cron_job_file);
            $new_line = str_replace("/","\/", $new_line);
            $new_line = "/".str_replace(".","\.", $new_line)."/";

            $ssh_con = new Ssh2_crontab_manager($HOSTAUTH,"22",$current["username"],$_SESSION['user_passwd']); 
            $ssh_con->remove_cronjob($new_line);  


            if( !@unlink($path_bin) ){
                $r = "Archivo borrado correctamente.";

            }else{
                $r = "Archivo NO borrado.";
            }

            saveJSON('tasks.php',$news);
            echo formatJSEND("success",$r);
        }else{
            echo formatJSEND("error","Dont exist Task. Error ID");
        }
            
    }

    
    //////////////////////////////////////////////////////////////////
    // Set Current Project
    //////////////////////////////////////////////////////////////////
    
    public function Project(){
        $revised_array = array();
        foreach($this->users as $user=>$data){
            if($this->username==$data['username']){
                $revised_array[] = array("username"=>$data['username'],"password"=>$data['password'],"project"=>$this->project);
            }else{
                $revised_array[] = array("username"=>$data['username'],"password"=>$data['password'],"project"=>$data['project']);
            }
        }
        // Save array back to JSON
        saveJSON('users.php',$revised_array);
        // Response
        echo formatJSEND("success",null);
    }

    public function checkExist(){
        foreach ($this->tasks as $key => $value) {
            if( $value["id"] == $this->id ){
                return true;
            }
        }
        return false;
    }

}
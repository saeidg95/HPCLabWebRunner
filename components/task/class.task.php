<?php

/*
*  Copyright (c) Codiad & Kent Safranski (codiad.com), distributed
*  as-is and without warranty under the MIT License. See 
*  [root]/license.txt for more. This information must remain intact.
*/

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
        $this->id = time();
        if( !$this->checkExist() ){
            $this->tasks[] = array(
                "id"=>$this->id,
                "name"=>$this->name,
                "description"=>$this->description,
                "job"=>$this->job,
                "type"=>$this->type,
                "path"=>$this->path,
                "project"=>""
            );
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
        if( $this->checkExist() ){
            $news=array();
            foreach ($this->tasks as $key => $data) {
                if( $data["id"] != $this->id ){
                    $news[] = $data;
                }
            }
            saveJSON('tasks.php',$news);
            echo formatJSEND("success",null);
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
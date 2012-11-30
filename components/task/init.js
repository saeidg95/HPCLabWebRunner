/*
*  Copyright (c) Codiad & Kent Safranski (codiad.com), distributed
*  as-is and without warranty under the MIT License. See 
*  [root]/license.txt for more. This information must remain intact.
*/

$(function(){ task.init(); });

var task = {

    controller : 'components/task/controller.php',
    dialog : 'components/task/dialog.php',
    
    //////////////////////////////////////////////////////////////////
    // Initilization
    //////////////////////////////////////////////////////////////////

    init : function(){
        
    },
        
    //////////////////////////////////////////////////////////////////
    // Open the task manager dialog
    //////////////////////////////////////////////////////////////////
    
    list : function(){
        $('#modal-content form').die('submit'); // Prevent form bubbling
        modal.load(600,task.dialog+'?action=list');
    },
    
    //////////////////////////////////////////////////////////////////
    // Create Task
    //////////////////////////////////////////////////////////////////
    
    create_new : function(){
        modal.load(400,task.dialog+'?action=create');
        $('#modal-content form').live('submit',function(e){
            e.preventDefault();
            var filelist=new Array();


            $.get(project.controller+'?action=get_current',function(data){
                var project_info = jsend.parse(data);
                if(project_info!='error'){
                    //filemanager.index('/'+project_info.path);
                    //task.project(project_info.path);
                    //message.success('Project Loaded');
                }
            }); 

            var name        = $('#modal-content form input[name="name"]').val();
            var description = $('#modal-content form input[name="description"]').val();
            var job         = $('#modal-content form input[name="job"]').val();
            var type         = $('#modal-content form select[name="type"]').val();

            if( $.trim(name).length<=0){
                message.error('Name is required');
            }else if($.trim(job).length<=0){
                message.error('Job is required');
            }else if($.trim(type).length<=0){
                message.error('Type is required');
            }else{
                $.get(task.controller,{ action:'create',name:name,description:description,job:job,type:type },function(data){
                    create_response = jsend.parse(data);
                    if(create_response!='error'){
                        message.success('Task has been Created');
                        task.list();
                    }
                });
            }
        });
    },
    create_by_file : function(path){
        modal.load(400,task.dialog+'?action=create&path='+path);
        $('#modal-content form').live('submit',function(e){
            e.preventDefault();

            var path        = $('#modal-content form input[name="path"]').val();
            var name        = $('#modal-content form input[name="name"]').val();
            var description = $('#modal-content form input[name="description"]').val();
            var job         = $('#modal-content form input[name="job"]').val();
            var type         = $('#modal-content form select[name="type"]').val();

            if( $.trim(name).length<=0){
                message.error('Name is required');
            }else if($.trim(job).length<=0){
                message.error('Job is required');
            }else if($.trim(type).length<=0){
                message.error('Type is required');
            }else{
                $.get(task.controller,{ action:'create',name:name,description:description,job:job,type:type,path:path },function(data){
                    create_response = jsend.parse(data);
                    if(create_response!='error'){
                        message.success('Task has been Created');
                        task.list();
                    }
                });
            }
        });
    },
    //////////////////////////////////////////////////////////////////
    // Delete Task
    //////////////////////////////////////////////////////////////////

    delete : function(id, name){
        modal.load(400,task.dialog+'?action=delete&tid='+id+'&name='+name);
        $('#modal-content form').live('submit',function(e){
            e.preventDefault();
            var name = $('#modal-content form input[name="tid"]').val();
            $.get(task.controller+'?action=delete&tid='+id,function(data){
                delete_response = jsend.parse(data);
                if(delete_response!='error'){
                    message.success('Task Deleted')
                    task.list();
                }
            });
        });
    },
    
    //////////////////////////////////////////////////////////////////
    // Change Current Project
    //////////////////////////////////////////////////////////////////
    
    project : function(project){
        $.get(task.controller+'?action=project&project='+project);
    }


};
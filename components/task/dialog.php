<?php

    /*
    *  Copyright (c) Codiad & Kent Safranski (codiad.com), distributed
    *  as-is and without warranty under the MIT License. See 
    *  [root]/license.txt for more. This information must remain intact.
    */

    require_once('../../config.php');
    
    //////////////////////////////////////////////////////////////////
    // Verify Session or Key
    //////////////////////////////////////////////////////////////////
    
    checkSession();

    switch($_GET['action']){
    
        //////////////////////////////////////////////////////////////
        // List tasks
        //////////////////////////////////////////////////////////////
        
        case 'list':
        
            ?>
            <label>Task List</label>
            <div id="task-list">
            <table width="100%">
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Type</th>
                    <th>Run Job</th>
                    <th>File Path</th>
                </tr>
            <?php
            // Get tasks JSON data
            $tasks = getJSON('tasks.php');
            foreach($tasks as $task=>$data){      
                if( isset($_SESSION["project"] ) ){
                    if( $data['project'] == $_SESSION["project"] ){
            ?>
                    <tr>
                        <td><?php echo($data['name']); ?></td>
                        <td><?php echo($data['description'])?></td>
                        <td><?php echo ($data["type"])?></td>
                        <td><?php echo ($data["job"])?></td>
                        <td><?php echo ($data["path"])?></td>
                        <td><a onclick="task.delete('<?php echo($data['id']); ?>','<?php echo($data['name']); ?>');" class="icon">[</a></td>
                    </tr>
            <?php


                    }
                }
            }
            ?>
            </table>
            </div>
            <button class="btn-right" onclick="modal.unload();return false;">Close</button>
            <?php
            
            break;
            
        //////////////////////////////////////////////////////////////////////
        // Create New User
        //////////////////////////////////////////////////////////////////////
        
        case 'create':
        
            ?>
            <form>
            <label>Name</label>
            <input type="text" name="name" autofocus="autofocus" autocomplete="off">
            <label>Description</label>
            <input type="text" name="description">
            <label>Job</label>
            <input type="hidden" name="path"  value="<?php echo (( isset($_GET["path"]) )?$_GET["path"]:"") ?>"/>
            <div class="crontab-editor">

                <div id="minutes-editor" class="editor-section">

                    <a href="javascript:void(0);" class="label" onclick="setminutes(this);" >Minutes</a>
                    <span id="minutes-set">*</span>

                    <div class="set-section" >
                        <!--Set minutes -->
                        <select class="every-n-minutes">
                            <option value="*">Every minutes</option>
                            <?php for($i=1; $i<60; $i++): ?>
                                <option value="*/<?php echo $i; ?>">Every <?php echo $i; ?> minutes</option>
                            <?php endfor;?>
                         </select>

                         <select class="selected-minutes" multiple="multiple" >
                            <?php for($i=1; $i<60; $i++): ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                            <?php endfor;?>
                         </select>

                    </div>
                </div>


            </div>
            <input type="text" name="job" readonly="readonly">
            <label>Type</label>
            <select name="type">
                <option value="php">PHP</option>
                <option value="python">Python</option>
                <option value="gcc">C++</option>
            </select>
            <pre>File path: <?php echo (( isset($_GET["path"]) )?$_GET["path"]:"") ?></pre>
            <button class="btn-left">Create Task</button><button class="btn-right" onclick="task.list();return false;">Cancel</button>
            </form>
            <?php
            break;

        case 'delete':
        
        ?>
            <form>
            <input type="hidden" name="tid" value="<?php echo($_GET['tid']); ?>">
            <label>Confirm Task Deletion</label>
            <pre>Task name: <?php echo($_GET['name']); ?></pre>
            <button class="btn-left">Confirm</button><button class="btn-right" onclick="task.list();return false;">Cancel</button>
             </form>
            <?php

            break;
            
        
    }
    
?>
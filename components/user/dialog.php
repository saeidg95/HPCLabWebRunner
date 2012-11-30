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
        // List Projects
        //////////////////////////////////////////////////////////////
        
        case 'list':
        
            ?>
            <label>User List</label>
            <div id="project-list">
            <table width="100%">
                <tr>
                    <th>User</th>
                    <th width="3">Assig?</th>
                    <th width="3"></th>
                </tr>
            <?php
        
            // Get projects JSON data
            $users = getJSON('users.php');
            $project_part = explode("/", $_SESSION["project"]);
            ?>

            <?php  foreach($users as $user=>$data): ?>
                <?php if( isset( $_SESSION["project"] ) ): ?>
                    <tr>
                    <td><?php echo($data['username']); ?></td>

                    <?php if( $project_part[0] == $_SESSION["user"] ):?>

                        <?php if( $_SESSION['user'] == $data['username'] ):?>
                            <td><span class="icon">W</span></td>
                            <td></td>
                        <?php else: ?>
                            <?php if( !in_array($_SESSION["project"], $data["projects"] ) ): ?>
                                <td></td>
                                <td><a onclick="user.assig('<?php echo($data['username']); ?>',1);" class="icon">Z</a></td>
                            <?php else: ?>
                                <td><span class="icon">W</span></td>
                                <td><a onclick="user.assig('<?php echo($data['username']); ?>',0);" class="icon">Y</a></td>
                            <?php endif;?>
                        <?php endif;?>

                    <?php else:?>
                        <?php if( !in_array($_SESSION["project"], $data["projects"] ) ): ?>
                            <td></td>
                            <td></td>
                        <?php else: ?>
                            <td><span class="icon">W</span></td>
                            <td></td>
                        <?php endif;?>
                    <?php endif;?>
                    </tr>
                <?php endif;?>
            <?php endforeach;?>

               
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
            <label>Userame</label>
            <input type="text" name="username" autofocus="autofocus" autocomplete="off">
            <label>Password</label>
            <input type="password" name="password1">
            <label>Confirm Password</label>
            <input type="password" name="password2">
            <button class="btn-left">Create Account</button><button class="btn-right" onclick="user.list();return false;">Cancel</button>
            <form>
            <?php
            break;
            
        //////////////////////////////////////////////////////////////////////
        // Delete User
        //////////////////////////////////////////////////////////////////////
        
        case 'delete':
        
        ?>
            <form>
            <input type="hidden" name="username" value="<?php echo($_GET['username']); ?>">
            <label>Confirm User Deletion</label>
            <pre>Account: <?php echo($_GET['username']); ?></pre>
            <button class="btn-left">Confirm</button><button class="btn-right" onclick="user.list();return false;">Cancel</button>
            <?php
            break;
            
        //////////////////////////////////////////////////////////////////////
        // Change Password
        //////////////////////////////////////////////////////////////////////
        
        case 'password':
        
        ?>
            <form>
            <input type="hidden" name="username" value="<?php echo($_GET['username']); ?>">
            <label>New Password</label>
            <input type="password" name="password1" autofocus="autofocus">
            <label>Confirm Password</label>
            <input type="password" name="password2">
            <button class="btn-left">Change <?php echo(ucfirst($_GET['username'])); ?>'s Password</button><button class="btn-right" onclick="user.list();return false;">Cancel</button>
            <?php
            break;
        
    }
    
?>
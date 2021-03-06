<?php require_once('config.php'); ?>
<!doctype html>

<head>
    <meta charset="utf-8">
    <title>CODIAD</title>
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/fonts.css">
    <link rel="stylesheet" href="css/screen.css">
    <link rel="stylesheet" href="css/editorjob.css">
</head>

<body>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script>!window.jQuery && document.write(unescape('%3Cscript src="js/jquery-1.7.2.min.js"%3E%3C/script%3E'));</script>
    <script src="js/jquery-ui-1.8.23.custom.min.js"></script>
    <script src="js/jquery.css3.min.js"></script>
    <script src="js/jquery.easing.js"></script>
    <script src="js/system.js"></script>
    <script src="js/editorjob.js"></script>
    
    <div id="message"></div>

    <?php
    
    //////////////////////////////////////////////////////////////////
    // NOT LOGGED IN
    //////////////////////////////////////////////////////////////////
    
    if(!isset($_SESSION['user'])){
    
        $path = rtrim(str_replace("index.php", "", $_SERVER['PHP_SELF']),"/");

        $users = file_exists($_SERVER['DOCUMENT_ROOT'] . $path . "/data/users.php");
        $projects = file_exists($_SERVER['DOCUMENT_ROOT'] . $path . "/data/projects.php");
        $active = file_exists($_SERVER['DOCUMENT_ROOT'] . $path . "/data/active.php");
        
        if(!$users && !$projects && !$active){
            // Installer
            require_once('components/install/view.php');
        }else{
            // Login form
            ?>
            
            <form id="login" method="post" style="position: fixed; width: 350px; top: 30%; left: 50%; margin-left: -175px; padding: 35px;">
            
                <label><span class="icon">+</span> Username</label>
                <input type="text" name="username" autofocus="autofocus" autocomplete="off">
                
                <label><span class="icon">U</span> Password</label>
                <input type="password" name="password">
                
                <button>Login</button>
            
            </form>
            
            <script src="components/user/init.js"></script>
            <?php 
        
        } 
    
    //////////////////////////////////////////////////////////////////
    // AUTHENTICATED
    //////////////////////////////////////////////////////////////////
    
    }else{
    
    ?>

    <div id="workspace">
    
        <div id="sb-left" class="sidebar">
        
            <div class="sb-left-content">
            
                <a id="lock-left-sidebar" class="icon">U</a>
        
                <div id="context-menu" data-path="" data-type="">
                    <a data-action="new_file" class="directory-only"><span class="icon">l</span>New File</a>
                    <a data-action="new_directory" class="directory-only"><span class="icon">s</span>New Folder</a>
                    <hr class="directory-only">
                    <a data-action="upload" class="directory-only"><span class="icon">v</span>Upload Files</a>
                    <hr class="directory-only">
                    <a data-action="copy" class="both"><span class="icon">m</span>Copy</a>
                    <a data-action="paste" class="directory-only"><span class="icon">n</span>Paste</a>
                    <hr class="non-root">
                    <a data-action="rename" class="non-root"><span class="icon">&amp;</span>Rename</a>
                    <hr class="non-root">
                    <a data-action="delete" class="non-root"><span class="icon">[</span>Delete</a>
                    <hr>
                    <a data-action="backup"><span class="icon">x</span>Download</a>
                    <hr>
                    <a data-action="addtask" class="file-only"><span class="icon">x</span>Add task</a>
                </div>
            
                <div id="file-manager"></div>
                
                <ul id="active-files"></ul>
            
            </div>
        
            <div class="sidebar-handle"><span>||</span></div>
    
        </div>
    
        <div id="cursor-position">Ln: 0 &middot; Col: 0</div>
    
        <div id="editor-region">
        
            <div id="editor-bottom-bar">
                <div id="current-file"></div>
            </div>
        
        </div>
        
        
    
        <div id="sb-right" class="sidebar">
        
            <div class="sidebar-handle"><span>||</span></div>
        
            <div class="sb-right-content">
            
                <?php
                
                ////////////////////////////////////////////////////////////
                // Load Right Bar
                ////////////////////////////////////////////////////////////
                
                $right_bar = file_get_contents(COMPONENTS . "/right_bar.json");
                $right_bar = json_decode($right_bar,true);
                foreach($right_bar as $item=>$data){
                    
                    if($data['title']=='break'){
                        echo("<hr>");
                    }else{
                        echo('<a onclick="'.$data['onclick'].'"><span class="icon">'.$data['icon'].'</span>'.$data['title'].'</a>');
                    }
                    
                }
                
                ?>
            
            </div>

        </div>
        
    </div>
    
    <div id="modal-overlay"></div>
    <div id="modal"><div id="drag-handle" class="icon">0</div><div id="modal-content"></div></div>
    
    <iframe id="download"></iframe>

    
    <!-- COMPONENTS -->    
    <?php
    
        //////////////////////////////////////////////////////////////////
        // LOAD COMPONENTS
        //////////////////////////////////////////////////////////////////
    
        $components = file_get_contents(COMPONENTS . "/load.json");
        $components = json_decode($components,true);
        
        // JS
        foreach($components as $component){
            if(file_exists(COMPONENTS . "/" . $component . "/init.js")){
                echo('<script src="components/'.$component.'/init.js"></script>"');
            }
        }
        
        
        // CSS
        foreach($components as $component){
            if(file_exists(COMPONENTS . "/" . $component . "/screen.css")){
                echo('<link rel="stylesheet" href="components/'.$component.'/screen.css">');
            }
        }
    
    }
    
    ?>
    <!-- ACE -->
    <script src="components/editor/ace-editor/ace.js"></script>

</body>
</html>
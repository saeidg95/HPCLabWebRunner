<?php 

require_once 'Ssh2_crontab_manager/Ssh2_crontab_manager.php';

$ssh_con = new Ssh2_crontab_manager("XXX.XX.XX.XX","22","user","password");

$new_cronjobs = array(  
    '0 0 1 * * home/path/to/command/the_command.sh',  
    '30 8 * * 6 home/path/to/command/the_command.sh >/dev/null 2>&1'  
);  

$ssh_con->append_cronjob($new_cronjobs);  


?>
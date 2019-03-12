<?php
    $options = getopt("u:h:p:", array("help","file:","dry_run","create_table",));
    $user = isset($options['u']);
    $pass = isset($options['p']);
    $host = isset($options['h']);
    $file = isset($options['file']);
    $create_table = isset($options['create_table']);
    $dry_run = isset($options['dry_run']);
    $help = isset($options['help']);

    if($user === false || $host === false) {
        exit("Error Username and Host required");
    }
?>
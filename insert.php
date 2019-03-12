<?php
    $options = getopt("u:h:p:", array("help","file:","dry_run","create_table",));
    $user = isset($options['u']);
    $pass = isset($options['p']);
    $host = isset($options['h']);
    $file = isset($options['file']);
    $create_table = isset($options['create_table']);
    $dry_run = isset($options['dry_run']);
    $help = isset($options['help']);

    if($help === true) {
        exit("--file [csv file name] – this is the name of the CSV to be parsed
--create_table – this will cause the MySQL users table to be built (and no further action will be taken)
--dry_run – this will be used with the --file directive in the instance that we want
            to run the script but not insert into the DB. All other functions will be executed,
            but the database won't be altered.
-u – MySQL username
-p – MySQL password
-h – MySQL host
--help – which will output the above list of directives with details.");
    }

    if($user === false || $host === false) {
        exit("Error Username and Host required");
    }

    $password;

    if($pass === false) {
        $password = "";
    }
    else {
        $password = $options['p'];
    }
?>
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

    if($create_table === true) {
        $servername = $options['h'];
        $username = $options['u'];
        $dbname = "test";

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        drop_table($conn);
        create_table($conn);
        $conn->close();
        exit();
    }

    //Open file
    if($file === true) {
        $servername = $options['h'];
        $username = $options['u'];
        $dbname = "test";
        $filename = $options['file'];

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        parse_file($servername, $username, $dbname, $conn, $filename, $dry_run);

        $conn->close();
    }

    function parse_file($servername, $username, $dbname, $conn, $filename, $dry_run) {
        $file = fopen($filename, "r") or die("Unable to open file!");
        fgets($file);

        //Go through the csv and insert records
        while($line = fgets($file)) {
            //Skip if the line is empty
            if(trim($line) == ""){
                continue;
            }
            //Split the record into individual cells
            $line = explode(",", $line);

            //Parse and format the Name cell
            $line[0] = $conn->real_escape_string(trim(strtolower($line[0])));
            $line[0][0] = strtoupper($line[0][0]);
    
            //Parse and format the Surname cell
            $line[1] = $conn->real_escape_string(trim(strtolower($line[1])));
            $line[1][0] = strtoupper($line[1][0]);
    
            //Parse and format the email cell
            $line[2] = $conn->real_escape_string(trim(strtolower($line[2])));

            //If email is invalid skip the record and print an error
            if (filter_var($line[2], FILTER_VALIDATE_EMAIL) === false) {
                echo "Invalid email format on record: ". $line[0]." ". $line[1]." ". $line[2]. "\n";
                continue;
            }

            echo "To be inserted: ". $line[0]." ". $line[1]." ". $line[2]. "\n";
            
            if($dry_run === false) {
                insert_users($line[0], $line[1], $line[2], $conn);
            }
        }
    }

    function insert_users($line0, $line1, $line2, $conn) {
        //Insert
        $sql = "INSERT INTO users (`name`, surname, email)
        VALUES ('".$line0."', '".$line1."', '".$line2."')";

        //Error checking
        if ($conn->query($sql) === false) {
            print "Error Inserting\n";
        }
        else {
            print "Inserted\n";
        }
    }

    function create_table($conn) {
        // Create database
        $sql = " CREATE TABLE users (
            `name` VARCHAR(50),
            surname VARCHAR(50),
            email VARCHAR(50),
            UNIQUE (email)
            )";
        if ($conn->query($sql) === true) {
            echo "Table created successfully";
        }
        else {
            echo "Error creating table: " . $conn->error;
        }
    }

    function drop_table($conn) {
        // Drop table
        $sql = " DROP TABLE users";
        if ($conn->query($sql) === false) {
            echo "Failed dropping table: " . $conn->error;
        }
    }
?>

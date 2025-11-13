<?php
// Variables $varriable name; or $variablename=value;
$fname=$_POST['firstname'];
$lname=$_POST['lastname'];
$email=$_POST['email'];
$password=password_hash($_POST['password'],PASSWORD_DEFAULT);

// storing values from the html page $_POST['field id'];
// hasing password for dsafety pasword_hassh(password,hash_algorithm[PASSWORD_DEFAULT, PASSWORD_BCRYPT, etc]);

// connecting to the database
$$host = "127.0.0.1";
$user = "naima.aloula";
$pass = "mohamet25";
$db   = "attendancemanagement";

$con=new mysqli($host,$user,$pass,$db);
//needed valraiables (hostname,host_user,hostpassword,databasename)
if($con->connect_error){
    // error logic
    die("Connection falied");
}else{
    
//connection new mysqli(all the above variables)


// check connection use c->connect_error
    // ending the program if that is the case
    //OR
    //die("connection failed: ".$c->connect_error);


// Making SQL COMMANDS [getting th users table(SELECT * FROM users) / adding to the user table(INSERT INTO users (first_name, last_name, email, password_hash) VALUES ('$fname', '$lname', '$email', '$hashedpassword'))]
$stmt = $con->prepare("INSERT INTO users (first_name, last_name, email, password_hash) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $fname, $lname, $email, $password);

if ($stmt->execute()) {
    header('Location: ../view/login.html');
    exit();
} else {
    echo "Failed: " . $stmt->error;
}

//preventing SQL INJECTION: using c->prepare(command)
//getting the result -Query call $c->query($command);
// if you use select to see if there are results use $result->num_rows to see number of rows returned


// Redirect if insert is successful query retruns true or false 
if($c===TRUE){
    header('Location: ../view/login.html');
    exit();
}else{
    echo "Failed Retry";
}

// if html is rendered in php to check for a submit 
// $_SERVER['REQUEST_METHOD'] === 'POST'

// When using js and expecting a return value you echo a json instead of redirecting
//$state=["state"=>true];

//echo using json_encode(object to echo);

// ending the program
?>

<!-- require -->
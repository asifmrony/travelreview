<?php
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$username = $password = $fullname = $email = "";
$username_err = $password_err = $email_err = $fullname_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Validate fullname
    if(empty(trim($_POST["fullname"]))){
        $fullname_err = "Please enter your Name.";     
    } else{
        $fullname = trim($_POST["fullname"]);
    }    
 

    // Validate email
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter your Email.";     
    } else{
        $email = trim($_POST["email"]);
    }  
  

    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } else{ 
        // Prepare a select statement
        $sql = "SELECT username FROM signup WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = trim($_POST["username"]);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "This username is already taken.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        mysqli_stmt_close($stmt);
    }


    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have atleast 6 characters.";
    } else{
        $password = trim($_POST["password"]);
    }


     // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($email_err) && empty($fullname_err)){
        
        // Prepare an insert statement
        $sql = "INSERT INTO signup (fullname, email, username, password) VALUES (?, ?, ?, ?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssss",$param_fullname, $param_email, $param_username, $param_password);
            
            // Set parameters
            $param_fullname = $fullname;
            $param_email = $email;
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect to login page
                header("location: index.php");
            } else{
                echo "Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    // Close connection
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Sign Up - Travel Review</title>
    <link href="https://fonts.googleapis.com/css?family=Merriweather" rel="stylesheet"> 
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            font-family: 'Merriweather', serif; 
        }

        .container {
            width:50%;
            margin:0 auto;
            background-color: aquamarine;
            margin-top: 40px;
            border: 0.8px solid #aaa;
            border-radius: 2%;
        }

        .h1 {
            text-align: justify;
            margin: 20px;
        }

        .form-container {
            margin: 50px 0 0 50px;
        }

        .row {
            padding: 20px;
        }

        .s1 {
            margin-right: 30px;
        }
        .s2 {
            margin-right: 63px;
        }
        .s3 {
            margin-right: 22px;
        }
        .s4 {
            margin-right: 30px;
        }

        .form-control {
            width: 80%;
            height: 50px;
            border-radius: 10%;
        }

        .btn {
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .btn-default {
            background-color: red;
            color: #fff;
            padding: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Share your travel Experience with the World - Join with us</h1>
        <div class="form-container">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="row <?php echo (!empty($fullname_err)) ? 'has-error' : ''; ?>">

                    <span class="s1">full Name:</span><input type="text" class="form-control" id="fullname" name="fullname" value="<?php echo $fullname; ?>">

                    <span class="help-block"><?php echo $fullname_err; ?></span>
                </div>

                <div class="row <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                    <span class="s2">Email:</span><input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>">
                    <span class="help-block"><?php echo $email_err; ?></span>
                </div>

                <div class="row <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                    <span class="s3">Username:</span> <input type="text" class="form-control" id="username" name="username" value="<?php echo $username; ?>">
                    <span class="help-block"><?php echo $username_err; ?></span>
                </div>

                <div class="row <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                    <span class="s4">Password:</span> <input type="password" class="form-control" id="password" name="password" value="<?php echo $password; ?>">
                    <span class="help-block"><?php echo $password_err; ?></span>
                </div>
                <button type="submit" class="btn btn-default">Submit</button>
                <p>Already have an account? <a href="index.php">Login here</a>.</p>
            </form>
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php
        session_start();    
        require 'db.php';
        if(isset($_POST['submit'])){
            $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'];

            $sql = "SELECT * FROM users WHERE email=?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if(mysqli_num_rows($result) == 1){
                $row = mysqli_fetch_assoc($result);
                if(password_verify($password, $row['password'])){
                    // Password is correct, start a session
                    $_SESSION['email'] = $email;
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['username'] = $row['username'];
                    echo "Login successful! Welcome, " . htmlspecialchars($row['username']) . ".";
                    header("Location: tasks.php");
                    exit();
                } else {
                    echo "Invalid password.";
                }
            } else {
                echo "No user found with that email.";
            }
        }
        mysqli_close($conn);
    ?>      

    <form action="" method="post">
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <input type="submit" name="submit" value="Login"> <input type="submit" value="Register" onclick="window.location.href='register.php'"><br>
    </form>
</body>
</html>




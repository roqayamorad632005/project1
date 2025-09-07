<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php
    require 'db.php';
    if (!$conn) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $username = trim(filter_input(INPUT_POST, "username", FILTER_DEFAULT));
        $email    = trim(filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL));
        $password = $_POST['password'];

        // Check if username or email already exists
        $checkQuery = "SELECT id FROM users WHERE username=? OR email=?";
        $stmt = mysqli_prepare($conn, $checkQuery);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ss", $username, $email);
            mysqli_stmt_execute($stmt);
            $checkResult = mysqli_stmt_get_result($stmt);

            if ($checkResult === false) {
                echo "Error: " . mysqli_error($conn);
            } elseif (mysqli_num_rows($checkResult) > 0) {
                echo "Username or Email already exists.";
            } else {
                // Hash the password before storing it
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
                $stmtInsert = mysqli_prepare($conn, $sql);
                if ($stmtInsert) {
                    mysqli_stmt_bind_param($stmtInsert, "sss", $username, $email, $hashedPassword);
                    if (mysqli_stmt_execute($stmtInsert)) {
                        echo "<p style='color:green;'>Registration successful!";
                    } else {
                        echo "Error: " . mysqli_error($conn);
                    }
                    mysqli_stmt_close($stmtInsert);
                } else {
                    echo "Error preparing insert statement: " . mysqli_error($conn);
                }
            }
            mysqli_stmt_close($stmt);
        } else {
            echo "Error preparing select statement: " . mysqli_error($conn);
        }
    }

    mysqli_close($conn);
    ?>

    <form action="" method="post">
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <input type="submit" name="submit" value="Register"> <input type="submit" value="login" onclick="window.location.href='login.php'"><br>
    </form>
</body>
</html>
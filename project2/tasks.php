<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tasks</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php
        session_start();
        require 'db.php';       
        if (!$conn) {
            die("Database connection failed: " . mysqli_connect_error());
        } 

        if (!isset($_SESSION['email'])) {
            header("Location: login.php");
            exit();
        }
        $userId = $_SESSION['user_id'];

        // add task
        if (isset($_POST['add'])) {
            $task = trim(filter_input(INPUT_POST, 'task', FILTER_SANITIZE_SPECIAL_CHARS));
            $sql = "INSERT INTO tasks (title, status, user_id) VALUES (?, 'pending', ?)";
            $stmt = mysqli_prepare($conn, $sql);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "si", $task, $userId);
                if (mysqli_stmt_execute($stmt)) {
                    $_SESSION['message'] = "Task added successfully.";
                } else {
                    $_SESSION['message'] = "An error occurred while adding the task.";
                }
                mysqli_stmt_close($stmt);
            }
        }
        
        // delete task
        if (isset($_POST['delete'])) {
            $task = intval($_POST['delete']);
            $sql = "DELETE FROM tasks WHERE id=? AND user_id=?";
            $stmt = mysqli_prepare($conn, $sql);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ii", $task, $userId);
                mysqli_stmt_execute($stmt);
                $_SESSION['message'] = mysqli_stmt_affected_rows($stmt) > 0 
                    ? "Task deleted successfully." 
                    : "Task not found or you do not have permission.";
                header("Location: tasks.php");
                exit();
                mysqli_stmt_close($stmt);
            }
        }

        // update task status
        if (isset($_POST['done'])) {
            $task = intval($_POST['done']);
            $sql = "UPDATE tasks SET status = 'done' WHERE id = ? AND user_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ii", $task, $userId);
                mysqli_stmt_execute($stmt);
                $_SESSION['message'] = mysqli_stmt_affected_rows($stmt) > 0 
                    ? "Task marked as done." 
                    : "Task not found or you do not have permission.";
                header("Location: tasks.php");
                exit();
                mysqli_stmt_close($stmt);
            }
        }

        // get tasks
        $sql = "SELECT * FROM tasks WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $userId);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            mysqli_stmt_close($stmt);
        } else {
            $result = false;
        }
    ?>

    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>

    <!-- Add Task -->
    <form method="post">
        <input type="text" name="task" placeholder="Add a new task" required>
        <input type="submit" name="add" value="Add Task">
    </form>

    <div class="nav">
        <a href="logout.php">Logout</a>
    </div>

    <?php
    if (isset($_SESSION['message'])) {
        echo "<p class='message'>" . htmlspecialchars($_SESSION['message']) . "</p>";
        unset($_SESSION['message']);
    }

    if ($result && mysqli_num_rows($result) > 0) {
        echo "<div class='task-list'>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<div class="task '.($row['status'] === 'done' ? 'done' : '').'">';
            echo "<span>".htmlspecialchars($row['title'])."</span>";
            echo " <small>(" . htmlspecialchars($row['status']) . ")</small>";
            
            if ($row['status'] !== 'done') {
                echo '<form method="post">
                        <input type="hidden" name="done" value="'.$row['id'].'">
                        <input type="submit" value="Mark as Done">
                      </form>';
            }

            echo '<form method="post">
                    <input type="hidden" name="delete" value="'.$row['id'].'">
                    <input type="submit" value="Delete">
                  </form>';

            echo '</div>';
        }
        echo "</div>";
    } else {
        echo "<p>No tasks available.</p>";
    }

    mysqli_close($conn);
    ?>
</body>
</html>

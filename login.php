<?php
session_start();
require '../pos/db.php'; // adjust path since db.php is in your POS folder

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($id, $dbUser, $dbPass, $role);
    
    if ($stmt->fetch()) {
        if (password_verify($password, $dbPass)) {
            if ($role !== 'customer') {
                $_SESSION['flash'] = "Only customers can log in here!";
                header("Location: login.php");
                exit;
            }

            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $dbUser;
            $_SESSION['role'] = $role;

            header("Location: index.html"); // customer home page
            exit;
        }
    }
    $_SESSION['flash'] = "Invalid login!";
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head><title>Customer Login</title></head>
<body>
<h2>Customer Login</h2>
<?php if (!empty($error)) echo "<p style='color:red'>$error</p>"; ?>
<form method="POST">
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit">Login</button>
</form>
</body>
</html>

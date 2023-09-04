<?php
$host = 'localhost';
$db   = 'social_network';
$user = 'root';  // Your database username
$pass = 'root';      // Your database password
$charset = 'utf8mb4';

$pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass);

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);

    if ($user = $stmt->fetch()) {
        if (password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            header('Location: profile.php');
            exit;
        } else {
            $message = "Invalid password.";
        }
    } else {
        $message = "User not found.";
    }
}

?>
<form method="post">
    Username: <input type="text" name="username" required>
    Password: <input type="password" name="password" required>
    <input type="submit" value="Login">
</form>
<?php echo $message; ?>

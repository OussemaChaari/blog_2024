<?php
include '../connect.php';
session_start();
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = sha1($_POST['pass']);
    // Check if the email and password match a record in the database
    $checkUser = "SELECT * FROM users WHERE email = :email AND password = :password";
    $stmt = $conn->prepare($checkUser);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':password', $password, PDO::PARAM_STR);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        // Login successful
        $_SESSION['user_email'] = $email; 
        header('Location: dashboard.php'); // Redirect to the dashboard
        exit();
    } else {
        $messageError[] = 'Invalid email or password. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include('../includes/include.php'); ?>
    <title>User login</title>
</head>

<body>
    <?php include('../message/message.php'); ?>
    <div class="bg-light d-flex flex-column min-vh-100" style="height: 100vh;">
        <h1 class="text-center py-4 text-secondary"><i class="fas fa-home"></i> User Dashboard</h1>
        <form action="" method="POST" class="mx-auto p-4 border rounded shadow" style="width: 400px;">
            <h3 class="text-center">login</h3>
            <div class="form-group">
                <input type="email" name="email" class="form-control" placeholder="Enter email" id="email" required>
            </div>
            <div class="form-group">
                <input type="password" name="pass" class="form-control" placeholder="Enter password" id="pass" required>
            </div>
            <div class="row">
                <div class="col text-right d-flex flex-column">
                    <button type="submit" name="login" class="btn btn-primary">Login</button>
                    <div class="d-flex mt-2">
                        <p>You D'ont have account</p><a type="submit" class="ml-2" name="register" href="register.php">Register</a>
                    </div>
                </div>
            </div>
        </form>
        <?php include('../admin/footer.php'); ?>
    </div>
</body>

</html>
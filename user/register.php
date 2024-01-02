<?php
include '../connect.php';
if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = sha1($_POST['pass']);
    $checkEmail = "SELECT * FROM users WHERE email = :email";
    $stmt = $conn->prepare($checkEmail);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        $messageError[] = 'Email already registered. Please use a different email address.';
    } else {
        $insertUser = "INSERT INTO users (name, email, password) VALUES (:name, :email, :password)";
        $stmt = $conn->prepare($insertUser);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);
        if ($stmt->execute()) {
            $messageSuccess[] = 'Registration successful. You can now login.';
        } else {
            $messageError[] = 'Error registering user. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include('../includes/include.php'); ?>
    <title>User Register</title>
</head>

<body>
    <?php include('../message/message.php'); ?>
    <div class="bg-light d-flex flex-column min-vh-100" style="height: 100vh;">
        <form action="" method="POST" class="mx-auto p-4 border rounded shadow" style="width: 400px;margin-top:5rem;">
            <h3 class="text-center">Register</h3>
            <div class="form-group">
                <input type="text" name="name" class="form-control" placeholder="Enter username" id="name" required>
            </div>
            <div class="form-group">
                <input type="email" name="email" class="form-control" placeholder="Enter email" id="email" required>
            </div>
            <div class="form-group">
                <input type="password" name="pass" class="form-control" placeholder="Enter password" id="pass" required>
            </div>
            <div class="row">
                <div class="col text-right d-flex flex-column">
                    <button type="submit" name="register" class="btn btn-success">Register</button>
                    <div class="d-flex mt-2">
                        <p>You have account</p><a type="submit" class="ml-2" name="login" class="btn btn-primary"
                            href="index.php">login</a>
                    </div>
                </div>
            </div>
        </form>
        <?php include('../admin/footer.php'); ?>
    </div>
</body>

</html>
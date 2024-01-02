<?php
include '../connect.php';
session_start();
if (isset($_POST['login'])) {
    $name = $_POST['name'];
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    $pass = sha1($_POST['pass']);
    $pass = filter_var($pass, FILTER_SANITIZE_STRING);
    $select_admin = $conn->prepare("SELECT * FROM `admin` WHERE name = ? AND password = ?");
    $select_admin->execute([$name, $pass]);
    if ($select_admin->rowCount() > 0) {
        $fetch_admin_id = $select_admin->fetch(PDO::FETCH_ASSOC);
        $_SESSION['admin_id'] = $fetch_admin_id['id'];
        header('location:dashboard.php');
    } else {
        $messageError[] = 'Incorrect Username Or Password!';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include('../includes/include.php'); ?>
    <title>Admin Blog 2024</title>
</head>

<body>
    <?php include('../message/message.php'); ?>
    <div class="bg-light d-flex flex-column min-vh-100" style="height: 100vh;">
        <h1 class="text-center py-4 text-secondary"><i class="fas fa-home"></i> Admin Dashboard</h1>
        <form action="" method="POST" class="mx-auto p-4 border rounded shadow" style="width: 400px;">
            <h3 class="text-center">login now</h3>
            <p class="text-success">default username = <span>oussema</span> & password = <span>oussema</span></p>
            <div class="form-group">
                <input type="text" name="name" class="form-control" placeholder="Enter username" id="name" required>
            </div>
            <div class="form-group">
                <input type="password" name="pass" class="form-control" placeholder="Enter password" id="pass" required>
            </div>
            <div class="row">
                <div class="col text-right">
                    <button type="submit" name="login" class="btn btn-primary">Login Now</button>
                </div>
            </div>
        </form>
        <?php include('footer.php'); ?>
    </div>
</body>
</html>
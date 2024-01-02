<?php
include '../connect.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include('../includes/include.php'); ?>
    <title>User</title>
</head>

<body>
    <?php include('message/message.php'); ?>
    <div class="bg-light d-flex flex-column min-vh-100" style="height: 100vh;">
        <h1 class="text-center py-4 text-secondary"><i class="fas fa-home"></i> User Dashboard</h1>
        <form action="" method="POST" class="mx-auto p-4 border rounded shadow" style="width: 400px;">
            <h3 class="text-center">login</h3>
            <div class="form-group">
                <input type="text" name="name" class="form-control" placeholder="Enter username" id="name" required>
            </div>
            <div class="form-group">
                <input type="password" name="pass" class="form-control" placeholder="Enter password" id="pass" required>
            </div>
            <div class="row">
                <div class="col text-right">
                    <button type="submit" name="login" class="btn btn-primary">Login</button>
                    <button type="submit" name="register" class="btn btn-success">Register</button>
                </div>
            </div>
        </form>
        <?php include('../admin/footer.php'); ?>
    </div>
</body>
</html>
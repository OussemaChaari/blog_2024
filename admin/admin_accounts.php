<?php
include '../connect.php';
session_start();
$admin_id = $_SESSION['admin_id'];

if (isset($_POST['change'])) {
    $sql = "SELECT * FROM admin WHERE id = :admin_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    $ancienPassword = $_POST['ancien_pass'];
    $newPassword = sha1($_POST['new_pass']);
    if (sha1($ancienPassword) === $admin['password']) {
        $sql = "UPDATE admin SET name = :name, password = :password WHERE id = :admin_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':name', $_POST['name'], PDO::PARAM_STR);
        $stmt->bindParam(':password', $newPassword, PDO::PARAM_STR);
        $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            $messageSuccess[] = 'Informations mises à jour avec succès.';
        } else {
            $messageError[] = 'Erreur lors de la mise à jour des informations.';
        }
    } else {
        $messageError[] = 'L\'ancien mot de passe ne correspond pas.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include('../includes/include.php'); ?>
    <title>Account</title>
</head>

<body>
    <?php include('sidebar.php'); ?>
    <?php include('../message/message.php'); ?>
    <section style="width:500px;">
        <h4 class="heading">Changement Admin Information</h4>
        <form action="" method="post">
            <div class="form-group">
                <input type="text" name="name" class="form-control" value="<?= $admin_name; ?>" id="name" required>
            </div>
            <div class="form-group">
                <input type="password" name="ancien_pass" class="form-control" placeholder="Enter ancien password" id="pass" required>
            </div>
            <div class="form-group">
                <input type="password" name="new_pass" class="form-control" placeholder="Enter new password" id="pass" required>
            </div>
            <div class="row">
                <div class="col text-right">
                    <button type="submit" name="change" class="btn btn-primary">Change Info</button>
                </div>
            </div>
        </form>
    </section>

</body>

</html>
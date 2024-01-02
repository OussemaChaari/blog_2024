<?php
include '../connect.php';
session_start();
function getCount($requette,$status = null)
{
    global $conn; 
    $statusCondition = '';
    if ($status !== null) {
        $statusCondition = "WHERE status = :status";
    }
    $sql = "SELECT COUNT(*) as table_count FROM $requette $statusCondition";
    $stmt = $conn->prepare($sql);
    if ($status !== null) {
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
    }
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['table_count'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include('../includes/include.php'); ?>
    <title>Dashboard</title>
</head>

<body>

    <?php include('sidebar.php'); ?>
    <div class="container mt-2" style="width:700px;">
        <h4 class="heading">Statistiques</h4>
        <div class="row">
            <div class="col-md-4 mt-2">
                <div class="card bg-secondary text-white">
                    <div class="card-body">
                        Posts: <?= getCount('posts'); ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mt-2">
                <div class="card bg-secondary text-white">
                    <div class="card-body">
                        Posts Published: <?= getCount('posts','published'); ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mt-2">
                <div class="card bg-secondary text-white">
                    <div class="card-body">
                        Posts Draft: <?= getCount('posts','draft'); ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mt-2">
                <div class="card bg-secondary text-white">
                    <div class="card-body">
                         Users: <?= getCount('users'); ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mt-2">
                <div class="card bg-secondary text-white">
                    <div class="card-body">
                        Commentaires: <?= getCount('comments'); ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mt-2">
                <div class="card bg-secondary text-white">
                    <div class="card-body">
                   
                        Categories: <?= getCount('categories'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
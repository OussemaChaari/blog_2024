<?php
include '../connect.php';
session_start();
if(isset($_POST['add_category'])){
    $category = $_POST['category'];
    $category = filter_var($category, FILTER_SANITIZE_STRING);
    if(isset($category)){
      $insert_category = $conn->prepare("INSERT INTO `categories`(name) VALUES(?)");
      $insert_category->execute([$category]);
      $messageSuccess[] = 'Category Added Successfuly!';
    }else{
        $messageError[] = 'Failed To Add Category!';
    }

 }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include('../includes/include.php'); ?>
    <title>Add Category</title>
</head>

<body>
    <?php include('sidebar.php'); ?>
    <?php include('../message/message.php'); ?>
    <section style="width:500px;">
        <h4 class="heading">Add new category</h4>
        <form action="" method="post">
            <div class="form-group">
                <input type="text" name="category" class="form-control" placeholder="add category title" id="category" required>
            </div>
            <div class="row">
                <div class="col text-right">
                    <button type="submit" name="add_category" class="btn btn-primary">Insert Category</button>
                </div>
            </div>
        </form>
    </section>

</body>

</html>